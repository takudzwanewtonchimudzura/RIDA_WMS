<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRecords;
use App\Models\IrrigationMaintenance;
use App\Models\IrrigationScheme;
use Illuminate\Http\Request;

class IrrigationMaintenanceController extends Controller
{
    use AuthorizesRecords;

    public function index(Request $request)
    {
        $query = $this->scopeRecordsToOwner($request, IrrigationMaintenance::with('irrigationScheme')->latest('date_repaired'));
        $maintenances = $query->paginate(20)->withQueryString();

        return view('irrigation-maintenances.index', compact('maintenances'));
    }

    public function create(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $schemes = $this->scopeRecordsToOwner($request, IrrigationScheme::query())->orderBy('name')->get();

        return view('irrigation-maintenances.form', ['maintenance' => new IrrigationMaintenance(), 'schemes' => $schemes]);
    }

    public function store(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $data = $this->validatedData($request);
        $scheme = IrrigationScheme::findOrFail($data['irrigation_scheme_id']);
        $this->authorizeRecordUpdate($request, $scheme);

        IrrigationMaintenance::create($this->snapshotScheme($data, $scheme) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('irrigation-maintenances.index')->with('success', 'Irrigation maintenance record created.');
    }

    public function show(Request $request, IrrigationMaintenance $irrigationMaintenance)
    {
        $this->authorizeRecordView($request, $irrigationMaintenance);
        $irrigationMaintenance->load('irrigationScheme');

        return view('irrigation-maintenances.show', ['maintenance' => $irrigationMaintenance]);
    }

    public function edit(Request $request, IrrigationMaintenance $irrigationMaintenance)
    {
        $this->authorizeRecordUpdate($request, $irrigationMaintenance);
        $schemes = $this->scopeRecordsToOwner($request, IrrigationScheme::query())->orderBy('name')->get();
        $irrigationMaintenance->load('irrigationScheme');

        return view('irrigation-maintenances.form', ['maintenance' => $irrigationMaintenance, 'schemes' => $schemes]);
    }

    public function update(Request $request, IrrigationMaintenance $irrigationMaintenance)
    {
        $this->authorizeRecordUpdate($request, $irrigationMaintenance);
        $data = $this->validatedData($request);
        $scheme = IrrigationScheme::findOrFail($data['irrigation_scheme_id']);
        $this->authorizeRecordUpdate($request, $scheme);

        $irrigationMaintenance->update($this->snapshotScheme($data, $scheme) + [
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('irrigation-maintenances.show', $irrigationMaintenance)->with('success', 'Irrigation maintenance record updated.');
    }

    public function destroy(Request $request, IrrigationMaintenance $irrigationMaintenance)
    {
        $this->authorizeRecordDelete($request, $irrigationMaintenance);
        $irrigationMaintenance->update(['deleted_by' => $request->user()->id]);
        $irrigationMaintenance->delete();

        return redirect()->route('irrigation-maintenances.index')->with('success', 'Irrigation maintenance record deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'irrigation_scheme_id' => 'required|exists:irrigation_schemes,id',
            'repaired_by' => 'required|string|max:255',
            'works_done' => 'required|string|max:4000',
            'spares_fitted' => 'nullable|string|max:4000',
            'spares_received_by' => 'nullable|string|max:255',
            'contact_details' => 'nullable|string|max:1000',
            'date_repaired' => 'required|date',
            'date_last_repaired' => 'nullable|date|before_or_equal:date_repaired',
            'remarks' => 'nullable|string|max:4000',
        ]);
    }

    private function snapshotScheme(array $data, IrrigationScheme $scheme): array
    {
        return $data + [
            'scheme_name' => $scheme->name,
            'village' => $scheme->village,
            'ward' => $scheme->ward,
            'water_source_type' => $scheme->water_source_type,
            'year_established' => $scheme->year_established,
            'established_by' => $scheme->established_by,
            'pump_type' => $scheme->pump_type,
            'power_type' => $scheme->power_type,
            'irrigation_type' => $scheme->irrigation_type,
            'total_storage_tanks_m3' => $scheme->total_storage_tanks_m3,
        ];
    }
}
