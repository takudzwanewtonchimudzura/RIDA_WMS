<?php

namespace Database\Seeders;

use App\Models\Borehole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BoreholeSeeder extends Seeder
{
    public function run(): void
    {
        if (Borehole::count() >= 20) {
            $this->command?->info('Boreholes already seeded, skipping.');
            return;
        }

        // Get some users to assign as creators
        $users = User::limit(5)->get();
        $userIds = $users->pluck('id')->toArray();

        // Zimbabwean provinces, districts, and villages
        $provinces = config('zimbabwe.provinces');
        $bounds = config('zimbabwe.bounds');

        $boreholes = [];

        // Create 20 boreholes
        for ($i = 1; $i <= 20; $i++) {
            $province = array_rand($provinces);
            $district = $provinces[$province][array_rand($provinces[$province])];
            $village = 'Village ' . chr(65 + rand(0, 25)) . rand(1, 10);
            $ward = 'Ward ' . rand(1, 30);

            $boreholes[] = [
                'borehole_serial_no' => 'BH-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'province' => $province,
                'admin_district' => $district,
                'locality' => $village,
                'ward' => $ward,
                'vidco' => 'VIDCO' . rand(1, 10),
                'usage' => ['Domestic', 'Livestock', 'Irrigation'][rand(0, 2)],
                'dth_bit_serial_no' => 'BIT-' . rand(1000, 9999),
                'hammer_serial_no' => 'HAM-' . rand(1000, 9999),
                'date' => Carbon::now()->subDays(rand(1, 365)),
                
                // Location
                'latitude' => $bounds['lat_min'] + (rand(0, 1000) / 1000) * ($bounds['lat_max'] - $bounds['lat_min']),
                'longitude' => $bounds['lng_min'] + (rand(0, 1000) / 1000) * ($bounds['lng_max'] - $bounds['lng_min']),
                'grid_reference' => 'GRID' . rand(1000, 9999),
                'elevation' => rand(800, 1500),
                
                // Drilling Details
                'first_drilled_diameter_mm' => 250,
                'second_drilled_diameter_mm' => 200,
                'third_drilled_diameter_mm' => 150,
                'final_borehole_depth_m' => rand(80, 200),
                'yield_lps' => rand(5, 25) / 10,
                'development_hours' => rand(24, 72),
                
                // Gravel Packing
                'gravel_packing_from_m' => 10,
                'gravel_packing_to_m' => 50,
                'gravel_type' => ['Quartz', 'River Gravel', 'Crushed Stone'][rand(0, 2)],
                'gravel_thickness_m' => 40,
                
                // Cement Sealing
                'cement_sealing_from_m' => 0,
                'cement_sealing_to_m' => 10,
                'cement_type' => 'OPC',
                'cement_volume_bags' => rand(20, 50),
                
                // Water Quality
                'tds_mg_l' => rand(200, 800),
                'ph' => rand(65, 80) / 10,
                'turbidity_ntu' => rand(1, 10),
                'electrical_conductivity' => rand(300, 1200),
                'water_color' => ['Clear', 'Slightly Cloudy', 'Brownish'][rand(0, 2)],
                'odor' => ['None', 'Slight', 'Earth'][rand(0, 2)],
                'taste' => ['Good', 'Slightly Salty', 'Metallic'][rand(0, 2)],
                'water_quality_remarks' => 'Water quality acceptable for domestic use',
                
                // Pump Installation
                'pipe_length_m' => rand(60, 120),
                'cylinder_size_m' => 3,
                'swl_m' => rand(10, 30),
                'dwl_m' => rand(35, 80),
                'pump_type' => ['India Mark II', 'Afridev', 'Volanta'][rand(0, 2)],
                'pump_model' => 'Model-' . rand(100, 300),
                'pump_depth_m' => rand(40, 90),
                
                // Project Information
                'funding_agency' => ['World Bank', 'UNICEF', 'Government', 'NGO'][rand(0, 3)],
                'project_name' => 'Water Project ' . rand(1, 5),
                'contractor' => ['Zimbabwe Drilling Co.', 'Water Wells Ltd.', 'Aqua Drillers Zimbabwe'][rand(0, 2)],
                'number_of_beneficiaries' => rand(500, 2000),
                'total_cost' => rand(5000000, 20000000),
                'cost_currency' => 'USD',
                
                // System Fields
                'created_by' => $userIds[array_rand($userIds)],
                'approved_by' => $userIds[array_rand($userIds)],
                'approved_at' => Carbon::now()->subDays(rand(1, 30)),
                'created_at' => Carbon::now()->subDays(rand(31, 90)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }

        Borehole::insert($boreholes);
        echo "✅ Boreholes seeded successfully! (20 records)\n";
    }
}