<?php

namespace Tests\Feature;

use App\Exports\BoreholesExport;
use App\Models\Borehole;
use App\Models\Client;
use App\Models\Consumable;
use App\Models\ConsumableCategory;
use App\Models\Dam;
use App\Models\Invoice;
use App\Models\IrrigationScheme;
use App\Models\ProspectingSite;
use App\Models\PipedWaterScheme;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WaterPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemRoleMatrixTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_eleven_role_fixtures_can_read_their_permitted_data_across_core_modules(): void
    {
        $users = collect()
            ->merge(User::factory()->admin()->count(4)->create())
            ->merge(User::factory()->operator()->count(4)->create())
            ->merge(User::factory()->viewer()->count(3)->create());

        $stockCategory = ConsumableCategory::create(['name' => 'Matrix consumables']);
        foreach ($users->filter(fn (User $user) => $user->isAdmin()) as $adminIndex => $admin) {
            $this->actingAs($admin);
            foreach (range(1, 5) as $itemIndex) {
                $this->post(route('consumables.inventory.store'), [
                    'name' => "Matrix bit A{$adminIndex} I{$itemIndex}",
                    'code' => "MATRIX-A{$adminIndex}-I{$itemIndex}",
                    'category_id' => $stockCategory->id,
                    'unit' => 'pieces',
                    'unit_cost' => 10,
                    'minimum_stock' => 5,
                    'initial_quantity' => 100,
                ])->assertRedirect(route('consumables.inventory.index'));
            }
        }
        $stockItem = Consumable::firstOrFail();

        foreach ($users as $userIndex => $user) {
            foreach (range(1, 5) as $recordIndex) {
                $suffix = sprintf('U%02d-R%02d', $userIndex + 1, $recordIndex);
                Borehole::create([
                    'borehole_serial_no' => "MATRIX-BH-{$suffix}",
                    'province' => 'Midlands Province',
                    'admin_district' => 'North District',
                    'locality' => "Village {$suffix}",
                    'village' => "Village {$suffix}",
                    'ward' => (string) $recordIndex,
                    'date' => '2026-09-20',
                    'created_by' => $user->id,
                    'latitude' => -19.2,
                    'longitude' => 29.8,
                    'blowing_yield' => 2.5,
                ]);
                Dam::create([
                    'name' => "Matrix Dam {$suffix}",
                    'village' => "Village {$suffix}",
                    'ward' => (string) $recordIndex,
                    'district' => 'North District',
                    'province' => 'Midlands Province',
                    'wall_condition' => 'good',
                    'spillway_condition' => 'good',
                    'created_by' => $user->id,
                ]);
                PipedWaterScheme::create([
                    'name' => "Matrix Piped {$suffix}",
                    'village' => "Village {$suffix}",
                    'ward' => (string) $recordIndex,
                    'water_source' => 'borehole',
                    'year_established' => 2020,
                    'established_by' => 'District Council',
                    'pump_type' => 'Submersible',
                    'power_type' => 'solar',
                    'network_length' => 120,
                    'storage_tanks' => 15,
                    'stand_pipes_count' => 8,
                    'functionality' => 'Functional',
                    'households_served' => 24,
                    'population_served' => 120,
                    'women_served' => 60,
                    'men_served' => 40,
                    'children_served' => 20,
                    'created_by' => $user->id,
                ]);
                IrrigationScheme::create([
                    'name' => "Matrix Irrigation {$suffix}",
                    'village' => "Village {$suffix}",
                    'ward' => (string) $recordIndex,
                    'irrigation_type' => 'Drip',
                    'water_source_type' => 'Borehole',
                    'functionality' => 'Functional',
                    'created_by' => $user->id,
                ]);
                WaterPoint::create([
                    'name' => "Matrix Water Point {$suffix}",
                    'village_name' => "Village {$suffix}",
                    'ward' => (string) $recordIndex,
                    'type_of_water_source' => 'Borehole',
                    'functionality' => 'Functional',
                    'created_by' => $user->id,
                ]);
            }

            $this->actingAs($user);
            if (!$user->isViewer()) {
                $this->submitFiveAssetRecords($user, $userIndex);
            }

            foreach (range(1, 5) as $recordIndex) {
                $this->post(route('tickets.store'), [
                    'title' => "Matrix ticket U{$userIndex} R{$recordIndex}",
                    'category' => 'technical',
                    'priority' => 'normal',
                    'description' => 'Role-matrix verification request.',
                ])->assertRedirect();
            }

            if (!$user->isViewer()) {
                foreach (range(1, 5) as $recordIndex) {
                    $this->post(route('consumables.stock-requests.store'), [
                        'project_type' => 'borehole',
                        'project_name' => "Matrix stock project U{$userIndex} R{$recordIndex}",
                        'village' => "Village U{$userIndex}",
                        'ward' => (string) $recordIndex,
                        'purpose' => 'Role-matrix stock request.',
                        'items' => [['consumable_id' => $stockItem->id, 'quantity' => 1]],
                    ])->assertRedirect(route('consumables.stock-requests.index'));
                }
            }

            if (!$user->isViewer()) {
                foreach (range(1, 5) as $recordIndex) {
                    $this->post(route('consumables.usage-reports.store'), [
                        'project_type' => 'borehole',
                        'project_name' => "Matrix usage project U{$userIndex} R{$recordIndex}",
                        'village' => "Village U{$userIndex}",
                        'ward' => (string) $recordIndex,
                        'work_description' => 'Role-matrix usage report.',
                        'maintenance_type' => 'installation',
                        'date_of_work' => '2026-09-20',
                        'items' => [['consumable_id' => $stockItem->id, 'quantity_used' => 1]],
                    ])->assertRedirect(route('consumables.usage-reports.index'));
                }
            }

            if (!$user->isViewer()) {
                foreach (range(1, 5) as $recordIndex) {
                    $this->post(route('prospecting.store'), [
                        'project_name' => "Matrix project U{$userIndex} R{$recordIndex}",
                        'village' => "Village U{$userIndex}",
                        'ward' => (string) $recordIndex,
                        'district' => 'North District',
                        'province' => 'Midlands Province',
                        'survey_date' => '2026-09-20',
                        'siting_agency' => 'Government',
                        'prospector_name' => $user->name,
                    ])->assertRedirect();
                }
            }

            if ($user->isAdmin()) {
                foreach (range(1, 5) as $recordIndex) {
                    $this->post(route('consumables.suppliers.store'), [
                        'name' => "Matrix Supplier U{$userIndex} R{$recordIndex}",
                        'contact_person' => "Contact {$recordIndex}",
                        'email' => "supplier-u{$userIndex}-r{$recordIndex}@example.test",
                        'phone' => '07700000001',
                        'address' => 'Matrix test address',
                    ])->assertRedirect(route('consumables.suppliers.index'));

                    $this->post(route('clients.store'), [
                        'name' => "Matrix client U{$userIndex} R{$recordIndex}",
                        'province' => 'Midlands Province',
                        'district' => 'North District',
                    ])->assertRedirect();
                    $client = Client::where('name', "Matrix client U{$userIndex} R{$recordIndex}")->firstOrFail();

                    $this->post(route('invoices.store'), [
                        'client_id' => $client->id,
                        'issue_date' => '2026-09-20',
                        'due_date' => '2026-10-20',
                        'currency' => 'USD',
                        'tax_amount' => 0,
                        'status' => 'issued',
                        'items' => [['description' => 'Inspection', 'quantity' => 1, 'unit_price' => 10]],
                    ])->assertRedirect();
                }
            }
        }

        $this->assertDatabaseCount('boreholes', 95);
        $this->assertDatabaseCount('dams', 95);
        $this->assertDatabaseCount('piped_water_schemes', 95);
        $this->assertDatabaseCount('irrigation_schemes', 95);
        $this->assertDatabaseCount('water_points', 95);
        $this->assertDatabaseCount('dam_maintenance', 40);
        $this->assertDatabaseCount('irrigation_maintenances', 40);
        $this->assertDatabaseCount('consumables', 20);
        $this->assertDatabaseCount('inventory', 20);
        $this->assertDatabaseCount('stock_requests', 40);
        $this->assertDatabaseCount('usage_reports', 40);
        $this->assertDatabaseCount('support_tickets', 55);
        $this->assertDatabaseCount('prospecting_sites', 40);
        $this->assertDatabaseCount('clients', 20);
        $this->assertDatabaseCount('invoices', 20);
        $this->assertDatabaseCount('suppliers', 20);

        foreach ($users as $userIndex => $user) {
            $this->actingAs($user);
            $suffix = sprintf('U%02d-R01', $userIndex + 1);
            $visibleRecordCount = $user->isOperator() ? 10 : 95;

            $this->get(route('boreholes.index'))->assertOk();
            $this->get(route('dams.index'))->assertOk();
            $this->get(route('piped-water-schemes.index'))->assertOk();
            $this->get(route('irrigation-schemes.index'))->assertOk();
            $this->get(route('water-points.index'))->assertOk();
            $this->get(route('tickets.index'))->assertOk();
            $this->get(route('prospecting.index'))->assertOk();
            $this->get(route('consumables.inventory.index'))->assertOk();
            $this->get(route('consumables.stock-requests.index'))->assertOk();
            $this->get(route('consumables.usage-reports.index'))->assertOk();
            $mapRecordCount = $visibleRecordCount;
            $this->assertCount($mapRecordCount, $this->getJson(route('api.boreholes.map-data'))->assertOk()->json('features'));
            $this->getJson('/api/dashboard/chart-data')
                ->assertOk()
                ->assertJsonPath('boreholes', $visibleRecordCount)
                ->assertJsonPath('dams', $visibleRecordCount)
                ->assertJsonPath('piped_water_schemes', $visibleRecordCount);

            $ownedBorehole = Borehole::where('borehole_serial_no', "MATRIX-BH-{$suffix}")->firstOrFail();
            $foreignBorehole = Borehole::where('created_by', '!=', $user->id)->firstOrFail();
            $this->get(route('boreholes.show', $ownedBorehole))->assertOk();
            if ($user->isOperator()) {
                $this->get(route('boreholes.show', $foreignBorehole))->assertForbidden();
                $this->get(route('dams.show', Dam::where('created_by', '!=', $user->id)->firstOrFail()))->assertForbidden();
                $this->get(route('piped-water-schemes.show', PipedWaterScheme::where('created_by', '!=', $user->id)->firstOrFail()))->assertForbidden();
                $this->get(route('irrigation-schemes.show', IrrigationScheme::where('created_by', '!=', $user->id)->firstOrFail()))->assertForbidden();
                $this->get(route('water-points.show', WaterPoint::where('created_by', '!=', $user->id)->firstOrFail()))->assertForbidden();
                $this->assertSame(10, (new BoreholesExport(['created_by' => $user->id]))->collection()->count());
            } else {
                $this->get(route('boreholes.show', $foreignBorehole))->assertOk();
            }

            if ($user->isViewer()) {
                $this->get(route('boreholes.create'))->assertForbidden();
                $this->get(route('dams.create'))->assertForbidden();
                $this->get(route('irrigation-schemes.create'))->assertForbidden();
                $this->get(route('piped-water-schemes.create'))->assertForbidden();
                $this->get(route('water-points.create'))->assertForbidden();
                $this->get(route('prospecting.create'))->assertForbidden();
                $this->get(route('consumables.inventory.create'))->assertForbidden();
                $this->get(route('consumables.stock-requests.create'))->assertForbidden();
                $this->post(route('consumables.stock-requests.store'), [])->assertForbidden();
                $this->post(route('consumables.usage-reports.store'), [])->assertForbidden();
            }

            if ($user->isAdmin()) {
                $this->get(route('clients.index'))->assertOk();
                $this->get(route('invoices.index'))->assertOk();
                $this->get(route('consumables.suppliers.index'))->assertOk();
            } else {
                $this->get(route('clients.index'))->assertForbidden();
                $this->get(route('invoices.index'))->assertForbidden();
                $this->get(route('consumables.suppliers.index'))->assertForbidden();
            }
        }
    }

    private function submitFiveAssetRecords(User $user, int $userIndex): void
    {
        foreach (range(1, 5) as $recordIndex) {
            $suffix = sprintf('U%02d-F%02d', $userIndex + 1, $recordIndex);
            $this->postJson(route('boreholes.store'), [
                'borehole_serial_no' => "FORM-BH-{$suffix}",
                'province' => 'Midlands Province',
                'admin_district' => 'North District',
                'locality' => "Form Village {$suffix}",
                'village' => "Form Village {$suffix}",
                'ward' => (string) $recordIndex,
                'date' => '2026-09-20',
                'latitude' => -19.2,
                'longitude' => 29.8,
                'blowing_yield' => 2.5,
            ])->assertCreated();

            $this->post(route('dams.store'), [
                'name' => "Form Dam {$suffix}",
                'village' => "Form Village {$suffix}",
                'ward' => (string) $recordIndex,
                'district' => 'North District',
                'province' => 'Midlands Province',
                'wall_condition' => 'good',
                'spillway_condition' => 'good',
                'capacity' => 100,
            ])->assertRedirect();

            $dam = Dam::where('name', "Form Dam {$suffix}")->firstOrFail();
            $this->post(route('dams.maintenance.store', $dam), [
                'maintenance_type' => 'routine',
                'maintenance_date' => '2026-09-20',
                'status' => 'completed',
                'wall_repair_details' => "Inspection {$recordIndex}",
            ])->assertRedirect(route('dams.maintenance.index', $dam));

            $this->post(route('piped-water-schemes.store'), [
                'name' => "Form Piped {$suffix}",
                'village' => "Form Village {$suffix}",
                'ward' => (string) $recordIndex,
                'water_source' => 'borehole',
                'year_established' => 2020,
                'established_by' => 'District Council',
                'pump_type' => 'Submersible',
                'power_type' => 'solar',
                'network_length' => 120,
                'storage_tanks' => 15,
                'stand_pipes_count' => 8,
                'functionality' => 'Functional',
                'households_served' => 24,
                'population_served' => 120,
                'women_served' => 60,
                'men_served' => 40,
                'children_served' => 20,
            ])->assertRedirect(route('piped-water-schemes.index'));

            $this->post(route('irrigation-schemes.store'), [
                'name' => "Form Irrigation {$suffix}",
                'village' => "Form Village {$suffix}",
                'ward' => (string) $recordIndex,
                'irrigation_type' => 'Drip',
                'water_source_type' => 'Borehole',
                'functionality' => 'Functional',
            ])->assertRedirect(route('irrigation-schemes.index'));

            $scheme = IrrigationScheme::where('name', "Form Irrigation {$suffix}")->firstOrFail();
            $this->post(route('irrigation-maintenances.store'), [
                'irrigation_scheme_id' => $scheme->id,
                'repaired_by' => $user->name,
                'works_done' => "Maintenance {$recordIndex}",
                'date_repaired' => '2026-09-20',
            ])->assertRedirect(route('irrigation-maintenances.index'));

            $this->post(route('water-points.store'), [
                'name' => "Form Water Point {$suffix}",
                'village_name' => "Form Village {$suffix}",
                'ward' => (string) $recordIndex,
                'type_of_water_source' => 'Borehole',
                'functionality' => 'Functional',
            ])->assertRedirect(route('water-points.index'));
        }
    }
}