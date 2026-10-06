<?php

namespace App\Http\Controllers;

use App\Models\Dam;
use App\Http\Controllers\Concerns\AuthorizesRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DamController extends Controller
{
    use AuthorizesRecords;

    /**
     * Display a listing of dams (All Dams).
     */
    public function index(Request $request)
    {
        $visibleDams = $this->scopeRecordsToOwner($request, Dam::query());
        $query = (clone $visibleDams)->withCount('maintenanceRecords');

        $hasFilters = $request->hasAny(['q', 'province', 'condition', 'min_capacity', 'max_capacity', 'has_stone_pitching']);

        if ($hasFilters) {
            $filters = $request->only(['q', 'province', 'condition', 'min_capacity', 'max_capacity', 'has_stone_pitching']);

            if (!empty($filters['q'])) {
                $term = '%' . trim($filters['q']) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('province', 'like', $term)
                      ->orWhere('village', 'like', $term)
                      ->orWhere('ward', 'like', $term)
                      ->orWhere('district', 'like', $term);
                });
            }

            if (!empty($filters['province'])) {
                $query->where('province', $filters['province']);
            }

            if (!empty($filters['condition'])) {
                $condition = $filters['condition'];
                $query->where(function ($q) use ($condition) {
                    $q->where('wall_condition', $condition)
                      ->orWhere('spillway_condition', $condition);
                });
            }

            if (isset($filters['min_capacity']) && is_numeric($filters['min_capacity'])) {
                $query->where('capacity', '>=', (float)$filters['min_capacity']);
            }

            if (isset($filters['max_capacity']) && is_numeric($filters['max_capacity'])) {
                $query->where('capacity', '<=', (float)$filters['max_capacity']);
            }

            if (isset($filters['has_stone_pitching']) && $filters['has_stone_pitching'] !== '') {
                $val = filter_var($filters['has_stone_pitching'], FILTER_VALIDATE_BOOLEAN);
                $query->where('has_stone_pitching', $val);
            }
        }

        $dams = $query->orderBy('created_at', 'desc')
                      ->paginate(20)
                      ->withQueryString();

        $provinces = (clone $visibleDams)->distinct('province')
                        ->whereNotNull('province')
                        ->orderBy('province')
                        ->pluck('province');

        $stats = [
            'total' => (clone $visibleDams)->count(),
            'matched' => $dams->total(),
            'by_province' => (clone $visibleDams)->groupBy('province')
                ->select('province', DB::raw('count(*) as count'))
                ->get(),
            'needs_attention' => (clone $visibleDams)->where(function ($q) {
                $q->whereIn('wall_condition', ['poor', 'critical'])
                  ->orWhereIn('spillway_condition', ['poor', 'critical']);
            })->count(),
        ];

        return view('dams.index', compact('dams', 'stats', 'provinces'));
    }

    /**
     * Search dams.
     */
    public function search(Request $request)
    {
        $filters = $request->only(['q', 'province', 'condition', 'min_capacity', 'max_capacity', 'has_stone_pitching']);

        $visibleDams = $this->scopeRecordsToOwner($request, Dam::query());
        $query = (clone $visibleDams)->withCount('maintenanceRecords');

        if (!empty($filters['q'])) {
            $term = '%' . trim($filters['q']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('village', 'like', $term)
                  ->orWhere('ward', 'like', $term)
                  ->orWhere('district', 'like', $term);
            });
        }

        if (!empty($filters['province'])) {
            $query->where('province', $filters['province']);
        }

        if (!empty($filters['condition'])) {
            $condition = $filters['condition'];
            $query->where(function ($q) use ($condition) {
                $q->where('wall_condition', $condition)
                  ->orWhere('spillway_condition', $condition);
            });
        }

        if (isset($filters['min_capacity']) && is_numeric($filters['min_capacity']) && $filters['min_capacity'] >= 0) {
            $query->where('capacity', '>=', floatval($filters['min_capacity']));
        }

        if (isset($filters['max_capacity']) && is_numeric($filters['max_capacity']) && $filters['max_capacity'] >= 0) {
            $query->where('capacity', '<=', floatval($filters['max_capacity']));
        }

        if (isset($filters['has_stone_pitching']) && $filters['has_stone_pitching'] !== '') {
            $val = filter_var($filters['has_stone_pitching'], FILTER_VALIDATE_BOOLEAN);
            $query->where('has_stone_pitching', $val);
        }

        $dams = $query->orderBy('created_at', 'desc')
                      ->paginate(20)
                      ->appends($request->except('page'));

        $stats = [
            'total' => (clone $visibleDams)->count(),
            'matched' => $dams->total(),
            'by_province' => (clone $visibleDams)->groupBy('province')
                ->select('province', DB::raw('count(*) as count'))
                ->get(),
            'needs_attention' => (clone $visibleDams)->where(function ($q) {
                $q->whereIn('wall_condition', ['poor', 'critical'])
                  ->orWhereIn('spillway_condition', ['poor', 'critical']);
            })->count(),
        ];

        return view('dams.index', compact('dams', 'stats', 'filters'));
    }

    /**
     * Display dam water levels.
     */
    public function levels(Request $request)
    {
        $visibleDams = $this->scopeRecordsToOwner($request, Dam::query());
        $dams = (clone $visibleDams)->orderBy('name')->get();

        $stats = [
            'total_dams' => (clone $visibleDams)->count(),
            'full_dams' => (clone $visibleDams)->whereNotNull('capacity')
                ->whereNotNull('water_depth')
                ->whereRaw('water_depth / capacity >= 0.8')
                ->count(),
            'low_dams' => (clone $visibleDams)->whereNotNull('capacity')
                ->whereNotNull('water_depth')
                ->whereRaw('water_depth / capacity < 0.3')
                ->where('water_depth', '>', 0)
                ->count(),
            'critical_dams' => (clone $visibleDams)->whereIn('wall_condition', ['poor', 'critical'])->count(),
        ];

        return view('dams.levels', compact('dams', 'stats'));
    }

    /**
     * Show the form for creating a new dam.
     */
    public function create()
    {
        abort_if(request()->user()->isViewer(), 403);
        $provinces = [
            'Harare Province', 'Bulawayo Province', 'Midlands Province',
            'Masvingo Province', 'Manicaland Province', 'MashEast Province',
            'MashWest Province', 'MatSouth Province', 'MatNorth Province', 'MashCentral Province'
        ];

        $conditions = ['good', 'fair', 'poor', 'critical'];
        $spillwayTypes = ['natural on rocks', 'artificial', 'concrete', 'other'];

        return view('dams.create', compact('provinces', 'conditions', 'spillwayTypes'));
    }

    /**
     * Store a newly created dam.
     */
    public function store(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $data = $request->validate([
            'name'                     => 'required|string|max:255|unique:dams,name',
            'village'                  => 'required|string|max:255',
            'ward'                     => 'required|string|max:255',
            'district'                 => 'required|string|max:255',
            'province'                 => 'required|string|max:255',
            'latitude'                 => 'nullable|numeric|between:-90,90',
            'longitude'                => 'nullable|numeric|between:-180,180',
            'wall_length'              => 'nullable|numeric|min:0',
            'wall_height'              => 'nullable|numeric|min:0',
            'water_depth'              => 'nullable|numeric|min:0',
            'wall_condition'           => 'required|in:good,fair,poor,critical',
            'wall_condition_comment'   => 'nullable|string|max:1000',
            'spillway_width'           => 'nullable|numeric|min:0',
            'spillway_type'            => 'nullable|string|max:255',
            'spillway_condition'       => 'required|in:good,fair,poor,critical',
            'spillway_condition_comment' => 'nullable|string|max:1000',
            'throwback'                => 'nullable|numeric|min:0',
            'capacity'                 => 'nullable|numeric|min:0',
            'wall_volume'              => 'nullable|numeric|min:0',
            'has_stone_pitching'       => 'nullable',
            'has_star_grass'           => 'nullable',
            'general_comments'         => 'nullable|string|max:2000',
        ]);

        $data['has_stone_pitching'] = $request->has('has_stone_pitching') ? true : false;
        $data['has_star_grass']     = $request->has('has_star_grass') ? true : false;
        $data['created_by']         = Auth::id();

        if ($request->user()->isAdmin()) {
            $data['approved_at'] = now();
            $data['approved_by'] = $request->user()->id;
        }

        try {
            $dam = Dam::create($data);
        } catch (\Exception $e) {
            // Support both AJAX and normal form submissions
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating dam record: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->withInput()
                ->with('error', 'Error creating dam record: ' . $e->getMessage());
        }

        // Support both AJAX and normal form submissions
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dam record created successfully.',
                'redirect_url' => route('dams.show', $dam->id),
                'dam' => $dam
            ], 201);
        }

        return redirect()->route('dams.show', $dam->id)
            ->with('success', 'Dam record created successfully.');
    }

    /**
     * Display the specified dam.
     */
    public function show(Request $request, $id)
    {
        $dam = Dam::withCount('maintenanceRecords')->findOrFail($id);
        $this->authorizeRecordView($request, $dam);
        $maintenanceRecords = $dam->maintenanceRecords()
            ->orderBy('maintenance_date', 'desc')
            ->paginate(10);

        return view('dams.show', compact('dam', 'maintenanceRecords'));
    }

    public function approve(Request $request, Dam $dam)
    {
        $this->authorizeAdministrator($request);
        if (!$dam->approved_at) {
            $dam->update(['approved_at' => now(), 'approved_by' => $request->user()->id]);
        }

        return redirect()->route('dams.show', $dam)->with('success', 'Dam approved.');
    }

    public function unapprove(Request $request, Dam $dam)
    {
        $this->authorizeAdministrator($request);
        $dam->update(['approved_at' => null, 'approved_by' => null]);

        return redirect()->route('dams.show', $dam)->with('success', 'Dam approval removed.');
    }

    /**
     * Show the form for editing the dam.
     */
    public function edit(Request $request, $id)
    {
        $dam = Dam::findOrFail($id);
        $this->authorizeRecordUpdate($request, $dam);

        $provinces = [
            'Harare Province', 'Bulawayo Province', 'Midlands Province',
            'Masvingo Province', 'Manicaland Province', 'MashEast Province',
            'MashWest Province', 'MatSouth Province', 'MatNorth Province', 'MashCentral Province'
        ];

        $conditions = ['good', 'fair', 'poor', 'critical'];
        $spillwayTypes = ['natural on rocks', 'artificial', 'concrete', 'other'];

        return view('dams.edit', compact('dam', 'provinces', 'conditions', 'spillwayTypes'));
    }

    /**
     * Update the specified dam.
     */
    public function update(Request $request, $id)
    {
        $dam = Dam::findOrFail($id);
        $this->authorizeRecordUpdate($request, $dam);

        $data = $request->validate([
            'name'                     => 'required|string|max:255|unique:dams,name,' . $dam->id,
            'village'                  => 'required|string|max:255',
            'ward'                     => 'required|string|max:255',
            'district'                 => 'required|string|max:255',
            'province'                 => 'required|string|max:255',
            'latitude'                 => 'nullable|numeric|between:-90,90',
            'longitude'                => 'nullable|numeric|between:-180,180',
            'wall_length'              => 'nullable|numeric|min:0',
            'wall_height'              => 'nullable|numeric|min:0',
            'water_depth'              => 'nullable|numeric|min:0',
            'wall_condition'           => 'required|in:good,fair,poor,critical',
            'wall_condition_comment'   => 'nullable|string|max:1000',
            'spillway_width'           => 'nullable|numeric|min:0',
            'spillway_type'            => 'nullable|string|max:255',
            'spillway_condition'       => 'required|in:good,fair,poor,critical',
            'spillway_condition_comment' => 'nullable|string|max:1000',
            'throwback'                => 'nullable|numeric|min:0',
            'capacity'                 => 'nullable|numeric|min:0',
            'wall_volume'              => 'nullable|numeric|min:0',
            'has_stone_pitching'       => 'nullable',
            'has_star_grass'           => 'nullable',
            'general_comments'         => 'nullable|string|max:2000',
        ]);

        $data['has_stone_pitching'] = $request->has('has_stone_pitching') ? true : false;
        $data['has_star_grass']     = $request->has('has_star_grass') ? true : false;
        $data['updated_by']         = Auth::id();

        $dam->update($data);

        return redirect()->route('dams.show', $dam->id)
            ->with('success', 'Dam record updated successfully.');
    }

    /**
     * Show dam inspections.
     */
    public function inspections(Request $request)
    {
        $dams = $this->scopeRecordsToOwner($request, Dam::query())
            ->withCount('maintenanceRecords')
            ->orderBy('name')
            ->get();

        return view('dams.inspections', compact('dams'));
    }

    /**
     * Remove the specified dam.
     */
    public function destroy(Request $request, $id)
    {
        $dam = Dam::findOrFail($id);
        $this->authorizeRecordDelete($request, $dam);

        if ($dam->maintenanceRecords()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete dam with maintenance records. Delete maintenance records first.');
        }

        $dam->delete();

        return redirect()->route('dams.index')
            ->with('success', 'Dam record deleted successfully.');
    }

    /**
     * Export dams data.
     */
    public function export(Request $request)
    {
        $dams = $this->scopeRecordsToOwner($request, Dam::query())->get();
        $format = $request->get('format', 'csv');

        if ($format === 'csv') {
            return $this->exportToCsv($dams);
        }

        return redirect()->back()->with('error', 'Export format not supported.');
    }

    private function exportToCsv($dams)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="dams_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($dams) {
            $file = fopen('php://output', 'w');

            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Name', 'Village', 'Ward', 'District', 'Province',
                'Latitude', 'Longitude', 'Wall Length (m)', 'Wall Height (m)',
                'Water Depth (m)', 'Wall Condition', 'Spillway Type',
                'Spillway Condition', 'Capacity (m³)', 'Water Level %',
                'Stone Pitching', 'Star Grass', 'Created Date'
            ]);

            foreach ($dams as $dam) {
                $waterLevelPercentage = 0;
                if ($dam->capacity && $dam->water_depth) {
                    $waterLevelPercentage = min(100, ($dam->water_depth / $dam->capacity) * 100);
                }

                fputcsv($file, [
                    $dam->name,
                    $dam->village,
                    $dam->ward,
                    $dam->district,
                    $dam->province,
                    $dam->latitude ?? '',
                    $dam->longitude ?? '',
                    $dam->wall_length ?? '',
                    $dam->wall_height ?? '',
                    $dam->water_depth ?? '',
                    ucfirst($dam->wall_condition),
                    $dam->spillway_type ?? '',
                    ucfirst($dam->spillway_condition),
                    $dam->capacity ?? '',
                    $waterLevelPercentage . '%',
                    $dam->has_stone_pitching ? 'Yes' : 'No',
                    $dam->has_star_grass ? 'Yes' : 'No',
                    $dam->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}