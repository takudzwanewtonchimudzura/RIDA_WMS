<?php

namespace Tests\Feature;

use App\Models\Borehole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsumablesPerformanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_report_is_limited_to_owned_boreholes_and_groups_serials(): void
    {
        $operator = User::factory()->operator()->create();
        $this->createBorehole($operator, [
            'borehole_serial_no' => 'BH-101',
            'dth_bit_serial_no' => 'BIT-7',
            'hammer_serial_no' => 'HAM-2',
            'drilling_bits_used' => 2,
            'hammer_rings_used' => 4,
        ]);
        $secondBorehole = $this->createBorehole($operator, [
            'borehole_serial_no' => 'BH-102',
            'dth_bit_serial_no' => 'BIT-7',
            'hammer_serial_no' => 'HAM-2',
            'drilling_bits_used' => 3,
            'hammer_rings_used' => 1,
        ]);
        $secondBorehole->consumableUsages()->createMany([
            ['equipment_type' => 'bit', 'serial_number' => 'BIT-7', 'quantity_used' => 3],
            ['equipment_type' => 'hammer', 'serial_number' => 'HAM-2', 'quantity_used' => 1],
        ]);
        $this->createBorehole(User::factory()->create(), [
            'borehole_serial_no' => 'BH-201',
            'dth_bit_serial_no' => 'OTHER-BIT',
            'hammer_serial_no' => 'OTHER-HAMMER',
            'drilling_bits_used' => 10,
            'hammer_rings_used' => 12,
        ]);

        $this->actingAs($operator)
            ->get(route('reports.consumables.performance', [
                'project_name' => 'North Project',
                'date_from' => '2026-01-01',
                'date_to' => '2026-12-31',
            ]))
            ->assertOk()
            ->assertSee('BIT-7')
            ->assertSee('HAM-2')
            ->assertSee('North Project')
            ->assertSee('5')
            ->assertDontSee('OTHER-BIT')
            ->assertDontSee('OTHER-HAMMER');
    }

    private function createBorehole(User $owner, array $overrides): Borehole
    {
        return Borehole::create(array_merge([
            'borehole_serial_no' => 'BH-' . uniqid(),
            'province' => 'Midlands Province',
            'admin_district' => 'North District',
            'locality' => 'Chigo A',
            'ward' => '4',
            'date' => '2026-09-25',
            'project_name' => 'North Project',
            'created_by' => $owner->id,
        ], $overrides));
    }
}