<?php

namespace Tests\Feature;

use App\Models\PipedWaterScheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemainingAreasTest extends TestCase
{
    use RefreshDatabase;

    public function test_water_quality_tests_are_recorded_and_history_is_visible(): void
    {
        $operator = User::factory()->operator()->create();
        $scheme = PipedWaterScheme::create([
            'name' => 'Test Scheme',
            'village' => 'Test Village',
            'ward' => 'Ward 1',
            'water_source' => 'borehole',
            'year_established' => 2020,
            'established_by' => 'RIDA',
            'pump_type' => 'Solar pump',
            'power_type' => 'solar',
            'network_length' => 100,
            'storage_tanks' => 20,
            'stand_pipes_count' => 4,
            'functionality' => 'Functional',
            'households_served' => 50,
            'population_served' => 250,
            'women_served' => 125,
            'men_served' => 100,
            'children_served' => 25,
            'created_by' => $operator->id,
        ]);

        $this->actingAs($operator)
            ->post(route('piped-water-schemes.quality-tests.store', $scheme), [
                'tested_at' => '2026-10-01',
                'ph' => 7.2,
                'turbidity_ntu' => 2.1,
                'ecoli_cfu_100ml' => 0,
                'tds_mg_l' => 210,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('water_quality_tests', [
            'piped_water_scheme_id' => $scheme->id,
            'tested_by' => $operator->id,
            'ecoli_cfu_100ml' => 0,
        ]);

        $this->get(route('piped-water-schemes.quality'))
            ->assertOk()
            ->assertSee('Meets Standards');
        $this->get(route('piped-water-schemes.quality-tests.history', $scheme))
            ->assertOk()
            ->assertSee('01 Oct 2026');
    }

    public function test_report_pages_render_without_placeholder_data_errors(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('reports.executive.dashboard'))
            ->assertOk()
            ->assertSee('provinceChart');

        $this->get(route('reports.geographic.distribution'))
            ->assertOk()
            ->assertSee('mapContainer')
            ->assertDontSee('Interactive Map Integration');
    }
}
