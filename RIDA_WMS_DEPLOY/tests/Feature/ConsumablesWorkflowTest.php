<?php

namespace Tests\Feature;

use App\Models\Consumable;
use App\Models\ConsumableCategory;
use App\Models\Inventory;
use App\Models\StockRequest;
use App\Models\UsageReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsumablesWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_five_items_and_operator_can_request_issue_and_report_usage(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->operator()->create();
        $this->actingAs($admin);

        $this->get(route('consumables.categories.index'))->assertOk();
        $this->get(route('consumables.categories.create'))->assertOk();
        $this->post(route('consumables.categories.store'), [
            'name' => 'Drilling tools',
            'description' => 'Bits and hammers',
        ])->assertRedirect(route('consumables.categories.index'));
        $category = ConsumableCategory::firstOrFail();
        foreach (range(2, 5) as $index) {
            $this->post(route('consumables.categories.store'), [
                'name' => "Additional category {$index}",
                'description' => 'Five-record category capture test',
            ])->assertRedirect(route('consumables.categories.index'));
        }
        $this->assertDatabaseCount('consumable_categories', 5);
        $this->get(route('consumables.categories.edit', $category))->assertOk();
        $this->put(route('consumables.categories.update', $category), [
            'name' => 'Drilling consumables',
            'description' => 'Bits and hammers',
        ])->assertRedirect(route('consumables.categories.index'));
        $category->refresh();

        $this->get(route('consumables.suppliers.index'))->assertOk();
        $this->get(route('consumables.suppliers.create'))->assertOk();

        foreach (range(1, 5) as $index) {
            $this->post(route('consumables.suppliers.store'), [
                'name' => "Test supplier {$index}",
                'contact_person' => "Contact {$index}",
                'email' => "supplier{$index}@example.test",
                'phone' => "077000000{$index}",
                'address' => 'Test address',
            ])->assertRedirect(route('consumables.suppliers.index'));
        }
        $this->assertDatabaseCount('suppliers', 5);
        $supplier = \App\Models\Supplier::firstOrFail();
        $this->get(route('consumables.suppliers.index'))->assertOk()->assertSee('Test supplier');
        $this->get(route('consumables.suppliers.show', $supplier))->assertOk();
        $this->get(route('consumables.suppliers.edit', $supplier))->assertOk();
        $this->put(route('consumables.suppliers.update', $supplier), [
            'name' => 'Updated test supplier',
            'contact_person' => 'Primary contact',
            'email' => 'updated@example.test',
            'phone' => '07700000001',
            'address' => 'Updated test address',
            'status' => 'active',
        ])->assertRedirect(route('consumables.suppliers.index'));

        foreach (range(1, 5) as $index) {
            $this->post(route('consumables.inventory.store'), [
                'name' => "Drill bit {$index}",
                'code' => "BIT-TEST-{$index}",
                'category_id' => $category->id,
                'unit' => 'pieces',
                'unit_cost' => '12.50',
                'minimum_stock' => 4,
                'initial_quantity' => 20,
                'location' => 'Main store',
                'batch_number' => "BATCH-{$index}",
            ])->assertRedirect(route('consumables.inventory.index'));
        }

        $this->assertDatabaseCount('consumables', 5);
        $this->assertDatabaseCount('inventory', 5);
        $firstItem = Consumable::where('code', 'BIT-TEST-1')->firstOrFail();
        $this->get(route('consumables.inventory.create'))->assertOk();
        $this->get(route('consumables.inventory.index'))->assertOk()->assertSee('BIT-TEST-1');
        $this->get(route('consumables.inventory.show', $firstItem))->assertOk();
        $this->get(route('consumables.inventory.edit', $firstItem))->assertOk();
        $this->put(route('consumables.inventory.update', $firstItem), [
            'name' => $firstItem->name,
            'code' => $firstItem->code,
            'category_id' => $category->id,
            'description' => 'Measured test item',
            'unit' => 'pieces',
            'unit_cost' => '12.50',
            'minimum_stock' => 4,
            'status' => 'active',
        ])->assertRedirect(route('consumables.inventory.index'));

        $this->post(route('consumables.inventory.adjust-stock', $firstItem), [
            'adjustment_type' => 'add',
            'quantity' => 5,
            'reason' => 'Received delivery',
        ])->assertRedirect(route('consumables.inventory.index'));
        $this->assertSame(25, (int) $firstItem->fresh()->current_stock);

        $this->actingAs($operator);
        $this->get(route('consumables.stock-requests.create'))->assertOk();
        foreach (range(1, 5) as $index) {
            $this->post(route('consumables.stock-requests.store'), [
                'project_type' => 'borehole',
                'project_name' => "North project {$index}",
                'village' => 'Chigo A',
                'ward' => '4',
                'purpose' => 'Borehole drilling',
                'items' => [
                    ['consumable_id' => $firstItem->id, 'quantity' => 2, 'notes' => "Request {$index}"],
                ],
            ])->assertRedirect(route('consumables.stock-requests.index'));
        }

        $this->assertDatabaseCount('stock_requests', 5);
        $this->assertDatabaseCount('stock_request_items', 5);
        $stockRequest = StockRequest::firstOrFail();
        $this->get(route('consumables.stock-requests.show', $stockRequest))->assertOk();
        $this->get(route('consumables.stock-requests.index'))->assertOk();
        $this->actingAs($admin)->post(route('consumables.stock-requests.approve', $stockRequest), [
            'items' => [$stockRequest->items()->first()->id => ['approved_quantity' => 2]],
        ])->assertRedirect(route('consumables.stock-requests.show', $stockRequest));
        $this->post(route('consumables.stock-requests.issue', $stockRequest), [
            'items' => [$stockRequest->items()->first()->id => ['issued_quantity' => 2]],
        ])->assertRedirect(route('consumables.stock-requests.show', $stockRequest));
        $this->assertSame(23, (int) $firstItem->fresh()->current_stock);

        $this->actingAs($operator);
        foreach (range(1, 5) as $index) {
            $this->post(route('consumables.usage-reports.store'), [
                'project_type' => 'borehole',
                'project_name' => "North project {$index}",
                'village' => 'Chigo A',
                'ward' => '4',
                'work_description' => "Installed bit on borehole {$index}",
                'maintenance_type' => 'installation',
                'date_of_work' => '2026-09-20',
                'items' => [
                    ['consumable_id' => $firstItem->id, 'quantity_used' => 1, 'batch_number' => 'BATCH-1'],
                ],
            ])->assertRedirect(route('consumables.usage-reports.index'));
        }

        $this->assertDatabaseCount('usage_reports', 5);
        $this->assertDatabaseCount('usage_report_items', 5);
        $this->assertSame(18, (int) $firstItem->fresh()->current_stock);
        $this->assertDatabaseCount('inventory_movements', 12);
        $this->assertSame(18, (int) $firstItem->inventory->movements()->sum('quantity_delta'));
        $this->get(route('consumables.usage-reports.index'))->assertOk();
        $this->get(route('consumables.usage-reports.create'))->assertOk();
        $report = UsageReport::firstOrFail();
        $this->get(route('consumables.usage-reports.show', $report))->assertOk();
        $this->get(route('consumables.usage-reports.report'))->assertOk();
        $this->get(route('consumables.usage-reports.export'))->assertOk();
    }

    public function test_viewer_cannot_adjust_stock_or_report_usage(): void
    {
        $viewer = User::factory()->viewer()->create();
        $category = ConsumableCategory::create(['name' => 'Viewer test']);
        $item = Consumable::create([
            'name' => 'Restricted item',
            'code' => 'VIEWER-ITEM',
            'category_id' => $category->id,
            'unit' => 'pieces',
            'unit_cost' => 1,
            'minimum_stock' => 0,
        ]);
        Inventory::create(['consumable_id' => $item->id, 'quantity' => 10]);

        $this->actingAs($viewer)
            ->post(route('consumables.inventory.adjust-stock', $item), [
                'adjustment_type' => 'remove',
                'quantity' => 1,
                'reason' => 'Unauthorized test',
            ])
            ->assertForbidden();
        $this->post(route('consumables.usage-reports.store'), [])->assertForbidden();
    }
}
