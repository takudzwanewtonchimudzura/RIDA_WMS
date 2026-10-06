<?php

namespace Database\Seeders;

use App\Models\WaterPoint;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class WaterPointSeeder extends Seeder
{
    public function run(): void
    {
        if (WaterPoint::count() >= 50) {
            $this->command?->info('Water points already seeded, skipping.');
            return;
        }

        // Get existing users to assign as creators
        $users = User::all();
        $userIds = $users->pluck('id')->toArray();

        $waterPoints = [];
        
        // Status options
        $functionalities = ['Functional', 'Partially Functional', 'Nonfunctional'];
        $conditions = ['good', 'bad', 'needs attention'];
        $sourceTypes = ['Borehole', 'Well', 'Spring', 'River'];
        $seasons = ['Year-round', 'Seasonal', 'Dry season only'];
        $bounds = config('zimbabwe.bounds');

        // Create 50 water points
        for ($i = 1; $i <= 50; $i++) {
            $functionality = $functionalities[array_rand($functionalities)];
            $condition = $conditions[array_rand($conditions)];

            $waterPoints[] = [
                'name' => 'Water Point ' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'village_name' => 'Village ' . chr(65 + rand(0, 25)) . rand(1, 10),
                'ward' => 'Ward ' . rand(1, 10),
                
                // Location
                'latitude' => $bounds['lat_min'] + (rand(0, 1000) / 1000) * ($bounds['lat_max'] - $bounds['lat_min']),
                'longitude' => $bounds['lng_min'] + (rand(0, 1000) / 1000) * ($bounds['lng_max'] - $bounds['lng_min']),
                
                // Source Details
                'type_of_water_source' => $sourceTypes[array_rand($sourceTypes)],
                'year_drilled' => rand(2010, 2023),
                'seasonality' => $seasons[array_rand($seasons)],
                'type_of_lifting_device' => ['Hand Pump', 'Solar Pump', 'Electric Pump', 'Gravity'][rand(0, 3)],
                
                // Functionality
                'functionality' => $functionality,
                'strokes_before_water' => $functionality == 'Functional' ? rand(5, 20) : rand(30, 50),
                'strokes_to_fill_20l' => $functionality == 'Functional' ? rand(20, 40) : rand(50, 100),
                'number_of_pipes' => rand(1, 3),
                
                // Infrastructure Condition
                'apron_condition' => $condition,
                'runoff_channel_condition' => $condition,
                'soakaway_condition' => $condition,
                
                // Service Details
                'households_served' => rand(50, 300),
                'population_served' => rand(250, 1500),
                'women_served' => rand(100, 600),
                'men_served' => rand(100, 600),
                'children_below_18' => rand(50, 300),
                
                // Management Committee
                'has_water_committee' => rand(0, 1),
                'committee_functional' => rand(0, 1),
                'committee_total_members' => rand(5, 15),
                'committee_women' => rand(2, 8),
                'committee_men' => rand(3, 10),
                
                // Maintenance History
                'last_repaired_by' => ['District Water Office', 'Community', 'NGO'][rand(0, 2)],
                'last_spares_fitted' => ['Piston', 'Seals', 'Pipes', 'Handle'][rand(0, 3)],
                'last_repair_date' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 180)) : null,
                
                // System Fields
                'created_by' => $userIds[array_rand($userIds)],
                'approved_by' => $functionality == 'Functional' ? $userIds[array_rand($userIds)] : null,
                'approved_at' => $functionality == 'Functional' ? Carbon::now()->subDays(rand(1, 30)) : null,
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }

        WaterPoint::insert($waterPoints);
        echo "✅ Water Points seeded successfully! (50 records)\n";
    }
}