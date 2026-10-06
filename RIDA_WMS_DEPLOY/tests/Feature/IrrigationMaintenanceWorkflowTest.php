<?php

namespace Tests\Feature;

use App\Models\IrrigationMaintenance;
use App\Models\IrrigationScheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IrrigationMaintenanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_record_five_maintenance_events_for_an_owned_scheme(): void
    {
        $operator = User::factory()->operator()->create();
        $scheme = IrrigationScheme::create([
            'name' => 'Demo irrigation scheme',
            'village' => 'Chigo A',
            'ward' => '4',
            'irrigation_type' => 'Drip',
            'water_source_type' => 'Borehole',
            'functionality' => 'Functional',
            'created_by' => $operator->id,
        ]);

        $this->actingAs($operator);
        foreach (range(1, 5) as $index) {
            $this->post(route('irrigation-maintenances.store'), [
                'irrigation_scheme_id' => $scheme->id,
                'repaired_by' => "Technician {$index}",
                'works_done' => "Maintenance event {$index}",
                'spares_fitted' => "Seal {$index}",
                'date_repaired' => '2026-09-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
            ])->assertRedirect(route('irrigation-maintenances.index'));
        }

        $this->assertDatabaseCount('irrigation_maintenances', 5);
        $maintenance = IrrigationMaintenance::firstOrFail();
        $this->get(route('irrigation-maintenances.index'))->assertOk()->assertSee('Demo irrigation scheme');
        $this->get(route('irrigation-maintenances.show', $maintenance))->assertOk()->assertSee('Maintenance event');
        $this->get(route('irrigation-maintenances.edit', $maintenance))->assertOk();

        $this->put(route('irrigation-maintenances.update', $maintenance), [
            'irrigation_scheme_id' => $scheme->id,
            'repaired_by' => 'Lead Technician',
            'works_done' => 'Replaced pump bearing',
            'date_repaired' => '2026-09-10',
        ])->assertRedirect(route('irrigation-maintenances.show', $maintenance));

        $this->assertDatabaseHas('irrigation_maintenances', [
            'id' => $maintenance->id,
            'repaired_by' => 'Lead Technician',
            'scheme_name' => 'Demo irrigation scheme',
        ]);

        $this->delete(route('irrigation-maintenances.destroy', $maintenance))
            ->assertRedirect(route('irrigation-maintenances.index'));
        $this->assertSoftDeleted('irrigation_maintenances', ['id' => $maintenance->id]);
    }

    public function test_operator_cannot_attach_maintenance_to_another_users_scheme(): void
    {
        $owner = User::factory()->operator()->create();
        $operator = User::factory()->operator()->create();
        $scheme = IrrigationScheme::create([
            'name' => 'Private scheme',
            'village' => 'Other Village',
            'ward' => '2',
            'irrigation_type' => 'Canal',
            'water_source_type' => 'River',
            'functionality' => 'Functional',
            'created_by' => $owner->id,
        ]);

        $this->actingAs($operator)->post(route('irrigation-maintenances.store'), [
            'irrigation_scheme_id' => $scheme->id,
            'repaired_by' => 'Technician',
            'works_done' => 'Repair',
            'date_repaired' => '2026-09-20',
        ])->assertForbidden();

        $this->assertDatabaseCount('irrigation_maintenances', 0);
    }
}