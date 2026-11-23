<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Borehole;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WaterQualityTest;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_replace(require config_path('rwims.php'), ['current_password' => 'password'], $overrides);
    }

    public function test_settings_require_admin_and_password_confirmation_and_exclude_deployment_secrets(): void
    {
        $this->get(route('settings.edit'))->assertRedirect(route('login'));
        foreach (['operator', 'supervisor', 'finance', 'stores', 'viewer'] as $role) {
            $this->actingAs(User::factory()->create(compact('role')))->get(route('settings.edit'))->assertForbidden();
            $this->put(route('settings.update'), $this->payload())->assertForbidden();
        }
        $this->actingAs(User::factory()->admin()->create())->get(route('settings.edit'))->assertOk()
            ->assertSee('System settings')->assertDontSee('MAIL_PASSWORD')->assertDontSee('DB_PASSWORD');
        $this->put(route('settings.update'), $this->payload(['current_password' => 'wrong']))
            ->assertSessionHasErrors('current_password');
        $this->assertDatabaseCount('system_settings', 0);
        $this->put(route('settings.update'), $this->payload(['MAIL_PASSWORD' => 'do-not-store-this', 'APP_KEY' => 'do-not-store-this']))
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertStringNotContainsString('do-not-store-this', SystemSetting::firstOrFail()->toJson());
        $this->assertStringNotContainsString('current_password', AuditLog::where('action', 'settings_updated')->firstOrFail()->toJson());
    }

    public function test_invalid_settings_cannot_relax_safety_limits_or_create_inconsistent_policy(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
        $this->actingAs(User::factory()->admin()->create());
        foreach ([
            ['idle_timeout', 0], ['idle_timeout', 121], ['default_page_size', 10000], ['organization_name', ''],
            ['support_email', 'not-email'], ['capture_cost_limit', -1], ['capture_cost_limit', 100000.01],
            ['ph_min', 6], ['ph_max', 9], ['tds_max', 601], ['tds_max', 1.5], ['turbidity_max', 5.01],
        ] as [$field, $value]) {
            $this->putJson(route('settings.update'), $this->payload([$field => $value]))
                ->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $this->putJson(route('settings.update'), $this->payload(['ph_min' => 8, 'ph_max' => 7]))
            ->assertUnprocessable()->assertJsonValidationErrors('ph_max');
        $this->assertDatabaseCount('system_settings', 0);
    }

    public function test_saved_settings_apply_to_branding_pagination_costs_quality_reports_and_idle_timeout(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        foreach (range(1, 12) as $index) {
            Borehole::create([
                'borehole_serial_no' => 'CFG-' . str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                ...$this->location(true), 'locality' => 'UAT village', 'date' => today(),
                'ph' => 7, 'tds_mg_l' => 500, 'turbidity_ntu' => 4, 'total_cost' => 4000,
            ]);
        }
        $service = app(ReportService::class);
        $this->assertEquals(100, $service->getKeyAchievements()['water_quality']);
        $this->put(route('settings.update'), $this->payload([
            'organization_name' => 'RIDA UAT Configured', 'support_email' => 'help@rida.gov.zw',
            'idle_timeout' => 5, 'default_page_size' => 10, 'capture_cost_limit' => 3000,
            'tds_max' => 400, 'turbidity_max' => 3, 'ph_min' => 6.8, 'ph_max' => 8.2,
        ]))->assertRedirect(route('settings.edit'))->assertSessionHasNoErrors();
        $this->get(route('boreholes.index'))->assertOk()->assertSee('RIDA UAT Configured')
            ->assertViewHas('boreholes', fn ($rows) => $rows->perPage() === 10 && $rows->total() === 12);
        $this->get(route('boreholes.index', ['per_page' => 25]))
            ->assertViewHas('boreholes', fn ($rows) => $rows->perPage() === 25);
        $this->getJson(route('boreholes.index', ['per_page' => 10000]))->assertUnprocessable();
        $this->get(route('water-points.index'))->assertViewHas('waterPoints', fn ($rows) => $rows->perPage() === 10);
        $this->get(route('boreholes.create'))->assertOk()->assertSee('max="3000"', false);
        $this->postJson(route('boreholes.store'), [
            'borehole_serial_no' => 'CFG-INVALID-COST', ...$this->location(true), 'locality' => 'UAT village',
            'date' => today()->toDateString(), 'total_cost' => 3000.01, 'cost_currency' => 'USD',
        ])->assertUnprocessable()->assertJsonValidationErrors('total_cost');
        foreach ([
            ['consumables.inventory.store', ['unit_cost' => 3000.01], 'unit_cost'],
            ['invoices.store', ['tax_amount' => 3000.01, 'items' => [['description' => 'Policy check', 'quantity' => 1, 'unit_price' => 3000.01]]], 'items.0.unit_price'],
            ['piped-water-schemes.store', ['fund_amount' => 3000.01], 'fund_amount'],
            ['irrigation-schemes.store', ['fund_amount_available' => 3000.01], 'fund_amount_available'],
        ] as [$route, $data, $field]) {
            $this->postJson(route($route), $data)->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $dam = \App\Models\Dam::create([
            'name' => 'Settings policy dam', ...$this->location(), 'village' => 'UAT village',
            'wall_condition' => 'good', 'spillway_condition' => 'good', 'created_by' => $admin->id,
        ]);
        $this->postJson(route('dams.maintenance.store', $dam), ['estimated_cost' => 3000.01, 'actual_cost' => 3000.01])
            ->assertUnprocessable()->assertJsonValidationErrors(['estimated_cost', 'actual_cost']);
        $this->assertEquals(0, $service->getKeyAchievements()['water_quality']);
        $this->assertEquals(0, $service->getWaterQualityAnalysis()['tds_compliance']);
        $quality = new WaterQualityTest(['ph' => 7, 'tds_mg_l' => 450, 'turbidity_ntu' => 2, 'ecoli_cfu_100ml' => 0]);
        $this->assertSame('Needs Attention', $quality->overall_quality);
        $this->get(route('reports.executive.dashboard'))->assertOk()->assertSee('TDS ≤400 mg/L');
        $this->assertDatabaseHas('audit_logs', ['user_id' => $admin->id, 'action' => 'settings_updated']);
        $this->assertEquals(4000, Borehole::firstOrFail()->total_cost);
        $this->withSession(['last_activity_at' => time() - 301])->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertSee('RIDA UAT Configured')->assertSee('mailto:help@rida.gov.zw', false);
    }

    public function test_tighter_quality_limits_reclassify_each_parameter_without_rewriting_readings(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->put(route('settings.update'), $this->payload([
            'ph_min' => 7, 'ph_max' => 8, 'tds_max' => 400, 'turbidity_max' => 3,
        ]))->assertRedirect()->assertSessionHasNoErrors();
        $this->get(route('settings.edit'))->assertOk();
        foreach ([
            ['ph' => 6.9], ['ph' => 8.1], ['tds_mg_l' => 401], ['turbidity_ntu' => 3.01],
        ] as $reading) {
            $test = new WaterQualityTest(array_replace([
                'ph' => 7.5, 'tds_mg_l' => 400, 'turbidity_ntu' => 3, 'ecoli_cfu_100ml' => 0,
            ], $reading));
            $this->assertSame('Needs Attention', $test->overall_quality);
        }
        $test = new WaterQualityTest(['ph' => 7, 'tds_mg_l' => 400, 'turbidity_ntu' => 3, 'ecoli_cfu_100ml' => 0]);
        $this->assertSame('Meets Recorded Limits', $test->overall_quality);
    }
}
