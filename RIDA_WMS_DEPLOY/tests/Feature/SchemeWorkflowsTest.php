<?php

namespace Tests\Feature;

use App\Models\Dam;
use App\Models\IrrigationScheme;
use App\Models\PipedWaterScheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchemeWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_management_navigation_is_restricted_to_administrators(): void
    {
        $operator = User::factory()->operator()->create();
        $this->actingAs($operator)
            ->get(route('dams.index'))
            ->assertOk()
            ->assertDontSee('href="' . route('users.index') . '"');

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
            ->get(route('dams.index'))
            ->assertOk()
            ->assertSee('href="' . route('users.index') . '"', false);
    }

    public function test_authenticated_user_can_create_update_view_and_delete_irrigation_scheme(): void
    {
        $user = User::factory()->operator()->create();
        $this->actingAs($user);
        $payload = [
            'name_of_scheme' => 'North Fields',
            'village' => 'Chigo A',
            'ward' => '4',
            'irrigation_type' => 'Drip',
            'type_of_water_source' => 'Borehole',
            'year_established' => 2019,
            'type_of_pump' => 'Solar Pump',
            'type_of_power' => 'Solar',
            'size_in_hectares' => 3.5,
            'total_storage_tanks_in_m3' => 15,
            'crop_pattern' => 'Maize',
            'plot_size' => 0.5,
            'number_of_beneficiaries_in_hh' => 24,
            'functionality_of_the_scheme' => 'Functional',
            'management_committee_available' => 1,
            'is_trained' => 1,
            'is_functional' => 1,
            'revolving_fund' => 1,
            'revolving_fund_amount' => 75,
            'total_number_of_members' => 8,
            'women_members' => 4,
        ];

        $this->post(route('irrigation-schemes.store'), $payload)
            ->assertRedirect(route('irrigation-schemes.index'))
            ->assertSessionHasNoErrors();

        $scheme = IrrigationScheme::firstOrFail();
        $this->assertSame($user->id, $scheme->created_by);
        $this->assertSame('North Fields', $scheme->name);
        $this->assertTrue($scheme->has_management_committee);

        $this->get(route('irrigation-schemes.index'))->assertOk();
        $this->get(route('irrigation-schemes.show', $scheme))->assertOk();
        $this->get(route('irrigation-schemes.edit', $scheme))->assertOk();

        $payload['name_of_scheme'] = 'South Fields';
        $this->put(route('irrigation-schemes.update', $scheme), $payload)
            ->assertRedirect(route('irrigation-schemes.index'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('irrigation_schemes', [
            'id' => $scheme->id,
            'name' => 'South Fields',
            'updated_by' => $user->id,
        ]);

        $this->delete(route('irrigation-schemes.destroy', $scheme))
            ->assertRedirect(route('irrigation-schemes.index'));
        $this->assertSoftDeleted('irrigation_schemes', ['id' => $scheme->id]);
    }

    public function test_authenticated_user_can_create_update_view_and_delete_piped_water_scheme(): void
    {
        $this->actingAs(User::factory()->operator()->create());
        $payload = [
            'name' => 'Chigo Water Scheme',
            'village' => 'Chigo A',
            'ward' => '4',
            'water_source' => 'borehole',
            'year_established' => 2018,
            'established_by' => 'District Council',
            'pump_type' => 'Submersible',
            'power_type' => 'solar',
            'network_length' => 1250,
            'storage_tanks' => 30,
            'stand_pipes_count' => 12,
            'functionality' => 'Functional',
            'households_served' => 120,
            'population_served' => 640,
            'women_served' => 330,
            'men_served' => 250,
            'children_served' => 60,
            'has_management_committee' => 1,
            'committee_trained' => 1,
            'committee_functional' => 1,
            'has_revolving_fund' => 1,
            'fund_amount' => 150,
            'committee_members_total' => 8,
            'committee_women' => 4,
            'committee_men' => 4,
            'remarks' => 'Routine inspection due.',
        ];

        $this->post(route('piped-water-schemes.store'), $payload)
            ->assertRedirect(route('piped-water-schemes.index'))
            ->assertSessionHasNoErrors();

        $scheme = PipedWaterScheme::firstOrFail();
        $this->assertTrue($scheme->has_management_committee);
        $this->get(route('piped-water-schemes.index'))->assertOk();
        $this->get(route('piped-water-schemes.show', $scheme))->assertOk();
        $this->get(route('piped-water-schemes.edit', $scheme))->assertOk();

        $payload['network_length'] = 1400;
        $payload['functionality'] = 'Partially Functional';
        $payload['committee_trained'] = 0;
        $this->put(route('piped-water-schemes.update', $scheme), $payload)
            ->assertRedirect(route('piped-water-schemes.index'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('piped_water_schemes', [
            'id' => $scheme->id,
            'network_length' => 1400,
            'functionality' => 'Partially Functional',
            'committee_trained' => false,
        ]);

        $this->delete(route('piped-water-schemes.destroy', $scheme))
            ->assertRedirect(route('piped-water-schemes.index'));
        $this->assertSoftDeleted('piped_water_schemes', ['id' => $scheme->id]);
    }

    public function test_authenticated_user_can_create_update_view_and_delete_dam(): void
    {
        $this->actingAs(User::factory()->operator()->create());
        $payload = [
            'name' => 'North Dam',
            'village' => 'Chigo A',
            'ward' => '4',
            'district' => 'North District',
            'province' => 'Harare Province',
            'wall_condition' => 'good',
            'spillway_condition' => 'good',
            'capacity' => 500,
        ];

        $this->post(route('dams.store'), $payload)
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $dam = Dam::firstOrFail();
        $this->get(route('dams.index'))->assertOk();
        $this->get(route('dams.show', $dam))->assertOk();
        $this->get(route('dams.edit', $dam))->assertOk();
        $this->get(route('dams.maintenance.index', $dam))->assertOk();
        $this->get(route('dams.maintenance.create', $dam))->assertOk();

        $maintenancePayload = [
            'maintenance_type' => 'routine',
            'maintenance_date' => '2026-09-01',
            'wall_repair_details' => 'Repaired minor erosion.',
            'estimated_cost' => 100,
            'actual_cost' => 90,
            'contractor_name' => 'Local Works',
            'status' => 'completed',
        ];
        $this->post(route('dams.maintenance.store', $dam), $maintenancePayload)
            ->assertRedirect(route('dams.maintenance.index', $dam))
            ->assertSessionHasNoErrors();

        $maintenance = $dam->maintenanceRecords()->firstOrFail();
        $this->get(route('dams.maintenance.show', [$dam, $maintenance]))->assertOk();
        $this->get(route('dams.maintenance.edit', [$dam, $maintenance]))->assertOk();
        $maintenancePayload['status'] = 'in_progress';
        $this->put(route('dams.maintenance.update', [$dam, $maintenance]), $maintenancePayload)
            ->assertRedirect(route('dams.maintenance.index', $dam))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('dam_maintenance', [
            'id' => $maintenance->id,
            'status' => 'in_progress',
            'updated_by' => auth()->id(),
        ]);
        $this->delete(route('dams.maintenance.destroy', [$dam, $maintenance]))
            ->assertRedirect(route('dams.maintenance.index', $dam));
        $this->assertSoftDeleted('dam_maintenance', ['id' => $maintenance->id]);

        $payload['name'] = 'South Dam';
        $this->put(route('dams.update', $dam), $payload)
            ->assertRedirect(route('dams.show', $dam->id))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('dams', ['id' => $dam->id, 'name' => 'South Dam']);

        $this->delete(route('dams.destroy', $dam))
            ->assertRedirect(route('dams.index'));
        $this->assertSoftDeleted('dams', ['id' => $dam->id]);
    }
}