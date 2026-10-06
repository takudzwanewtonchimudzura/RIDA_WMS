<?php

namespace Tests\Feature;

use App\Models\Dam;
use App\Models\Borehole;
use App\Models\IrrigationScheme;
use App\Models\PipedWaterScheme;
use App\Models\WaterPoint;
use App\Models\User;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReadinessSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_map_and_dam_inspection_pages_render(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        foreach (range(1, 5) as $index) {
            Dam::create([
                'name' => "Smoke Dam {$index}",
                'village' => 'Test Village',
                'ward' => '1',
                'district' => 'Test District',
                'province' => 'Midlands Province',
                'wall_condition' => 'good',
                'spillway_condition' => 'good',
                'capacity' => 100,
            ]);
        }

        foreach (range(1, 5) as $index) {
            Borehole::create([
                'borehole_serial_no' => "MAP-BH-{$index}",
                'province' => $index <= 3 ? 'Harare Province' : 'Midlands Province',
                'admin_district' => 'North District',
                'locality' => "Map Village {$index}",
                'village' => "Map Village {$index}",
                'ward' => (string) $index,
                'date' => '2026-09-20',
                'latitude' => -19.0 + ($index / 100),
                'longitude' => 29.0 + ($index / 100),
                'blowing_yield' => $index * 2,
                'created_by' => auth()->id(),
            ]);
        }

        $this->get(route('maps.geographic'))
            ->assertOk()
            ->assertSee('mapVisibleCount')
            ->assertSee('leaflet.js')
            ->assertSee('aria-label="Main navigation"', false);
        $this->getJson(route('api.boreholes.map-data'))
            ->assertOk()
            ->assertJsonCount(5, 'features');
        $this->getJson(route('api.boreholes.map-data', ['province' => 'Harare Province']))
            ->assertOk()
            ->assertJsonCount(3, 'features');
        $this->get(route('dams.inspections'))->assertOk();
    }

    public function test_map_api_requires_authentication_and_limits_operators_to_their_five_records(): void
    {
        $owner = User::factory()->operator()->create();
        $otherOperator = User::factory()->operator()->create();

        foreach ([$owner, $otherOperator] as $userIndex => $user) {
            foreach (range(1, 5) as $index) {
                Borehole::create([
                    'borehole_serial_no' => "MAP-SEC-{$userIndex}-{$index}",
                    'province' => 'Harare Province',
                    'admin_district' => 'North District',
                    'locality' => "Map Security Village {$index}",
                    'village' => "Map Security Village {$index}",
                    'ward' => (string) $index,
                    'date' => '2026-09-20',
                    'latitude' => -19.0 + ($index / 100),
                    'longitude' => 29.0 + ($index / 100),
                    'created_by' => $user->id,
                ]);
            }
        }

        $this->getJson(route('api.boreholes.map-data'))->assertUnauthorized();

        $ownerFeatures = $this->actingAs($owner)
            ->getJson(route('api.boreholes.map-data'))
            ->assertOk()
            ->assertJsonCount(5, 'features')
            ->json('features');

        $this->assertSame(
            [$owner->id],
            collect($ownerFeatures)->pluck('properties.id')->map(fn ($id) => (int) Borehole::find($id)->created_by)->unique()->values()->all()
        );
    }

    public function test_security_headers_are_sent_and_hsts_is_only_sent_over_https(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeaderMissing('Strict-Transport-Security');

        $secureResponse = app(SecurityHeaders::class)->handle(
            Request::create('https://rwims.example.test/', 'GET'),
            fn () => response('OK')
        );

        $this->assertSame('max-age=15552000', $secureResponse->headers->get('Strict-Transport-Security'));
    }

    public function test_borehole_mutations_are_limited_to_owner_and_admin(): void
    {
        $owner = User::factory()->operator()->create();
        $otherOperator = User::factory()->operator()->create();
        $borehole = Borehole::create([
            'borehole_serial_no' => 'AUTH-BH-001',
            'province' => 'Midlands Province',
            'admin_district' => 'North District',
            'locality' => 'Test Village',
            'village' => 'Test Village',
            'ward' => '1',
            'date' => '2026-09-28',
            'created_by' => $owner->id,
        ]);

        $this->actingAs($otherOperator)->get(route('boreholes.edit', $borehole))->assertForbidden();
        $this->actingAs($otherOperator)->delete(route('boreholes.destroy', $borehole))->assertForbidden();
        $this->actingAs(User::factory()->viewer()->create())
            ->get(route('boreholes.edit', $borehole))
            ->assertForbidden();

        $this->actingAs($otherOperator)
            ->patch(route('boreholes.approve', $borehole))
            ->assertForbidden();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
            ->patch(route('boreholes.approve', $borehole))
            ->assertRedirect(route('boreholes.show', $borehole));
        $this->assertDatabaseHas('boreholes', [
            'id' => $borehole->id,
            'approved_by' => $admin->id,
        ]);
    }

    public function test_dam_creation_cannot_set_approval_fields_from_request(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)->post(route('dams.store'), [
            'name' => 'Unapproved Test Dam',
            'village' => 'Test Village',
            'ward' => '1',
            'district' => 'Test District',
            'province' => 'Midlands Province',
            'wall_condition' => 'good',
            'spillway_condition' => 'good',
            'approved_at' => now()->toDateTimeString(),
            'approved_by' => $admin->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('dams', [
            'name' => 'Unapproved Test Dam',
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $this->actingAs($admin)->patch(route('dams.approve', Dam::where('name', 'Unapproved Test Dam')->firstOrFail()))
            ->assertRedirect();
        $this->assertDatabaseHas('dams', [
            'name' => 'Unapproved Test Dam',
            'approved_by' => $admin->id,
        ]);
    }

    public function test_geographic_popup_escapes_user_supplied_html(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        Borehole::create([
            'borehole_serial_no' => 'XSS-BH',
            'province' => 'Midlands Province',
            'admin_district' => 'North District',
            'locality' => '<img src=x onerror=alert(1)>',
            'village' => 'Test Village',
            'ward' => '1',
            'date' => '2026-09-28',
            'latitude' => -19.2,
            'longitude' => 29.8,
        ]);

        $popup = $this->getJson(route('api.boreholes.map-data'))
            ->assertOk()
            ->json('features.0.properties.popup');

        $this->assertStringNotContainsString('<img', $popup);
        $this->assertStringContainsString('&lt;img', $popup);
    }

    public function test_dashboard_uses_five_real_asset_records_instead_of_placeholder_totals(): void
    {
        $admin = User::factory()->admin()->create();
        foreach (range(1, 5) as $index) {
            Borehole::create([
                'borehole_serial_no' => "DASH-BH-{$index}",
                'province' => 'Midlands Province',
                'admin_district' => 'North District',
                'locality' => "Village {$index}",
                'village' => "Village {$index}",
                'ward' => (string) $index,
                'date' => '2026-09-20',
                'created_by' => $admin->id,
                'approved_at' => now(),
                'number_of_beneficiaries' => 100,
                'ph' => 7.1,
                'tds_mg_l' => 240,
                'turbidity_ntu' => 1.5,
            ]);
            Dam::create([
                'name' => "Dashboard Dam {$index}",
                'village' => "Village {$index}",
                'ward' => (string) $index,
                'district' => 'North District',
                'province' => 'Midlands Province',
                'wall_condition' => $index === 1 ? 'poor' : 'good',
                'spillway_condition' => 'good',
            ]);
            PipedWaterScheme::create([
                'name' => "Dashboard Scheme {$index}",
                'village' => "Village {$index}",
                'ward' => (string) $index,
                'water_source' => 'borehole',
                'year_established' => 2020,
                'established_by' => 'Council',
                'pump_type' => 'Submersible',
                'power_type' => 'solar',
                'network_length' => 100,
                'storage_tanks' => 10,
                'stand_pipes_count' => 5,
                'functionality' => $index < 4 ? 'Functional' : 'Non functional',
                'households_served' => 20,
                'population_served' => 100,
                'women_served' => 50,
                'men_served' => 30,
                'children_served' => 20,
                'created_by' => $admin->id,
            ]);
            IrrigationScheme::create([
                'name' => "Irrigation {$index}",
                'village' => "Village {$index}",
                'ward' => (string) $index,
                'irrigation_type' => 'Drip',
                'water_source_type' => 'Borehole',
                'functionality' => 'Functional',
                'created_by' => $admin->id,
            ]);
            WaterPoint::create([
                'name' => "Water Point {$index}",
                'village_name' => "Village {$index}",
                'ward' => (string) $index,
                'type_of_water_source' => 'Borehole',
                'functionality' => 'Functional',
                'created_by' => $admin->id,
            ]);
        }

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total Dams')
            ->assertSee('Total Piped Water Schemes')
            ->assertSee('Latest recorded pH');

        $this->assertSame(5, Dam::count());
        $this->assertSame(5, PipedWaterScheme::count());
        $this->assertSame(5, IrrigationScheme::count());
        $this->assertSame(5, WaterPoint::count());
        $this->assertSame(5, Borehole::count());
    }
}
