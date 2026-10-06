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
        $waterPoints = $this->scopeRecordsToOwner($request, WaterPoint::query())
            ->orderBy('created_at', 'desc')->paginate(20);
        return view('water-points.index', compact('waterPoints'));
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
        $data = $request->validate($this->rules());
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

        $data = $request->validate($this->rules());
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
            'ward' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'type_of_water_source' => 'required|string|max:255',
            'year_drilled' => 'nullable|integer|min:1900|max:' . date('Y'),
            'seasonality' => 'nullable|string|max:255',
            'type_of_lifting_device' => 'nullable|string|max:255',
            'functionality' => 'required|in:Functional,Partially Functional,Nonfunctional',
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
}