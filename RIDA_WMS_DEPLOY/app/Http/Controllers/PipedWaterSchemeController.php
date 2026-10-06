<?php
// app/Http/Controllers/PipedWaterSchemeController.php

namespace App\Http\Controllers;

use App\Models\PipedWaterScheme;
use App\Http\Controllers\Concerns\AuthorizesRecords;
use Illuminate\Http\Request;

class PipedWaterSchemeController extends Controller
{
    use AuthorizesRecords;

    // Display all schemes
    public function index(Request $request)
    {
        $query = $this->scopeRecordsToOwner($request, PipedWaterScheme::query());

        // Apply search filters if any are present
        if ($request->has('search') && !empty($request->input('search'))) {
            $term = '%' . trim($request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('village', 'like', $term)
                  ->orWhere('ward', 'like', $term);
            });
        }

        if ($request->has('status') && !empty($request->input('status')) && in_array($request->input('status'), ['Functional', 'Partially Functional', 'Non Functional'])) {
            $query->where('functionality', $request->input('status'));
        }
        // Order and paginate results
        $schemes = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('piped-water-schemes.index', compact('schemes'));
    }

    // Show create form
    public function create()
    {
        abort_if(request()->user()->isViewer(), 403);
        return view('piped-water-schemes.create');
    }

    // Store new scheme
    public function store(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $data = $request->validate($this->rules());
        $this->setCommitteeFlags($request, $data);
        $data['created_by'] = $request->user()->id;
        PipedWaterScheme::create($data);

        return redirect()->route('piped-water-schemes.index')
            ->with('success', 'Piped water scheme created successfully.');
    }

    // Show method (optional - if you want detail view)
    public function show(Request $request, $id)
    {
        $scheme = PipedWaterScheme::findOrFail($id);
        $this->authorizeRecordView($request, $scheme);
    
        // If you don't need a show page, redirect to index
        // return redirect()->route('piped-water-schemes.index');
        return view('piped-water-schemes.detail', compact('scheme'));
    }

    // Display scheme networks
    public function networks(Request $request)
    {
        $query = $this->scopeRecordsToOwner($request, PipedWaterScheme::query())
            ->select('id', 'name', 'village', 'ward', 'network_length', 'stand_pipes_count', 'functionality');

        if ($request->has('search') && !empty($request->input('search'))) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('village', 'like', '%' . $request->input('search') . '%');
            });
        }

        $schemes = $query->orderBy('ward')->orderBy('village')->get();
        
        return view('piped-water-schemes.networks', compact('schemes'));
    }

    // Display water quality page
    public function quality(Request $request)
    {
        $query = $this->scopeRecordsToOwner($request, PipedWaterScheme::query())
            ->with('latestQualityTest')
            ->select('id', 'name', 'village', 'water_source', 'functionality', 'created_by');

        if ($request->has('search') && !empty($request->input('search'))) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('village', 'like', '%' . $request->input('search') . '%');
            });
        }

        $schemes = $query->orderBy('village')->get();
        
        return view('piped-water-schemes.quality', compact('schemes'));
    }


    public function waterQuality($id)
    {
    // Fetch the water scheme maintenance data by ID
        $maintenance = PipedWaterScheme::findOrFail($id);
    
    // Fetch schemes data
        $schemes = PipedWaterScheme::select('id', 'name', 'village', 'water_source', 'functionality')
        ->orderBy('village')
        ->get();
    
    // Return the view with data
        return view('piped-water-schemes.quality', compact('schemes', 'maintenance'));
    }


    // Show edit form
    public function edit(Request $request, $id)
    {
        $pipedWaterScheme = PipedWaterScheme::findOrFail($id);
        $this->authorizeRecordUpdate($request, $pipedWaterScheme);
        return view('piped-water-schemes.create', compact('pipedWaterScheme'));
    }

    // Update scheme
    public function update(Request $request, PipedWaterScheme $pipedWaterScheme)
    {
        $this->authorizeRecordUpdate($request, $pipedWaterScheme);
        $data = $request->validate($this->rules());
        $this->setCommitteeFlags($request, $data);
        $pipedWaterScheme->update($data);

        return redirect()->route('piped-water-schemes.index')
            ->with('success', 'Piped water scheme updated successfully.');
    }

    // Delete scheme
    public function destroy(Request $request, PipedWaterScheme $pipedWaterScheme)
    {
        $this->authorizeRecordDelete($request, $pipedWaterScheme);
        $pipedWaterScheme->delete();
        
        return redirect()->route('piped-water-schemes.index')
            ->with('success', 'Piped water scheme deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'water_source' => 'required|in:borehole,deep well,spring,river,dam,sand abstraction,other',
            'water_source_other' => 'nullable|string|max:255',
            'year_established' => 'required|integer|min:1900|max:' . date('Y'),
            'established_by' => 'required|string|max:255',
            'pump_type' => 'required|string|max:255',
            'power_type' => 'required|in:solar,electricity,fossil fuel,wind,gravity',
            'network_length' => 'required|numeric|min:0',
            'storage_tanks' => 'required|numeric|min:0',
            'stand_pipes_count' => 'required|integer|min:0',
            'functionality' => 'required|in:Functional,Partially Functional,Non functional',
            'functionality_issues' => 'nullable|string',
            'households_served' => 'required|integer|min:0',
            'population_served' => 'required|integer|min:0',
            'women_served' => 'required|integer|min:0',
            'men_served' => 'required|integer|min:0',
            'children_served' => 'required|integer|min:0',
            'has_management_committee' => 'nullable|boolean',
            'committee_trained' => 'nullable|boolean',
            'committee_functional' => 'nullable|boolean',
            'has_revolving_fund' => 'nullable|boolean',
            'fund_amount' => 'nullable|numeric|min:0',
            'committee_members_total' => 'nullable|integer|min:0',
            'committee_women' => 'nullable|integer|min:0',
            'committee_men' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string',
        ];
    }

    private function setCommitteeFlags(Request $request, array &$data): void
    {
        foreach ([
            'has_management_committee',
            'committee_trained',
            'committee_functional',
            'has_revolving_fund',
        ] as $field) {
            $data[$field] = $request->boolean($field);
        }
    }
}