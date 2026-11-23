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

        $users = User::where('role', 'operator')->get();
        $userIds = $users->pluck('id')->toArray();
        $approver = User::where('role', 'supervisor')->firstOrFail();

        $provinces = config('zimbabwe.provinces');
        $bounds = config('zimbabwe.bounds');

        $dams = [];

        // Create 8 dams
        for ($i = 1; $i <= 8; $i++) {
            $location = app(\App\Services\ZimbabweLocations::class)->sample($i);
            $province = $location['province'];
            $district = $location['district'];

            $dams[] = [
                'name' => 'Dam ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'dam_code' => 'UAT-DAM-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'village' => 'Village ' . chr(65 + $i),
                'ward' => $location['ward'],
                'district' => $district,
                'province' => $province,
                
                // Location
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                
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
                'approved_by' => $approver->id,
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