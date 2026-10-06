<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WaterPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaterPointWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_enter_five_water_points_and_manage_owned_records(): void
    {
        $operator = User::factory()->operator()->create();
        $this->actingAs($operator);

        foreach (range(1, 5) as $index) {
            $this->post(route('water-points.store'), [
                'name' => "Test borehole {$index}",
                'village_name' => "Village {$index}",
                'ward' => (string) $index,
                'type_of_water_source' => 'Borehole',
                'functionality' => 'Functional',
            ])->assertRedirect(route('water-points.index'));
        }

        $this->assertDatabaseCount('water_points', 5);
        $point = WaterPoint::firstOrFail();
        $this->assertSame($operator->id, $point->created_by);
        $this->get(route('water-points.index'))->assertOk()->assertSee('Test borehole');
        $this->get(route('water-points.show', $point))->assertOk();
        $this->get(route('water-points.edit', $point))->assertOk();

        $this->put(route('water-points.update', $point), [
            'name' => 'Updated water point',
            'village_name' => 'Chigo A',
            'ward' => '1',
            'type_of_water_source' => 'Borehole',
            'functionality' => 'Partially Functional',
        ])->assertRedirect(route('water-points.index'));

        $this->assertDatabaseHas('water_points', [
            'id' => $point->id,
            'name' => 'Updated water point',
            'updated_by' => $operator->id,
        ]);

        $this->delete(route('water-points.destroy', $point))->assertRedirect(route('water-points.index'));
        $this->assertSoftDeleted('water_points', ['id' => $point->id]);
    }

    public function test_viewer_cannot_create_or_edit_water_points(): void
    {
        $viewer = User::factory()->viewer()->create();
        $this->actingAs($viewer);

        $this->get(route('water-points.create'))->assertForbidden();
        $this->post(route('water-points.store'), [])->assertForbidden();
    }
}