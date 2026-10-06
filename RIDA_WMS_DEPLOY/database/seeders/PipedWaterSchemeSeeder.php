<?php

namespace Database\Seeders;

use App\Models\PipedWaterScheme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PipedWaterSchemeSeeder extends Seeder
{
    public function run(): void
    {
        if (PipedWaterScheme::count() >= 15) {
            $this->command?->info('Piped water schemes already seeded, skipping.');
            return;
        }

        $users = User::all();
        $userIds = $users->pluck('id')->toArray();

        $schemes = [];

        // Real Zimbabwean districts used as representative ward/scheme names
        $districts = collect(config('zimbabwe.provinces'))->flatten()->values()->all();

        // Create 15 piped water schemes
        for ($i = 1; $i <= 15; $i++) {
            $ward = $districts[array_rand($districts)];
            
            $functionality = ['Functional', 'Partially Functional', 'Non functional'][rand(0, 2)];
            $hasCommittee = rand(0, 1);
            $hasFund = rand(0, 1);

            $schemes[] = [
                'name' => $ward . ' Piped Water Scheme ' . $i,
                'village' => 'Village ' . chr(65 + $i),
                'ward' => $ward,
                
                // Water Source
                'water_source' => ['borehole', 'spring', 'river', 'dam'][rand(0, 3)],
                'year_established' => rand(2015, 2022),
                'established_by' => ['Government', 'NGO', 'Community'][rand(0, 2)],
                
                // Technical Details
                'pump_type' => ['Submersible', 'Centrifugal', 'Jet Pump'][rand(0, 2)],
                'power_type' => ['solar', 'electricity', 'fossil fuel'][rand(0, 2)],
                'network_length' => rand(5000, 25000),
                'storage_tanks' => rand(50, 200),
                'stand_pipes_count' => rand(10, 50),
                
                // Functionality
                'functionality' => $functionality,
                'functionality_issues' => $functionality != 'Functional' ? 'Needs maintenance' : null,
                
                // Service Details
                'households_served' => rand(200, 1000),
                'population_served' => rand(1000, 5000),
                'women_served' => rand(400, 2000),
                'men_served' => rand(400, 2000),
                'children_served' => rand(200, 1000),
                
                // Management Committee
                'has_management_committee' => $hasCommittee,
                'committee_trained' => $hasCommittee ? rand(0, 1) : 0,
                'committee_functional' => $hasCommittee ? rand(0, 1) : 0,
                'has_revolving_fund' => $hasFund,
                'fund_amount' => $hasFund ? rand(500000, 5000000) : null,
                'committee_members_total' => $hasCommittee ? rand(7, 15) : null,
                'committee_women' => $hasCommittee ? rand(3, 8) : null,
                'committee_men' => $hasCommittee ? rand(4, 10) : null,
                
                // System Fields
                'created_by' => $userIds[array_rand($userIds)],
                'remarks' => 'Scheme serving ' . $ward . ' community',
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }

        PipedWaterScheme::insert($schemes);
        echo "✅ Piped Water Schemes seeded successfully! (15 records)\n";
    }
}