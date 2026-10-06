<?php

namespace App\Http\Controllers;

use App\Models\SchemeMaintenance;
use App\Models\PipedWaterScheme;
use App\Http\Controllers\Concerns\AuthorizesRecords;
use Illuminate\Http\Request;

class SchemeMaintenanceController extends Controller
{
    use AuthorizesRecords;

    public function index(Request $request)
    {
        $maintenances = $this->scopeRecordsToOwner($request, SchemeMaintenance::query())
            ->orderByDesc('date_repaired')
            ->paginate(20);

        return view('scheme-maintenances.index', compact('maintenances'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isOperator(), 403);
        $schemes = PipedWaterScheme::query()->orderBy('name')->get();

        return view('scheme-maintenances.create', compact('schemes'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isOperator(), 403);
        $data = $this->validatedData($request);
        $data['created_by'] = $request->user()->id;
        SchemeMaintenance::create($data);

        return redirect()->route('scheme-maintenances.index')
            ->with('success', 'Maintenance record created successfully.');
    }

    public function show(Request $request, SchemeMaintenance $scheme_maintenance)
    {
        $this->authorizeRecordView($request, $scheme_maintenance);
        $maintenance = $scheme_maintenance;

        return view('scheme-maintenances.show', compact('maintenance'));
    }

    public function edit(Request $request, SchemeMaintenance $scheme_maintenance)
    {
        $this->authorizeRecordUpdate($request, $scheme_maintenance);
        $maintenance = $scheme_maintenance;
        $schemes = PipedWaterScheme::query()->orderBy('name')->get();

        return view('scheme-maintenances.edit', compact('maintenance', 'schemes'));
    }

    public function update(Request $request, SchemeMaintenance $scheme_maintenance)
    {
        $this->authorizeRecordUpdate($request, $scheme_maintenance);
        $data = $this->validatedData($request);
        $data['updated_by'] = $request->user()->id;
        $scheme_maintenance->update($data);

        return redirect()->route('scheme-maintenances.index')
            ->with('success', 'Maintenance record updated successfully.');
    }

    public function destroy(Request $request, SchemeMaintenance $scheme_maintenance)
    {
        $this->authorizeRecordDelete($request, $scheme_maintenance);
        $scheme_maintenance->update(['deleted_by' => $request->user()->id]);
        $scheme_maintenance->delete();

        return redirect()->route('scheme-maintenances.index')
            ->with('success', 'Maintenance record deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        $input = $request->validate([
            'name_of_scheme' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'type_of_water_source' => 'nullable|string|max:255',
            'year_established' => 'nullable|integer|min:1900|max:' . now()->year,
            'type_of_pump' => 'nullable|string|max:255',
            'type_of_power' => 'nullable|string|max:255',
            'total_network_in_metres' => 'nullable|numeric|min:0',
            'total_storage_tanks_in_m3' => 'nullable|numeric|min:0',
            'number_of_stand_pipes' => 'nullable|integer|min:0',
            'repaired_by' => 'required|string|max:255',
            'works_done' => 'required|string|max:10000',
            'spares_fitted' => 'nullable|string|max:10000',
            'spares_received_by' => 'nullable|string|max:255',
            'contact_detail' => 'nullable|string|max:1000',
            'date_repaired' => 'required|date',
            'date_last_repaired' => 'nullable|date',
            'remarks' => 'nullable|string|max:10000',
        ]);

        return [
            'scheme_name' => $input['name_of_scheme'],
            'village' => $input['village'],
            'ward' => $input['ward'],
            'water_source_type' => $input['type_of_water_source'] ?? null,
            'year_established' => $input['year_established'] ?? null,
            'pump_type' => $input['type_of_pump'] ?? null,
            'power_type' => $input['type_of_power'] ?? null,
            'total_network_metres' => $input['total_network_in_metres'] ?? null,
            'total_storage_tanks_m3' => $input['total_storage_tanks_in_m3'] ?? null,
            'number_of_standpipes' => $input['number_of_stand_pipes'] ?? null,
            'repaired_by' => $input['repaired_by'],
            'works_done' => $input['works_done'],
            'spares_fitted' => $input['spares_fitted'] ?? null,
            'spares_received_by' => $input['spares_received_by'] ?? null,
            'contact_details' => $input['contact_detail'] ?? null,
            'date_repaired' => $input['date_repaired'],
            'date_last_repaired' => $input['date_last_repaired'] ?? null,
            'remarks' => $input['remarks'] ?? null,
        ];
    }
}