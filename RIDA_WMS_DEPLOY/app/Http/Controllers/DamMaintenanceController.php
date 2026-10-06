<?php

namespace App\Http\Controllers;

use App\Models\Dam;
use App\Models\DamMaintenance;
use App\Http\Controllers\Concerns\AuthorizesRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamMaintenanceController extends Controller
{
    use AuthorizesRecords;

    /**
     * Display a listing of maintenance records for a dam (Dam Inspections).
     */
    public function index(Request $request, Dam $dam)
    {
        $this->authorizeRecordView($request, $dam);
        $maintenanceRecords = $dam->maintenanceRecords()
            ->orderBy('maintenance_date', 'desc')
            ->paginate(15);
        
        $stats = [
            'total' => $dam->maintenanceRecords()->count(),
            'completed' => $dam->maintenanceRecords()->where('status', 'completed')->count(),
            'planned' => $dam->maintenanceRecords()->where('status', 'planned')->count(),
            'total_cost' => $dam->maintenanceRecords()->sum('actual_cost'),
        ];
        
        return view('dams.maintenance.index', compact('dam', 'maintenanceRecords', 'stats'));
    }

    /**
     * Show the form for creating a new maintenance record.
     */
    public function create(Request $request, Dam $dam)
    {
        abort_if($request->user()->isViewer(), 403);
        $this->authorizeRecordUpdate($request, $dam);
        $maintenanceTypes = [
            'repair' => 'Repair',
            'rehabilitation' => 'Rehabilitation', 
            'routine' => 'Routine Maintenance',
            'emergency' => 'Emergency Repair',
        ];
        
        $statuses = [
            'planned' => 'Planned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        
        return view('dams.maintenance.form', compact('dam', 'maintenanceTypes', 'statuses'));
    }

    /**
     * Store a newly created maintenance record.
     */
    public function store(Request $request, Dam $dam)
    {
        abort_if($request->user()->isViewer(), 403);
        $this->authorizeRecordUpdate($request, $dam);
        $data = $request->validate([
            'maintenance_type' => 'required|in:repair,rehabilitation,routine,emergency',
            'maintenance_date' => 'required|date',
            'wall_repair_details' => 'nullable|string|max:2000',
            'spillway_repair_details' => 'nullable|string|max:2000',
            'stone_pitching_replaced' => 'boolean',
            'star_grass_replanted' => 'boolean',
            'other_works' => 'nullable|string|max:2000',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'contractor_name' => 'nullable|string|max:255',
            'contractor_contact' => 'nullable|string|max:255',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'general_comments' => 'nullable|string|max:2000',
        ]);

        $data['dam_id'] = $dam->id;
        $data['stone_pitching_replaced'] = $request->has('stone_pitching_replaced');
        $data['star_grass_replanted'] = $request->has('star_grass_replanted');
        $data['created_by'] = $request->user()->id;

        DamMaintenance::create($data);

        return redirect()->route('dams.maintenance.index', $dam->id)
            ->with('success', 'Maintenance record created successfully.');
    }

    /**
     * Display the specified maintenance record.
     */
    public function show(Request $request, Dam $dam, DamMaintenance $maintenance)
    {
        abort_unless((int) $maintenance->dam_id === (int) $dam->id, 404);
        $this->authorizeRecordView($request, $maintenance);
        return view('dams.maintenance.show', compact('dam', 'maintenance'));
    }

    /**
     * Show the form for editing the maintenance record.
     */
    public function edit(Request $request, Dam $dam, DamMaintenance $maintenance)
    {
        abort_unless((int) $maintenance->dam_id === (int) $dam->id, 404);
        $this->authorizeRecordUpdate($request, $maintenance);
        $maintenanceTypes = [
            'repair' => 'Repair',
            'rehabilitation' => 'Rehabilitation',
            'routine' => 'Routine Maintenance',
            'emergency' => 'Emergency Repair',
        ];
        
        $statuses = [
            'planned' => 'Planned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        
        return view('dams.maintenance.form', compact('dam', 'maintenance', 'maintenanceTypes', 'statuses'));
    }

    /**
     * Update the specified maintenance record.
     */
    public function update(Request $request, Dam $dam, DamMaintenance $maintenance)
    {
        abort_unless((int) $maintenance->dam_id === (int) $dam->id, 404);
        $this->authorizeRecordUpdate($request, $maintenance);
        $data = $request->validate([
            'maintenance_type' => 'required|in:repair,rehabilitation,routine,emergency',
            'maintenance_date' => 'required|date',
            'wall_repair_details' => 'nullable|string|max:2000',
            'spillway_repair_details' => 'nullable|string|max:2000',
            'stone_pitching_replaced' => 'boolean',
            'star_grass_replanted' => 'boolean',
            'other_works' => 'nullable|string|max:2000',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'contractor_name' => 'nullable|string|max:255',
            'contractor_contact' => 'nullable|string|max:255',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'general_comments' => 'nullable|string|max:2000',
        ]);

        $data['stone_pitching_replaced'] = $request->has('stone_pitching_replaced');
        $data['star_grass_replanted'] = $request->has('star_grass_replanted');
        $data['updated_by'] = Auth::id();

        $maintenance->update($data);

        return redirect()->route('dams.maintenance.index', $dam->id)
            ->with('success', 'Maintenance record updated successfully.');
    }

    /**
     * Remove the specified maintenance record.
     */
    public function destroy(Request $request, Dam $dam, DamMaintenance $maintenance)
    {
        abort_unless((int) $maintenance->dam_id === (int) $dam->id, 404);
        $this->authorizeRecordDelete($request, $maintenance);
        $maintenance->delete();

        return redirect()->route('dams.maintenance.index', $dam->id)
            ->with('success', 'Maintenance record deleted successfully.');
    }
}