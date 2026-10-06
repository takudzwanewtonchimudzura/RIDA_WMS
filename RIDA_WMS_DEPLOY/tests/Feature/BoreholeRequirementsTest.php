<?php

namespace Tests\Feature;

use App\Models\Borehole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoreholeRequirementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_borehole_requirements_fields_are_saved_with_geology_and_casing_details(): void
    {
        $this->actingAs(User::factory()->operator()->create());
        $this->get(route('boreholes.create'))
            ->assertOk()
            ->assertSee('data-pump-field="handpump"', false);

        $response = $this->postJson(route('boreholes.store'), [
            'borehole_serial_no' => 'BH-REQ-001',
            'province' => 'Harare Province',
            'admin_district' => 'North District',
            'locality' => 'Chigo A',
            'ward' => '4',
            'village' => 'Chigo A',
            'date' => '2026-09-25',
            'blowing_yield' => 2.5,
            'final_cased_depth' => 85,
            'water_appearance' => 'Clear',
            'installed_by_name' => 'T. Operator',
            'installed_by_organisation' => 'Water Services',
            'consumables' => [
                ['equipment_type' => 'bit', 'serial_number' => 'BIT-01', 'quantity_used' => 2],
                ['equipment_type' => 'bit', 'serial_number' => 'BIT-02', 'quantity_used' => 1],
                ['equipment_type' => 'hammer', 'serial_number' => 'HAM-01', 'quantity_used' => 4],
            ],
            'bit_size_intervals' => [
                ['bit_size_mm' => 216, 'depth_from_m' => 0, 'depth_to_m' => 30],
                ['bit_size_mm' => 165, 'depth_from_m' => 30, 'depth_to_m' => 85],
            ],
            'geology_layers' => [[
                'sample_description' => 'Hard rock',
                'description_layer_depth_from' => 0,
                'description_layer_depth_to' => 22,
                'penetration_rates_to' => 22,
                'penetration_rates_rate' => '12 min/rod',
                'water_strike' => '18m',
            ]],
            'casing_details' => [[
                'description' => 'Plain',
                'diameter' => 150,
                'depth_from' => 0,
                'depth_to' => 80,
                'position' => 1,
                'casing_type' => 'PVC',
                'casing_class' => '9/10',
                'gravel_packed' => 1,
                'cement_sealed' => 0,
            ]],
        ]);

        $response->assertCreated()->assertJsonPath('success', true);

        $borehole = Borehole::firstOrFail();
        $this->assertDatabaseHas('boreholes', [
            'id' => $borehole->id,
            'village' => 'Chigo A',
            'blowing_yield' => 2.5,
            'final_cased_depth' => 85,
            'water_appearance' => 'Clear',
            'installed_by_name' => 'T. Operator',
            'installed_by_organisation' => 'Water Services',
        ]);
        $this->assertDatabaseHas('geology_layers', [
            'borehole_id' => $borehole->id,
            'sample_description' => 'Hard rock',
        ]);
        $this->assertDatabaseHas('casing_details', [
            'borehole_id' => $borehole->id,
            'casing_type' => 'PVC',
            'casing_class' => '9/10',
            'gravel_packed' => true,
            'cement_sealed' => false,
        ]);
        $this->assertDatabaseHas('borehole_consumable_usages', [
            'borehole_id' => $borehole->id,
            'equipment_type' => 'bit',
            'serial_number' => 'BIT-02',
            'quantity_used' => 1,
        ]);
        $this->assertSame(3, $borehole->consumableUsages()->count());
        $this->assertDatabaseHas('borehole_bit_size_intervals', [
            'borehole_id' => $borehole->id,
            'bit_size_mm' => 165,
            'depth_from_m' => 30,
            'depth_to_m' => 85,
        ]);

        $this->get(route('boreholes.edit', $borehole))->assertOk();
        $this->get(route('boreholes.show', $borehole))->assertOk();
        $this->get(route('boreholes.pdf.download', $borehole))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->get(route('boreholes.certificate.download', $borehole))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->put(route('boreholes.update', $borehole), [
            'borehole_serial_no' => 'BH-REQ-001',
            'province' => 'Harare Province',
            'admin_district' => 'North District',
            'locality' => 'Chigo A',
            'ward' => '4',
            'village' => 'Chigo B',
            'date' => '2026-09-25',
            'blowing_yield' => 3.1,
            'final_cased_depth' => 90,
            'water_appearance' => 'Slightly cloudy',
            'installed_by_name' => 'R. Technician',
            'installed_by_organisation' => 'Regional Water Office',
            'consumables' => [
                ['equipment_type' => 'bit', 'serial_number' => 'BIT-03', 'quantity_used' => 5],
                ['equipment_type' => 'hammer', 'serial_number' => 'HAM-02', 'quantity_used' => 2],
            ],
            'bit_size_intervals' => [
                ['bit_size_mm' => 203, 'depth_from_m' => 0, 'depth_to_m' => 90],
            ],
            'geology_layers' => [[
                'sample_description' => 'Sandstone',
                'description_layer_depth_from' => 0,
                'description_layer_depth_to' => 25,
            ]],
            'casing_details' => [[
                'description' => 'Slotted',
                'diameter' => 150,
                'depth_from' => 0,
                'depth_to' => 85,
                'position' => 1,
                'casing_type' => 'Steel',
                'casing_class' => '10',
                'gravel_packed' => 0,
                'cement_sealed' => 1,
            ]],
        ])->assertRedirect(route('boreholes.show', $borehole))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('boreholes', [
            'id' => $borehole->id,
            'village' => 'Chigo B',
            'final_cased_depth' => 90,
            'water_appearance' => 'Slightly cloudy',
        ]);
        $this->assertDatabaseHas('geology_layers', [
            'borehole_id' => $borehole->id,
            'sample_description' => 'Sandstone',
        ]);
        $this->assertDatabaseHas('casing_details', [
            'borehole_id' => $borehole->id,
            'casing_type' => 'Steel',
            'casing_class' => '10',
            'gravel_packed' => false,
            'cement_sealed' => true,
        ]);
        $this->assertSame(2, $borehole->consumableUsages()->count());
        $this->assertDatabaseHas('borehole_consumable_usages', [
            'borehole_id' => $borehole->id,
            'serial_number' => 'BIT-03',
            'quantity_used' => 5,
        ]);
        $this->assertSame(1, $borehole->bitSizeIntervals()->count());
        $this->assertDatabaseHas('borehole_bit_size_intervals', [
            'borehole_id' => $borehole->id,
            'bit_size_mm' => 203,
            'depth_from_m' => 0,
            'depth_to_m' => 90,
        ]);
    }
}