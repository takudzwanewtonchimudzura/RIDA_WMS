<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Borehole;
use App\Models\ConsumableCategory;
use App\Models\Dam;
use App\Models\DamMaintenance;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\ProspectingSite;
use App\Models\User;
use App\Models\WaterPoint;
use App\Services\ReportService;
use App\Services\UatDataRepair;
use App\Services\ZimbabweLocations;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoUserSeeder;
use Database\Seeders\UatScenarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UatReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Storage::fake('local');
    }

    private function borehole(array $values = []): Borehole
    {
        return Borehole::create(array_replace([
            'borehole_serial_no' => 'UAT-BH-' . bin2hex(random_bytes(4)),
            ...$this->location(true),
            'locality' => 'UAT village',
            'date' => today(),
        ], $values));
    }

    public function test_lists_dashboard_and_reports_use_the_same_depth_and_yield_including_zero(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->borehole(['borehole_serial_no' => 'BH-0014', 'final_borehole_depth_m' => 85, 'yield_lps' => 0.6]);
        $zero = $this->borehole(['borehole_serial_no' => 'BH-0001', 'final_borehole_depth' => 70, 'blowing_yield' => 0, 'yield_lps' => 9]);

        $this->get(route('boreholes.index'))->assertOk()->assertSeeInOrder(['BH-0001', 'BH-0014'])
            ->assertSee('85.00 m')->assertSee('0.60 l/s')->assertSee('0.00 l/s');
        $this->get(route('dashboard'))->assertViewHas('stats', fn ($stats) => $stats['borehole_data_completeness'] === 100.0)
            ->assertViewHas('metrics', fn ($metrics) => $metrics['borehole_data_completeness'] === 100.0);
        $this->get(route('reports.executive.dashboard'))->assertOk()->assertSee('85.0')->assertSee('0.60')
            ->assertDontSee('12% vs last period')->assertDontSee('5% improvement')
            ->assertDontSee('8% increase')->assertDontSee('15% vs budget')->assertDontSee('75,000');
        $this->assertSame(0.0, $zero->recorded_yield);
        $this->assertEquals(0.6, app(ReportService::class)->getAverageYield());
    }

    public function test_quality_limits_and_missing_readings_are_consistent_and_cached_data_is_invalidated(): void
    {
        $pass = ['ph' => 6.5, 'tds_mg_l' => 600, 'turbidity_ntu' => 5];
        $this->borehole($pass);
        $failing = $this->borehole(array_replace($pass, ['turbidity_ntu' => 10]));
        $this->borehole(array_replace($pass, ['turbidity_ntu' => null]));
        $this->borehole(array_replace($pass, ['ph' => 9]));
        $service = app(ReportService::class);
        $this->assertEquals(25, $service->getKeyAchievements()['water_quality']);
        $this->assertSame(3, $service->getKeyAchievements()['quality_tested']);
        $this->assertEquals(25, $service->getWaterQualityAnalysis()['compliance_rate']);
        $failing->update(['turbidity_ntu' => 2]);
        $this->assertEquals(50, $service->getKeyAchievements()['water_quality']);
        $this->assertSame([], $service->getRecommendations());
    }

    public function test_borehole_cost_limits_reject_unrealistic_negative_and_non_usd_amounts(): void
    {
        $this->actingAs(User::factory()->operator()->create());
        $payload = ['borehole_serial_no' => 'COST-CHECK', ...$this->location(true), 'locality' => 'UAT village', 'date' => today()->toDateString(), 'cost_currency' => 'USD'];
        foreach ([-1, 100000.01, 19700000] as $cost) {
            $this->postJson(route('boreholes.store'), $payload + ['total_cost' => $cost])->assertUnprocessable()->assertJsonValidationErrors('total_cost');
        }
        $this->postJson(route('boreholes.store'), array_replace($payload, ['total_cost' => 1000, 'cost_currency' => 'ZAR']))
            ->assertUnprocessable()->assertJsonValidationErrors('cost_currency');
        $this->postJson(route('boreholes.store'), $payload + ['total_cost' => 100000])->assertCreated();
        $this->borehole(['total_cost' => 5000, 'cost_currency' => 'ZAR']);
        $this->assertEquals(100000, app(ReportService::class)->getTotalInvestment());
    }

    public function test_invalid_countries_districts_provinces_and_wards_are_rejected_on_create_and_update(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $payload = ['name' => 'Location check dam', 'village' => 'UAT village', 'wall_condition' => 'good', 'spillway_condition' => 'good', ...$this->location()];
        foreach ([
            ['province' => 'Manicaland', 'district' => 'Chimanimani', 'latitude' => -19.8, 'longitude' => 25.53],
            ['province' => 'Manicaland', 'district' => 'Chipinge', 'latitude' => -15.88, 'longitude' => 32.5],
            ['province' => 'Masvingo', 'district' => 'Chombo'],
            ['ward' => 'Ward 999'],
            ['latitude' => -30],
            ['longitude' => null],
        ] as $badLocation) {
            $this->postJson(route('dams.store'), array_replace($payload, $badLocation))->assertUnprocessable();
        }
        $this->postJson(route('dams.store'), $payload)->assertCreated();
        $dam = Dam::where('name', 'Location check dam')->firstOrFail();
        $this->assertNull($dam->approved_at);
        $this->putJson(route('dams.update', $dam), array_replace($payload, ['province' => 'Masvingo', 'district' => 'Chombo']))
            ->assertUnprocessable()->assertJsonValidationErrors('district');
        $this->assertNotEmpty($dam->dam_code);
        $this->get(route('dams.index'))->assertSee($dam->dam_code)->assertSee('Provinces Represented');
    }

    public function test_prospecting_requires_independent_approval_and_chronological_drilling_history(): void
    {
        $this->travelTo(now()->startOfSecond());
        $creator = User::factory()->admin()->create();
        $reviewer = User::factory()->create(['role' => 'supervisor']);
        $this->actingAs($creator)->postJson(route('prospecting.store'), [
            ...$this->location(), 'village' => 'UAT survey village', 'survey_date' => today()->toDateString(),
        ])->assertRedirect();
        $site = ProspectingSite::firstOrFail();
        $this->assertNotNull($site->submitted_at);
        $this->patchJson(route('prospecting.decide', $site), ['status' => 'approved'])->assertForbidden();
        $this->patchJson(route('prospecting.mark-drilled', $site), ['drilled_at' => now()->toDateTimeString()])->assertUnprocessable();

        $this->actingAs($reviewer)->patch(route('prospecting.decide', $site), ['status' => 'approved'])->assertRedirect();
        $site->refresh();
        $this->assertSame($reviewer->id, $site->reviewed_by);
        $this->patchJson(route('prospecting.decide', $site), ['status' => 'rejected'])->assertUnprocessable();
        $this->actingAs($creator)->patchJson(route('prospecting.mark-drilled', $site), [])->assertUnprocessable()->assertJsonValidationErrors('drilled_at');
        foreach ([now()->subDay(), now()->addDay()] as $date) {
            $this->patchJson(route('prospecting.mark-drilled', $site), ['drilled_at' => $date->toDateTimeString()])->assertUnprocessable();
        }
        $this->travel(1)->minutes();
        $this->patch(route('prospecting.mark-drilled', $site), ['drilled_at' => now()->toDateTimeString()])->assertRedirect();
        $site->refresh();
        $this->assertSame('drilled', $site->status);
        $this->assertSame($creator->id, $site->drilled_by);
        $this->assertTrue($site->drilled_at->gte($site->reviewed_at));
        $this->assertDatabaseHas('audit_logs', ['record_id' => $site->id, 'record_type' => ProspectingSite::class, 'action' => 'drilled']);
        $this->patchJson(route('prospecting.mark-drilled', $site), ['drilled_at' => now()->toDateTimeString()])->assertUnprocessable();
    }

    public function test_invalid_existing_survey_can_be_corrected_but_cannot_be_approved_as_is(): void
    {
        $creator = User::factory()->operator()->create();
        $supervisor = User::factory()->create(['role' => 'supervisor']);
        $site = ProspectingSite::create([
            'reference' => 'BAD-LOCATION', 'village' => 'Village', 'ward' => '1',
            'province' => 'Masvingo', 'district' => 'Chombo', 'survey_date' => today(),
            'created_by' => $creator->id,
        ]);
        $this->actingAs($supervisor)->patchJson(route('prospecting.decide', $site), ['status' => 'approved'])->assertUnprocessable();
        $this->actingAs($creator)->get(route('prospecting.edit', $site))->assertOk()->assertSee('Chombo');
        $this->put(route('prospecting.update', $site), [
            ...$this->location(), 'village' => 'Correct village', 'survey_date' => today()->toDateString(),
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->actingAs($supervisor)->patch(route('prospecting.decide', $site), ['status' => 'approved'])->assertRedirect()->assertSessionHasNoErrors();
    }

    public function test_supervisors_cannot_approve_their_own_assets_and_finance_and_stores_have_separate_access(): void
    {
        $supervisor = User::factory()->create(['role' => 'supervisor']);
        $operator = User::factory()->operator()->create();
        $own = $this->borehole(['created_by' => $supervisor->id]);
        $other = $this->borehole(['created_by' => $operator->id]);
        $this->actingAs($supervisor)->patch(route('boreholes.approve', $own))->assertForbidden();
        $this->patch(route('boreholes.approve', $other))->assertRedirect();
        $this->assertSame($supervisor->id, $other->fresh()->approved_by);

        $finance = User::factory()->create(['role' => 'finance']);
        $this->actingAs($finance)->get(route('clients.index'))->assertOk();
        $this->get(route('invoices.create'))->assertOk();
        $this->get(route('boreholes.create'))->assertForbidden();
        $this->get(route('consumables.inventory.create'))->assertForbidden();
        $this->patch(route('boreholes.approve', $other))->assertForbidden();

        $stores = User::factory()->create(['role' => 'stores']);
        $this->actingAs($stores)->get(route('consumables.inventory.create'))->assertOk();
        $this->get(route('consumables.categories.index'))->assertOk();
        $this->get(route('consumables.suppliers.index'))->assertOk();
        $category = ConsumableCategory::create(['name' => 'Role check']);
        $this->post(route('consumables.inventory.store'), [
            'name' => 'Pump seal', 'code' => 'ROLE-SEAL', 'category_id' => $category->id,
            'unit' => 'pieces', 'unit_cost' => 12, 'minimum_stock' => 5, 'initial_quantity' => 25,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->get(route('invoices.index'))->assertForbidden();
        $this->get(route('users.index'))->assertForbidden();
        $this->get(route('dams.create'))->assertForbidden();
    }

    public function test_login_and_activity_track_real_access_without_misclassifying_old_logins(): void
    {
        $this->travelTo(now()->startOfSecond());
        $admin = User::factory()->admin()->create(['password' => Hash::make('UatPassword123!')]);
        $old = User::factory()->viewer()->create(['last_login_at' => now()->subMonths(5)]);
        $this->post(route('login'), ['email' => $admin->email, 'password' => 'UatPassword123!'])->assertRedirect();
        $this->assertTrue($admin->fresh()->last_login_at->isToday());
        $this->get(route('users.activity'))->assertOk()
            ->assertViewHas('stats', fn ($stats) => $stats['active_today'] === 1 && $stats['active_week'] === 1)
            ->assertSee('Inactive');
        $this->assertTrue($old->fresh()->last_login_at->lt(now()->subMonths(4)));
        $this->assertTrue($admin->fresh()->last_seen_at->isToday());
    }

    public function test_deleting_accounts_and_assets_is_soft_and_audited_without_losing_creator_history(): void
    {
        $admin = User::factory()->admin()->create();
        $creator = User::factory()->operator()->create();
        $borehole = $this->borehole(['created_by' => $creator->id]);
        $this->actingAs($admin)->delete(route('users.destroy', $creator))->assertRedirect();
        $this->assertSoftDeleted('users', ['id' => $creator->id]);
        $this->assertSame($creator->name, $borehole->fresh()->creator->name);
        $this->assertDatabaseHas('audit_logs', ['user_id' => $admin->id, 'record_type' => User::class, 'record_id' => $creator->id, 'action' => 'deleted']);
        $this->delete(route('boreholes.destroy', $borehole))->assertRedirect();
        $this->assertSoftDeleted('boreholes', ['id' => $borehole->id]);
        $this->assertDatabaseHas('audit_logs', ['user_id' => $admin->id, 'record_type' => Borehole::class, 'record_id' => $borehole->id, 'action' => 'deleted']);
    }

    public function test_water_points_link_boreholes_once_validate_location_and_export_filtered_owned_data(): void
    {
        $owner = User::factory()->operator()->create();
        $borehole = $this->borehole(['created_by' => $owner->id, 'borehole_serial_no' => 'LINKED-BH']);
        $this->actingAs($owner);
        $payload = ['name' => 'Linked service point', 'village_name' => 'UAT village', ...$this->location(), 'type_of_water_source' => 'Borehole', 'functionality' => 'Non Functional'];
        $this->postJson(route('water-points.store'), $payload)->assertUnprocessable()->assertJsonValidationErrors('borehole_id');
        $payload['borehole_id'] = $borehole->id;
        $this->post(route('water-points.store'), $payload)->assertRedirect()->assertSessionHasNoErrors();
        $this->postJson(route('water-points.store'), $payload)->assertUnprocessable()->assertJsonValidationErrors('borehole_id');
        $point = WaterPoint::firstOrFail();
        $this->putJson(route('water-points.update', $point), array_replace($payload, ['latitude' => $payload['latitude'] + 0.001]))
            ->assertUnprocessable()->assertJsonValidationErrors('latitude');
        WaterPoint::create(array_replace($payload, ['name' => 'Private foreign point', 'borehole_id' => null, 'type_of_water_source' => 'Well', 'created_by' => User::factory()->operator()->create()->id]));
        $this->get(route('water-points.index', ['search' => 'LINKED-BH']))->assertOk()->assertSee('Linked service point')->assertDontSee('Private foreign point');
        $csv = $this->get(route('water-points.index', ['export' => 'csv', 'province' => $payload['province']]));
        $csv->assertOk()->assertDownload('water-points.csv');
        $this->assertStringContainsString('LINKED-BH', $csv->streamedContent());
        $this->assertStringNotContainsString('Private foreign point', $csv->streamedContent());
    }

    public function test_cleanup_archives_only_obvious_accounts_and_seeds_complete_realistic_idempotent_scenarios(): void
    {
        $keeper = User::factory()->admin()->create(['email' => 'admin@rida.gov.zw']);
        $realAdmin = User::factory()->admin()->create(['email' => 'engineer@rida.gov.zw']);
        $fake = User::factory()->admin()->create(['email' => 'faker@example.org']);
        $duplicate = User::factory()->admin()->create(['name' => 'System Administrator', 'email' => 'admin@wims.local']);
        $this->seed(DatabaseSeeder::class);
        $sample = Borehole::firstOrFail();
        $originalCreator = $sample->created_by;
        $sample->update(['approved_by' => $originalCreator, 'approved_at' => now()]);
        $sampleDam = Dam::firstOrFail();
        $sampleDam->update(['approved_by' => $sampleDam->created_by, 'approved_at' => now()]);
        $counts = app(UatDataRepair::class)->run($keeper->email);
        $this->assertSame(2, $counts['archived_users']);
        $this->assertSame(2, $counts['self_approvals_returned_for_review']);
        $this->assertSame($originalCreator, $sample->fresh()->created_by);
        $this->assertNull($sample->fresh()->approved_at);
        $this->assertNull($sample->fresh()->approved_by);
        $this->assertNull($sampleDam->fresh()->approved_at);
        $this->assertDatabaseHas('audit_logs', ['record_type' => Borehole::class, 'record_id' => $sample->id, 'action' => 'approval_returned_for_review']);
        $this->assertSoftDeleted($fake);
        $this->assertSoftDeleted($duplicate);
        $this->assertNotNull($realAdmin->fresh());
        $this->assertSame('admin', $realAdmin->fresh()->role);
        $this->assertSame(0, User::where('email', 'not like', '%@rida.gov.zw')->count());
        $this->assertGreaterThanOrEqual(1000, (float) Borehole::min('total_cost'));
        $this->assertLessThanOrEqual(5000, (float) Borehole::max('total_cost'));
        $this->assertSame(6, Inventory::count());
        $this->assertSame(5, Invoice::count());
        $this->assertSame(6, DamMaintenance::count());
        $this->assertSame(3, ProspectingSite::where('reference', 'like', 'UAT-%')->count());
        foreach (Dam::all() as $dam) {
            $this->assertTrue(app(ZimbabweLocations::class)->contains($dam->province, $dam->district, (float) $dam->latitude, (float) $dam->longitude));
        }
        $password = User::where('role', 'operator')->firstOrFail()->password;
        $this->seed(DemoUserSeeder::class);
        $this->assertSame($password, User::where('role', 'operator')->firstOrFail()->password);
        $repeat = app(UatDataRepair::class)->run($keeper->email);
        $this->assertSame(0, $repeat['self_approvals_returned_for_review']);
        $this->seed(UatScenarioSeeder::class);
        $this->assertSame(6, Inventory::count());
        $this->assertSame(5, Invoice::count());
        $this->assertSame(6, DamMaintenance::count());
        $this->assertSame(10, WaterPoint::where('type_of_water_source', 'Borehole')->whereNotNull('borehole_id')->count());
        $this->assertGreaterThan(0, AuditLog::count());
    }
}
