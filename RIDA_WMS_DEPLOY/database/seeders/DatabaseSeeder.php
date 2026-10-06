<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(DemoUserSeeder::class);
        $this->call(BoreholeSeeder::class);
        $this->call(DamSeeder::class);
        $this->call(IrrigationSchemeSeeder::class);
        $this->call(PipedWaterSchemeSeeder::class);
        $this->call(WaterPointSeeder::class);
    }
}