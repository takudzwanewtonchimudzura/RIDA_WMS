<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Consumable;
use App\Models\ConsumableCategory;
use App\Models\Dam;
use App\Models\DamMaintenance;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\ProspectingSite;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ZimbabweLocations;
use Illuminate\Database\Seeder;

class UatScenarioSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('UAT scenarios must not be loaded in production.');
        }
        $operator = User::where('role', 'operator')->firstOrFail();
        $supervisor = User::where('role', 'supervisor')->firstOrFail();
        $finance = User::where('role', 'finance')->firstOrFail();
        $stores = User::where('role', 'stores')->firstOrFail();

        $category = ConsumableCategory::firstOrCreate(['name' => 'UAT - Drilling and Maintenance'], ['description' => 'Sample stock for workshop workflows']);
        $supplier = Supplier::firstOrCreate(['name' => 'UAT - District Engineering Supplies'], [
            'contact_person' => 'UAT Supplier Contact', 'phone' => 'UAT sample', 'address' => 'Harare depot (sample)',
            'status' => 'active',
        ]);
        foreach ([
            ['Diesel', 'litres', 1.65, 800, 100],
            ['Hydraulic oil', 'litres', 5.50, 120, 20],
            ['Pump seals', 'sets', 18, 5, 10],
            ['PVC casing 150 mm', 'metres', 24, 240, 50],
            ['Hand pump rods', 'pieces', 35, 60, 15],
            ['Cement 50 kg', 'bags', 12, 80, 20],
        ] as $i => [$name, $unit, $cost, $quantity, $minimum]) {
            $consumable = Consumable::firstOrCreate(['code' => 'UAT-STOCK-' . ($i + 1)], [
                'name' => $name . ' (UAT)', 'category_id' => $category->id, 'unit' => $unit,
                'unit_cost' => $cost, 'minimum_stock' => $minimum, 'status' => 'active',
            ]);
            $inventory = Inventory::firstOrCreate(['consumable_id' => $consumable->id], [
                'quantity' => $quantity, 'location' => 'Harare central stores', 'supplier_id' => $supplier->id,
                'batch_number' => 'UAT-OPENING', 'notes' => 'UAT opening stock',
            ]);
            if ($inventory->wasRecentlyCreated) {
                $inventory->movements()->create([
                    'user_id' => $stores->id, 'movement_type' => 'opening',
                    'quantity_delta' => $quantity, 'reason' => 'UAT opening stock',
                ]);
            }
        }

        $location = app(ZimbabweLocations::class)->sample(20);
        $client = Client::firstOrCreate(['name' => 'UAT - Community Water Programme'], [
            'contact_person' => 'UAT Project Coordinator', 'province' => $location['province'],
            'district' => $location['district'], 'created_by' => $finance->id, 'is_active' => true,
        ]);
        foreach (['draft', 'issued', 'issued', 'issued', 'issued'] as $i => $status) {
            $total = 1500 + $i * 500;
            $paid = $i === 2 ? 500 : ($i === 3 ? $total : 0);
            $invoice = Invoice::firstOrCreate(['invoice_number' => 'UAT-INV-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)], [
                'client_id' => $client->id, 'issue_date' => today()->subDays(30),
                'due_date' => $i === 4 ? today()->subDays(5) : today()->addDays(15),
                'currency' => 'USD', 'subtotal' => $total, 'tax_amount' => 0, 'total' => $total,
                'amount_paid' => $paid, 'status' => $status, 'created_by' => $finance->id,
                'notes' => 'UAT sample invoice; not a real financial obligation.',
            ]);
            if ($invoice->wasRecentlyCreated) {
                $invoice->items()->create([
                    'description' => 'UAT borehole drilling and pump service', 'quantity' => 1,
                    'unit_price' => $total, 'amount' => $total,
                ]);
                if ($paid > 0) {
                    $invoice->payments()->create([
                        'payment_date' => today()->subDays(2), 'amount' => $paid, 'method' => 'bank_transfer',
                        'reference' => 'UAT-PAY-' . ($i + 1), 'recorded_by' => $finance->id,
                    ]);
                }
            }
        }
        foreach (Dam::orderBy('id')->limit(3)->get() as $i => $dam) {
            foreach (['completed', 'planned'] as $status) {
                DamMaintenance::firstOrCreate(['dam_id' => $dam->id, 'general_comments' => 'UAT sample: ' . $status . ' routine maintenance'], [
                    'maintenance_type' => 'routine', 'maintenance_date' => $status === 'completed' ? today()->subDays(14 + $i) : today()->addDays(7 + $i),
                    'wall_repair_details' => 'Inspect embankment, clear erosion channels and repair minor surface damage.',
                    'spillway_repair_details' => 'Remove debris and check concrete joints.',
                    'estimated_cost' => 450 + $i * 100, 'actual_cost' => $status === 'completed' ? 425 + $i * 100 : null,
                    'status' => $status, 'created_by' => $operator->id,
                ]);
            }
        }
        foreach (['submitted', 'approved', 'drilled'] as $i => $status) {
            $location = app(ZimbabweLocations::class)->sample($i + 25);
            ProspectingSite::firstOrCreate(['reference' => 'UAT-PR-00' . ($i + 1)], $location + [
                'village' => 'UAT Community ' . ($i + 1), 'survey_date' => today()->subDays(10),
                'submitted_at' => now()->subDays(9), 'status' => $status, 'created_by' => $operator->id,
                'reviewed_by' => $status !== 'submitted' ? $supervisor->id : null,
                'reviewed_at' => $status !== 'submitted' ? now()->subDays(7) : null,
                'drilled_by' => $status === 'drilled' ? $operator->id : null,
                'drilled_at' => $status === 'drilled' ? now()->subDays(2) : null,
                'remarks' => 'UAT sample prospecting workflow',
            ]);
        }
    }
}
