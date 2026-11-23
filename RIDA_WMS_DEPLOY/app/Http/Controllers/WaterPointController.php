<?php

namespace App\Http\Controllers;

use App\Models\WaterPoint;
use App\Http\Controllers\Concerns\AuthorizesRecords;
use Illuminate\Http\Request;

class WaterPointController extends Controller
{
    use AuthorizesRecords;

    /**
     * Display a listing of water points.
     */
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);
        if ($request->input('export') === 'csv') {
            return response()->streamDownload(function () use ($query) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Name', 'Province', 'District', 'Ward', 'Latitude', 'Longitude', 'Source', 'Status', 'Borehole']);
                foreach ($query->orderBy('name')->cursor() as $point) {
                    $row = [$point->name, $point->province, $point->district, $point->ward, $point->latitude, $point->longitude, $point->type_of_water_source, $point->functionality, $point->borehole?->borehole_serial_no];
                    fputcsv($out, array_map(fn ($value) => is_string($value) && !is_numeric($value) && preg_match('/^[\\s]*[=+@-]|^[\\t\\r]/', $value) ? "'" . $value : $value, $row));
                }
                fclose($out);
            }, 'water-points.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }
        $waterPoints = $query->orderBy('name')->paginate(config('rwims.default_page_size'))->withQueryString();
        return view('water-points.index', compact('waterPoints'));
    }

    private function filteredQuery(Request $request)
    {
        $query = $this->scopeRecordsToOwner($request, WaterPoint::with('borehole'));
        if ($request->filled('search')) {
            $term = '%' . $request->string('search')->trim() . '%';
            $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('village_name', 'like', $term)
                ->orWhereHas('borehole', fn ($b) => $b->where('borehole_serial_no', 'like', $term)));
        }
        foreach (['province', 'district', 'functionality', 'type_of_water_source'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }
        return $query;
    }

    /**
     * Show the form for creating a new water point.
     */
    public function create()
    {
        abort_if(request()->user()->isViewer(), 403);
        return view('water-points.create');
    }

    /**
     * Store a newly created water point.
     */
    public function store(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $data = $this->validatedData($request);
        $data['has_water_committee'] = $request->boolean('has_water_committee');
        $data['committee_functional'] = $request->boolean('committee_functional');
        $data['created_by'] = $request->user()->id;

        WaterPoint::create($data);

        return redirect()->route('water-points.index')
            ->with('success', 'Water point created successfully.');
    }

    /**
     * Display the specified water point.
     */
    public function show(Request $request, $id)
    {
        $waterPoint = WaterPoint::findOrFail($id);
        $this->authorizeRecordView($request, $waterPoint);
        return view('water-points.show', compact('waterPoint'));
    }

    /**
     * Show the form for editing the water point.
     */
    public function edit(Request $request, $id)
    {
        $waterPoint = WaterPoint::findOrFail($id);
        $this->authorizeRecordUpdate($request, $waterPoint);
        return view('water-points.edit', compact('waterPoint'));
    }

    /**
     * Update the specified water point.
     */
    public function update(Request $request, $id)
    {
        $waterPoint = WaterPoint::findOrFail($id);
        $this->authorizeRecordUpdate($request, $waterPoint);

        $data = $this->validatedData($request, $waterPoint);
        $data['has_water_committee'] = $request->boolean('has_water_committee');
        $data['committee_functional'] = $request->boolean('committee_functional');
        $data['updated_by'] = $request->user()->id;

        $waterPoint->update($data);

        return redirect()->route('water-points.index')
            ->with('success', 'Water point updated successfully.');
    }

    /**
     * Remove the specified water point.
     */
    public function destroy(Request $request, $id)
    {
        $waterPoint = WaterPoint::findOrFail($id);
        $this->authorizeRecordDelete($request, $waterPoint);
        $waterPoint->delete();

        return redirect()->route('water-points.index')
            ->with('success', 'Water point deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'village_name' => 'required|string|max:255',
            ...app(\App\Services\ZimbabweLocations::class)->rules(),
            'type_of_water_source' => 'required|in:Borehole,Well,Spring,River',
            'year_drilled' => 'nullable|integer|min:1900|max:' . date('Y'),
            'seasonality' => 'nullable|string|max:255',
            'type_of_lifting_device' => 'nullable|string|max:255',
            'functionality' => ['required', \Illuminate\Validation\Rule::in(\App\Models\InfrastructureStatus::VALUES)],
            'strokes_before_water' => 'nullable|integer|min:0',
            'strokes_to_fill_20l' => 'nullable|integer|min:0',
            'number_of_pipes' => 'nullable|integer|min:0',
            'apron_condition' => 'nullable|in:good,bad,needs attention',
            'runoff_channel_condition' => 'nullable|in:good,bad,needs attention',
            'soakaway_condition' => 'nullable|in:good,bad,needs attention',
            'households_served' => 'nullable|integer|min:0',
            'population_served' => 'nullable|integer|min:0',
            'women_served' => 'nullable|integer|min:0',
            'men_served' => 'nullable|integer|min:0',
            'children_below_18' => 'nullable|integer|min:0',
            'has_water_committee' => 'nullable|boolean',
            'committee_functional' => 'nullable|boolean',
            'committee_total_members' => 'nullable|integer|min:0',
            'committee_women' => 'nullable|integer|min:0',
            'committee_men' => 'nullable|integer|min:0',
            'last_repaired_by' => 'nullable|string|max:255',
            'last_spares_fitted' => 'nullable|string|max:255',
            'last_repair_date' => 'nullable|date',
        ];
    }

    private function validatedData(Request $request, ?WaterPoint $waterPoint = null): array
    {
        $data = $request->validate($this->rules() + [
            'borehole_id' => [
                'nullable', 'required_if:type_of_water_source,Borehole', 'prohibited_unless:type_of_water_source,Borehole',
                \Illuminate\Validation\Rule::exists('boreholes', 'id')->whereNull('deleted_at'),
                \Illuminate\Validation\Rule::unique('water_points', 'borehole_id')->ignore($waterPoint?->id),
            ],
        ]);
        if (!empty($data['borehole_id'])) {
            $borehole = \App\Models\Borehole::findOrFail($data['borehole_id']);
            $this->authorizeRecordView($request, $borehole);
            $errors = [];
            foreach (['province' => 'province', 'district' => 'admin_district', 'ward' => 'ward', 'latitude' => 'latitude', 'longitude' => 'longitude'] as $field => $source) {
                $actual = $data[$field] ?? null;
                $expected = $borehole->{$source};
                $matches = in_array($field, ['latitude', 'longitude'])
                    ? ($actual === null && $expected === null) || ($actual !== null && $expected !== null && abs((float) $actual - (float) $expected) < 0.00001)
                    : $actual === $expected;
                if (!$matches) {
                    $errors[$field] = 'Location must match the linked borehole record.';
                }
            }
            if ($errors) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }
        }
        return $data;
    }
}