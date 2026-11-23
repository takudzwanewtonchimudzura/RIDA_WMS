<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borehole;
use App\Http\Controllers\Concerns\AuthorizesRecords;

class BoreholeMapController extends Controller
{
    use AuthorizesRecords;

    /**
     * Display geographic map of boreholes
     */
    public function geographicMaps(Request $request)
    {
        // Get boreholes with coordinates
        $boreholes = $this->scopeRecordsToOwner($request, Borehole::query())
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Group by province for filtering
        $provinces = $this->scopeRecordsToOwner($request, Borehole::query())
                    ->whereNotNull('province')
                            ->distinct()
                            ->pluck('province')
                            ->sort();

        // Prepare data for the map
        $mapData = $boreholes->map(function ($borehole) {
            return [
                'id' => $borehole->id,
                'serial_no' => $borehole->borehole_serial_no,
                'latitude' => (float) $borehole->latitude,
                'longitude' => (float) $borehole->longitude,
                'province' => $borehole->province,
                'district' => $borehole->admin_district,
                'locality' => $borehole->locality,
                'depth' => $borehole->recorded_depth,
                'yield' => $borehole->recorded_yield,
                'status' => $this->getBoreholeStatus($borehole),
                'popup_content' => $this->getPopupContent($borehole),
            ];
        });

        return view('maps.geographic', [
            'boreholes' => $boreholes,
            'mapData' => $mapData,
            'provinces' => $provinces,
            'totalBoreholes' => $boreholes->count(),
            'boreholesWithCoords' => $boreholes->count(),
        ]);
    }

    /**
     * Get borehole status based on yield and depth
     */
    private function getBoreholeStatus($borehole)
    {
        $yield = $borehole->blowing_yield ?? $borehole->yield_lps;
        if (!$yield) {
            return 'unknown';
        }

        if ($yield >= 10) {
            return 'high_yield';
        } elseif ($yield >= 5) {
            return 'medium_yield';
        } else {
            return 'low_yield';
        }
    }

    /**
     * Generate popup content for map markers
     */
    private function getPopupContent($borehole)
    {
        $serial = e($borehole->borehole_serial_no);
        $locality = e($borehole->locality);
        $district = e($borehole->admin_district);
        $province = e($borehole->province);
        return "
            <div class='borehole-popup'>
                <h6><strong>{$serial}</strong></h6>
                <p><strong>Location:</strong> {$locality}, {$district}</p>
                <p><strong>Province:</strong> {$province}</p>
                <p><strong>Depth:</strong> " . ($borehole->recorded_depth === null ? 'N/A' : number_format($borehole->recorded_depth, 2) . ' m') . "</p>
                <p><strong>Blowing Yield:</strong> " . ($borehole->recorded_yield === null ? 'N/A' : number_format($borehole->recorded_yield, 2) . ' l/s') . "</p>
                <p><strong>Date:</strong> " . ($borehole->date ? $borehole->date->format('M d, Y') : 'N/A') . "</p>
                <a href='" . route('boreholes.show', $borehole) . "' class='btn btn-sm btn-primary mt-2'>View Details</a>
            </div>
        ";
    }

    /**
     * Get borehole data as JSON for API calls
     */
    public function getBoreholeData(Request $request)
    {
        $query = $this->scopeRecordsToOwner($request, Borehole::query())
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // Filter by province if provided
        if ($request->has('province') && $request->province) {
            $query->where('province', $request->province);
        }

        $boreholes = $query->get();

        $data = $boreholes->map(function ($borehole) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $borehole->longitude, (float) $borehole->latitude]
                ],
                'properties' => [
                    'id' => $borehole->id,
                    'serial_no' => $borehole->borehole_serial_no,
                    'province' => $borehole->province,
                    'district' => $borehole->admin_district,
                    'locality' => $borehole->locality,
                    'depth' => $borehole->recorded_depth,
                    'yield' => $borehole->recorded_yield,
                    'status' => $this->getBoreholeStatus($borehole),
                    'popup' => $this->getPopupContent($borehole),
                ]
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $data
        ]);
    }
}