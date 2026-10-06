<?php

namespace Tests\Feature;

use App\Models\SchemeMaintenance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchemeMaintenanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_capture_five_piped_water_maintenance_records(): void
    {
        $operator = User::factory()->operator()->create();
        $this->actingAs($operator);

        foreach (range(1, 5) as $index) {
            $this->post(route('scheme-maintenances.store'), [
                'name_of_scheme' => "Piped Scheme {$index}",
                'village' => 'Chigo A',
                'ward' => '4',
                'type_of_water_source' => 'Borehole',
                'year_established' => 2018,
                'type_of_pump' => 'Submersible',
                'type_of_power' => 'Solar',
                'total_network_in_metres' => 1250,
                'total_storage_tanks_in_m3' => 30,
                'number_of_stand_pipes' => 12,
                'repaired_by' => "Technician {$index}",
                'works_done' => "Maintenance work {$index}",
                'spares_fitted' => 'Valve',
                'spares_received_by' => 'Community representative',
                'contact_detail' => 'office@example.test',
                'date_repaired' => '2026-09-20',
                'date_last_repaired' => '2026-08-20',
                'remarks' => 'Reviewed on site',
            ])->assertRedirect(route('scheme-maintenances.index'))->assertSessionHasNoErrors();
        }

        $this->assertDatabaseCount('scheme_maintenances', 5);
        $this->assertDatabaseHas('scheme_maintenances', [
            'scheme_name' => 'Piped Scheme 5',
            'water_source_type' => 'Borehole',
            'pump_type' => 'Submersible',
            'total_network_metres' => 1250,
            'created_by' => $operator->id,
        ]);

        $this->get(route('scheme-maintenances.index'))->assertOk()->assertSee('Piped Scheme 1');
        $maintenance = SchemeMaintenance::where('scheme_name', 'Piped Scheme 1')->firstOrFail();
        $this->get(route('scheme-maintenances.show', $maintenance))->assertOk()->assertSee('Maintenance work 1');
        $this->get(route('scheme-maintenances.edit', $maintenance))->assertOk();

        $this->put(route('scheme-maintenances.update', $maintenance), [
            'name_of_scheme' => 'Piped Scheme Updated',
            'village' => 'Chigo A',
            'ward' => '4',
            'repaired_by' => 'Updated Technician',
            'works_done' => 'Pump repaired',
            'date_repaired' => '2026-09-21',
        ])->assertRedirect(route('scheme-maintenances.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('scheme_maintenances', [
            'id' => $maintenance->id,
            'scheme_name' => 'Piped Scheme Updated',
            'updated_by' => $operator->id,
        ]);

        $this->delete(route('scheme-maintenances.destroy', $maintenance))
            ->assertRedirect(route('scheme-maintenances.index'));
        $this->assertSoftDeleted('scheme_maintenances', ['id' => $maintenance->id]);
        $this->assertDatabaseHas('scheme_maintenances', [
            'id' => $maintenance->id,
            'deleted_by' => $operator->id,
        ]);
    }

    public function test_maintenance_records_are_owner_scoped_and_viewers_cannot_mutate(): void
    {
        $owner = User::factory()->operator()->create();
        $otherOperator = User::factory()->operator()->create();
        $viewer = User::factory()->viewer()->create();
        $maintenance = SchemeMaintenance::create([
            'scheme_name' => 'Owner Scheme',
            'village' => 'Chigo A',
            'ward' => '4',
            'repaired_by' => 'Technician',
            'works_done' => 'Routine repair',
            'date_repaired' => '2026-09-20',
            'created_by' => $owner->id,
        ]);

        $this->actingAs($otherOperator)
            ->get(route('scheme-maintenances.show', $maintenance))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->get(route('scheme-maintenances.index'))
            ->assertOk()
            ->assertSee('Owner Scheme');
        $this->get(route('scheme-maintenances.edit', $maintenance))->assertForbidden();
        $this->post(route('scheme-maintenances.store'), [])->assertForbidden();
    }
}
