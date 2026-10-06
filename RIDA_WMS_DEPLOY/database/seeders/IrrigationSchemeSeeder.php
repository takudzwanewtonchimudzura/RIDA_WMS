<?php

namespace Database\Seeders;

use App\Models\IrrigationScheme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class IrrigationSchemeSeeder extends Seeder
{
    public function run(): void
    {
        if (IrrigationScheme::count() >= 10) {
            $this->command?->info('Irrigation schemes already seeded, skipping.');
            return;
        }

        $users = User::all();
        $userIds = $users->pluck('id')->toArray();

        $schemes = [];

        // Create 10 irrigation schemes
        for ($i = 1; $i <= 10; $i++) {
            $hasCommittee = rand(0, 1);
            $hasFund = rand(0, 1);
            $functionality = ['Functional', 'Partially Functional', 'Nonfunctional'][rand(0, 2)];

            $schemes[] = [
                'name' => 'Irrigation Scheme ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'village' => 'Village ' . chr(65 + $i),
                'ward' => 'Ward ' . rand(1, 10),
                
                // Irrigation Details
                'irrigation_type' => ['Sprinkler', 'Drip', 'Canal', 'Flood'][rand(0, 3)],
                'water_source_type' => ['Borehole', 'River', 'Dam', 'Lake'][rand(0, 3)],
                'year_established' => rand(2018, 2023),
                'established_by' => ['Government', 'NGO', 'Private'][rand(0, 2)],
                
                // Technical Details
                'pump_type' => ['Submersible', 'Centrifugal', 'Turbine'][rand(0, 2)],
                'power_type' => ['Solar', 'Diesel', 'Electricity'][rand(0, 2)],
                'size_hectares' => rand(10, 100),
                'beneficiary_households' => rand(20, 100),
                'plot_size' => rand(0.5, 2),
                'crop_pattern' => implode(', ', ['Maize', 'Vegetables', 'Rice', 'Fruits']),
                'total_storage_tanks_m3' => rand(20, 100),
                
                // Functionality
                'functionality' => $functionality,
                'functionality_comments' => $functionality != 'Functional' ? 'Requires pump repair' : 'Operating normally',
                
                // Management Committee
                'has_management_committee' => $hasCommittee,
                'committee_trained' => $hasCommittee ? rand(0, 1) : 0,
                'committee_functional' => $hasCommittee ? rand(0, 1) : 0,
                'has_revolving_fund' => $hasFund,
                'fund_amount_available' => $hasFund ? rand(1000000, 10000000) : null,
                'committee_total_members' => $hasCommittee ? rand(5, 12) : null,
                'committee_women' => $hasCommittee ? rand(2, 6) : null,
                'committee_men' => $hasCommittee ? rand(3, 8) : null,
                
                // System Fields
                'created_by' => $userIds[array_rand($userIds)],
                'approved_by' => $userIds[array_rand($userIds)],
                'approved_at' => Carbon::now()->subDays(rand(1, 30)),
                'remarks' => 'Irrigation scheme for smallholder farmers',
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }

        IrrigationScheme::insert($schemes);
        echo "✅ Irrigation Schemes seeded successfully! (10 records)\n";
    }
}