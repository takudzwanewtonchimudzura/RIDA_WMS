<?php

namespace Tests\Feature;

use App\Models\Borehole;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_report_api_is_scoped_to_non_admin_users(): void
    {
        $operator = User::factory()->operator()->create();
        Borehole::create([
            'borehole_serial_no' => 'BH-OWNED',
            'province' => 'Midlands Province',
            'admin_district' => 'North District',
            'locality' => 'Chigo A',
            'village' => 'Chigo A',
            'ward' => '4',
            'date' => '2026-06-01',
            'created_by' => $operator->id,
        ]);
        Borehole::create([
            'borehole_serial_no' => 'BH-PRIVATE',
            'province' => 'Midlands Province',
            'admin_district' => 'South District',
            'locality' => 'Other Village',
            'village' => 'Other Village',
            'ward' => '5',
            'date' => '2026-06-02',
            'created_by' => User::factory()->create()->id,
        ]);

        $this->actingAs($operator)
            ->get(route('reports.api.executive.data'))
            ->assertOk()
            ->assertJsonPath('totalBoreholes', 1);
    }

    public function test_geographic_filters_and_blowing_yield_are_used_in_reports(): void
    {
        Borehole::create([
            'borehole_serial_no' => 'BH-PISP-1',
            'province' => 'Midlands Province',
            'admin_district' => 'North District',
            'locality' => 'Chigo A',
            'village' => 'Chigo A',
            'ward' => '4',
            'siting_agency' => 'PISP',
            'date' => '2026-06-01',
            'blowing_yield' => 12,
            'installed_by_name' => 'A. Installer',
            'installed_by_organisation' => 'Field Services',
            'pump_installation_status' => 'successful',
        ]);
        Borehole::create([
            'borehole_serial_no' => 'BH-GOV-1',
            'province' => 'Midlands Province',
            'admin_district' => 'South District',
            'locality' => 'Other Village',
            'village' => 'Other Village',
            'ward' => '5',
            'siting_agency' => 'Government',
            'date' => '2026-06-02',
            'yield_lps' => 3,
        ]);

        $reports = app(ReportService::class);
        $filters = [
            'district' => 'North District',
            'village' => 'Chigo',
            'siting_agency' => 'PISP',
        ];

        $this->assertSame(1, $reports->getTotalBoreholes($filters));
        $this->assertSame(12.0, (float) $reports->getAverageYield($filters));
        $this->assertSame(100.0, (float) $reports->getDrillingMetrics($filters)['success_rate']);
        $this->assertSame(1, $reports->getYieldAnalysis($filters)['distribution'][3]);
        $installer = $reports->getInstallerPerformance($filters)->first();
        $this->assertSame('A. Installer', $installer->installed_by_name);
        $this->assertSame(1, (int) $installer->installation_count);
        $this->assertSame(1, (int) $installer->successful_installations);
        $this->assertSame(100.0, (float) $installer->success_rate);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('reports.technical.analysis', $filters))
            ->assertOk()
            ->assertSee('Village')
            ->assertSee('Siting agency');

        $this->get(route('reports.operational.performance', $filters))
            ->assertOk()
            ->assertSee('Pump installation success by installer');

        $this->get(route('analytics.dashboard', $filters))
            ->assertOk()
            ->assertSee('Siting Agency');
    }
}