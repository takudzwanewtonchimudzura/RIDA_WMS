<?php

namespace App\Http\Controllers;

use App\Models\IrrigationScheme;
use App\Http\Controllers\Concerns\AuthorizesRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IrrigationSchemeController extends Controller
{
    use AuthorizesRecords;

    /**
     * Display a listing of irrigation schemes.
     */
    public function index(Request $request)
    {
        $schemes = $this->scopeRecordsToOwner($request, IrrigationScheme::query())
            ->orderBy('created_at', 'desc')->paginate(20);
        return view('irrigation-schemes.index', compact('schemes'));
    }

    /**
     * Show the form for creating a new irrigation scheme.
     */
    public function create()
    {
        abort_if(request()->user()->isViewer(), 403);
        return view('irrigation-schemes.create');
    }

    /**
     * Store a newly created irrigation scheme.
     */
    public function store(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $this->normalizeFormFields($request);
        $data = $request->validate($this->rules());
        $data['has_management_committee'] = $request->boolean('management_committee_available');
        $data['committee_trained'] = $request->boolean('is_trained');
        $data['committee_functional'] = $request->boolean('is_functional');
        $data['has_revolving_fund'] = $request->boolean('revolving_fund');
        $data['created_by'] = Auth::id();

        IrrigationScheme::create($data);

        return redirect()->route('irrigation-schemes.index')
            ->with('success', 'Irrigation scheme created successfully.');
    }

    /**
     * Display the specified irrigation scheme.
     */
    public function show(Request $request, $id)
    {
        $scheme = IrrigationScheme::findOrFail($id);
        $this->authorizeRecordView($request, $scheme);
        return view('irrigation-schemes.show', compact('scheme'));
    }

    /**
     * Show the form for editing the irrigation scheme.
     */
    public function edit(Request $request, $id)
    {
        $scheme = IrrigationScheme::findOrFail($id);
        $this->authorizeRecordUpdate($request, $scheme);
        return view('irrigation-schemes.edit', compact('scheme'));
    }

    /**
     * Update the specified irrigation scheme.
     */
    public function update(Request $request, $id)
    {
        $scheme = IrrigationScheme::findOrFail($id);
        $this->authorizeRecordUpdate($request, $scheme);
        $this->normalizeFormFields($request);
        $data = $request->validate($this->rules());
        $data['has_management_committee'] = $request->boolean('management_committee_available');
        $data['committee_trained'] = $request->boolean('is_trained');
        $data['committee_functional'] = $request->boolean('is_functional');
        $data['has_revolving_fund'] = $request->boolean('revolving_fund');
        $data['updated_by'] = Auth::id();

        $scheme->update($data);

        return redirect()->route('irrigation-schemes.index')
            ->with('success', 'Irrigation scheme updated successfully.');
    }

    /**
     * Remove the specified irrigation scheme.
     */
    public function destroy(Request $request, $id)
    {
        $scheme = IrrigationScheme::findOrFail($id);
        $this->authorizeRecordDelete($request, $scheme);
        $scheme->delete();

        return redirect()->route('irrigation-schemes.index')
            ->with('success', 'Irrigation scheme deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'irrigation_type' => 'required|string|max:255',
            'water_source_type' => 'required|string|max:255',
            'year_established' => 'nullable|integer|min:1900|max:' . date('Y'),
            'established_by' => 'nullable|string|max:255',
            'pump_type' => 'nullable|string|max:255',
            'power_type' => 'nullable|string|max:255',
            'size_hectares' => 'nullable|numeric|min:0',
            'beneficiary_households' => 'nullable|integer|min:0',
            'plot_size' => 'nullable|numeric|min:0',
            'crop_pattern' => 'nullable|string',
            'total_storage_tanks_m3' => 'nullable|numeric|min:0',
            'functionality' => 'required|in:Functional,Partially Functional,Nonfunctional',
            'functionality_comments' => 'nullable|string',
            'fund_amount_available' => 'nullable|numeric|min:0',
            'committee_total_members' => 'nullable|integer|min:0',
            'committee_women' => 'nullable|integer|min:0',
            'committee_men' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string',
        ];
    }

    private function normalizeFormFields(Request $request): void
    {
        $aliases = [
            'name_of_scheme' => 'name',
            'type_of_water_source' => 'water_source_type',
            'type_of_pump' => 'pump_type',
            'type_of_power' => 'power_type',
            'size_in_hectares' => 'size_hectares',
            'total_storage_tanks_in_m3' => 'total_storage_tanks_m3',
            'number_of_beneficiaries_in_hh' => 'beneficiary_households',
            'functionality_of_the_scheme' => 'functionality',
            'revolving_fund_amount' => 'fund_amount_available',
            'total_number_of_members' => 'committee_total_members',
            'women_members' => 'committee_women',
        ];

        foreach ($aliases as $formField => $modelField) {
            if ($request->exists($formField)) {
                $request->merge([$modelField => $request->input($formField)]);
            }
        }
    }
}