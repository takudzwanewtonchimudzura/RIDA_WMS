<?php

namespace Tests\Feature;

use App\Models\Borehole;
use App\Models\Dam;
use App\Models\IrrigationScheme;
use App\Models\PipedWaterScheme;
use App\Models\User;
use App\Models\WaterPoint;
use Database\Seeders\BoreholeSeeder;
use Database\Seeders\DamSeeder;
use Database\Seeders\DemoUserSeeder;
use Database\Seeders\IrrigationSchemeSeeder;
use Database\Seeders\PipedWaterSchemeSeeder;
use Database\Seeders\WaterPointSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeders_populate_at_least_five_records_per_asset_type(): void
    {
        $this->seed(DemoUserSeeder::class);
        $this->seed(BoreholeSeeder::class);
        $this->seed(DamSeeder::class);
        $this->seed(IrrigationSchemeSeeder::class);
        $this->seed(PipedWaterSchemeSeeder::class);
        $this->seed(WaterPointSeeder::class);

        $this->assertGreaterThanOrEqual(5, User::count());
        $this->assertGreaterThanOrEqual(5, Borehole::count());
        $this->assertGreaterThanOrEqual(5, Dam::count());
        $this->assertGreaterThanOrEqual(5, IrrigationScheme::count());
        $this->assertGreaterThanOrEqual(5, PipedWaterScheme::count());
        $this->assertGreaterThanOrEqual(5, WaterPoint::count());

        foreach (User::pluck('email') as $email) {
            $this->assertStringEndsWith('@rida.gov.zw', $email);
        }
    }

    public function test_reseeding_does_not_duplicate_demo_records(): void
    {
        $this->seed(DemoUserSeeder::class);
        $this->seed(BoreholeSeeder::class);
        $this->seed(BoreholeSeeder::class);

        $this->assertSame(20, Borehole::count());
    }
}
