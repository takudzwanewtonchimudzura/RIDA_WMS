<?php

namespace Database\Seeders;

use App\Models\Dam;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DamSeeder extends Seeder
{
    public function run(): void
    {
        if (Dam::count() >= 8) {
            $this->command?->info('Dams already seeded, skipping.');
            return;
        }

        $users = User::all();
        $userIds = $users->pluck('id')->toArray();

        $provinces = config('zimbabwe.provinces');
        $bounds = config('zimbabwe.bounds');

        $dams = [];

        // Create 8 dams
        for ($i = 1; $i <= 8; $i++) {
            $province = array_rand($provinces);
            $district = $provinces[$province][array_rand($provinces[$province])];

            $dams[] = [
                'name' => 'Dam ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'village' => 'Village ' . chr(65 + $i),
                'ward' => 'Ward ' . rand(1, 8),
                'district' => $district,
                'province' => $province,
                
                // Location
                'latitude' => $bounds['lat_min'] + (rand(0, 1000) / 1000) * ($bounds['lat_max'] - $bounds['lat_min']),
                'longitude' => $bounds['lng_min'] + (rand(0, 1000) / 1000) * ($bounds['lng_max'] - $bounds['lng_min']),
                
                // Dam Wall Details
                'wall_length' => rand(50, 200),
                'wall_height' => rand(10, 30),
                'water_depth' => rand(5, 25),
                'wall_condition' => ['good', 'fair', 'poor'][rand(0, 2)],
                
                // Spillway Details
                'spillway_width' => rand(10, 50),
                'spillway_type' => ['Natural', 'Concrete', 'Rocks'][rand(0, 2)],
                'spillway_condition' => ['good', 'fair', 'poor'][rand(0, 2)],
                'throwback' => rand(20, 100),
                
                // Capacity Details
                'capacity' => rand(50000, 500000),
                'wall_volume' => rand(1000, 10000),
                
                // Structural Features
                'has_stone_pitching' => rand(0, 1),
                'has_star_grass' => rand(0, 1),
                
                // System Fields
                'created_by' => $userIds[array_rand($userIds)],
                'approved_by' => $userIds[array_rand($userIds)],
                'approved_at' => Carbon::now()->subDays(rand(1, 30)),
                'general_comments' => 'Dam used for irrigation and livestock',
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }

        Dam::insert($dams);
        echo "✅ Dams seeded successfully! (8 records)\n";
    }
}