<?php

namespace App\Http\Controllers;

use App\Models\Borehole;
use App\Models\GeologyLayer;
use App\Models\CasingDetail;
use App\Models\ProspectingSite;
use App\Models\User;
use App\Http\Controllers\Concerns\AuthorizesRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class BoreholeController extends Controller
{
    use AuthorizesRecords;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Borehole::with(['creator', 'approver'])
            ->withCount(['geologyLayers', 'casingDetails'])
            ->latest();
        $this->scopeRecordsToOwner($request, $query);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('borehole_serial_no', 'LIKE', "%{$search}%")
                  ->orWhere('province', 'LIKE', "%{$search}%")
                  ->orWhere('admin_district', 'LIKE', "%{$search}%")
                  ->orWhere('locality', 'LIKE', "%{$search}%")
                  ->orWhere('project_name', 'LIKE', "%{$search}%")
                  ->orWhere('contractor', 'LIKE', "%{$search}%")
                  ->orWhere('funding_agency', 'LIKE', "%{$search}%");
            });
        }

        // Filter by province
        if ($request->has('province') && $request->input('province') != '') {
            $query->where('province', $request->input('province'));
        }

        // Filter by status
        if ($request->has('status') && in_array($request->input('status'), ['approved', 'pending'])) {
            if ($request->input('status') === 'approved') {
                $query->whereNotNull('approved_at');
            } elseif ($request->input('status') === 'pending') {
                $query->whereNull('approved_at');
            }
        }

        // Filter by date range
        if ($request->has('date_from') && $request['date_from'] != '') {
            $query->where('date', '>=', $request->input('date_from'));
        }
        if ($request->has('date_to') && $request['date_to'] != '') {
            $query->where('date', '<=', $request->input('date_to'));
        }

        $perPage = $request->input('per_page', 20);
        $boreholes = $query->paginate($perPage);

        // Get unique provinces for filter dropdown
        $provinces = $this->scopeRecordsToOwner($request, Borehole::query())
            ->distinct()->pluck('province')->sort();

        return view('boreholes.index', compact('boreholes', 'provinces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $prospectingSite = null;
        if ($request->filled('prospecting_site')) {
            $prospectingSite = ProspectingSite::findOrFail($request->input('prospecting_site'));
            abort_unless(
                $request->user()->isAdmin() || (int) $prospectingSite->created_by === (int) $request->user()->id,
                403
            );
            abort_unless($prospectingSite->status === 'approved', 403, 'Only approved prospecting sites can be drilled.');
        }

        return view('boreholes.create', compact('prospectingSite'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $prospectingSite = null;
        if ($request->filled('prospecting_site_id')) {
            $prospectingSite = ProspectingSite::findOrFail($request->input('prospecting_site_id'));
            abort_unless(
                $request->user()->isAdmin() || (int) $prospectingSite->created_by === (int) $request->user()->id,
                403
            );
            abort_unless($prospectingSite->status === 'approved', 403, 'Only approved prospecting sites can be drilled.');
        }

        DB::beginTransaction();
        try {
            $this->normalizeConsumableRows($request);
            $data = $this->validateBoreholeData($request);
            $data['created_by'] = Auth::id();

            $borehole = Borehole::create($data);
            if ($prospectingSite) {
                $prospectingSite->update(['status' => 'drilled']);
            }

            if ($request->has('geology_layers')) {
                $this->createGeologyLayers($borehole, $request->input('geology_layers'));
            }

            if ($request->has('casing_details')) {
                $this->createCasingDetails($borehole, $request->input('casing_details'));
            }
            $this->syncBitSizeIntervals($borehole, $request->input('bit_size_intervals', []));
            $this->syncConsumableUsages($borehole, $request->input('consumables', []));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Borehole created successfully',
                'redirect_url' => route('boreholes.index'),
                'borehole' => $borehole
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating borehole: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Borehole $borehole)
    {
        $this->authorizeRecordView($request, $borehole);
        $borehole->load([
            'creator',
            'approver',
            'geologyLayers' => function ($query) {
                $query->orderBy('description_layer_depth_from');
            },
            'casingDetails' => function ($query) {
                $query->orderBy('position');
            },
            'consumableUsages' => function ($query) {
                $query->orderBy('equipment_type')->orderBy('serial_number');
            },
            'bitSizeIntervals',
        ]);

        return view('boreholes.show', compact('borehole'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $borehole = Borehole::findOrFail($id);
        $this->authorizeRecordUpdate($request, $borehole);
        // Check if borehole is approved (cannot edit approved records)
        if ($borehole->approved_at) {
            return redirect()->route('boreholes.show', $borehole)
                ->with('warning', 'Cannot edit an approved borehole record.');
        }

        $borehole->load(['geologyLayers', 'casingDetails', 'consumableUsages', 'bitSizeIntervals']);
        $bitSizeIntervals = $borehole->bitSizeIntervals->map(fn ($interval) => [
            'bit_size_mm' => $interval->bit_size_mm,
            'depth_from_m' => $interval->depth_from_m,
            'depth_to_m' => $interval->depth_to_m,
        ])->all();
        if (empty($bitSizeIntervals)) {
            foreach ([$borehole->first_drilled_diameter_mm, $borehole->second_drilled_diameter_mm, $borehole->third_drilled_diameter_mm] as $legacyDiameter) {
                if ($legacyDiameter) {
                    $bitSizeIntervals[] = ['bit_size_mm' => $legacyDiameter, 'depth_from_m' => '', 'depth_to_m' => ''];
                }
            }
        }
        if (empty($bitSizeIntervals)) {
            $bitSizeIntervals[] = ['bit_size_mm' => '', 'depth_from_m' => '', 'depth_to_m' => ''];
        }
        $consumableRows = $borehole->consumableUsages
            ->map(fn ($usage) => [
                'equipment_type' => $usage->equipment_type,
                'serial_number' => $usage->serial_number,
                'quantity_used' => $usage->quantity_used,
            ])
            ->all();

        if (empty($consumableRows)) {
            if ($borehole->dth_bit_serial_no) {
                $consumableRows[] = [
                    'equipment_type' => 'bit',
                    'serial_number' => $borehole->dth_bit_serial_no,
                    'quantity_used' => max(1, (int) $borehole->drilling_bits_used),
                ];
            }
            if ($borehole->hammer_serial_no) {
                $consumableRows[] = [
                    'equipment_type' => 'hammer',
                    'serial_number' => $borehole->hammer_serial_no,
                    'quantity_used' => max(1, (int) $borehole->hammer_rings_used),
                ];
            }
        }
        if (empty($consumableRows)) {
            $consumableRows[] = ['equipment_type' => '', 'serial_number' => '', 'quantity_used' => ''];
        }

        return view('boreholes.edit', compact('borehole', 'consumableRows', 'bitSizeIntervals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $borehole = Borehole::findOrFail($id);
        $this->authorizeRecordUpdate($request, $borehole);

        // Check if borehole is approved (cannot update approved records)
        if ($borehole->approved_at) {
            return redirect()->route('boreholes.show', $borehole)
                ->with('warning', 'Cannot update an approved borehole record.');
        }

        DB::beginTransaction();

        try {
            $this->normalizeConsumableRows($request);
            $this->normalizeBitSizeIntervals($request);
            // Validate main borehole data
            $validated = $this->validateBoreholeData($request, $borehole->id);

            // Add updated_by user
            $validated['updated_by'] = Auth::id();

            // Update the borehole
            $borehole->update($validated);

            // Update geology layers
            $this->syncGeologyLayers($borehole, $request->input('geology_layers', []));

            // Update casing details
            $this->syncCasingDetails($borehole, $request->input('casing_details', []));
            $this->syncBitSizeIntervals($borehole, $request->input('bit_size_intervals', []));
            $this->syncConsumableUsages($borehole, $request->input('consumables', []));

            DB::commit();

            return redirect()->route('boreholes.show', $borehole)
                ->with('success', 'Borehole record updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating borehole record: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $borehole = Borehole::findOrFail($id);
        $this->authorizeRecordDelete($request, $borehole);

        try {
            // Soft delete the borehole
            $borehole->deleted_by = Auth::id();
            $borehole->save();
            $borehole->delete();

            return redirect()->route('boreholes.index')
                ->with('success', 'Borehole deleted successfully!');

        } catch (Exception $e) {
            return redirect()->route('boreholes.index')
                ->with('error', 'Error deleting borehole: ' . $e->getMessage());
        }
    }

    /**
     * Restore a trashed borehole.
     */
    public function restore(Request $request, $id)
    {
        $this->authorizeRecordDelete($request);
        try {
            $borehole = Borehole::withTrashed()->findOrFail($id);
            $borehole->deleted_by = null;
            $borehole->restore();

            return redirect()->route('boreholes.index')
                ->with('success', 'Borehole restored successfully!');

        } catch (\Exception $e) {
            return redirect()->route('boreholes.index')
                ->with('error', 'Error restoring borehole: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a borehole.
     */
    public function forceDelete(Request $request, $id)
    {
        $this->authorizeRecordDelete($request);
        try {
            $borehole = Borehole::withTrashed()->findOrFail($id);

            // Check if borehole is approved
            if ($borehole->approved_at) {
                return redirect()->route('boreholes.index')
                    ->with('warning', 'Cannot permanently delete an approved borehole record.');
            }

            $borehole->geologyLayers()->forceDelete();
            $borehole->casingDetails()->forceDelete();
            $borehole->forceDelete();

            return redirect()->route('boreholes.index')
                ->with('success', 'Borehole permanently deleted!');

        } catch (\Exception $e) {
            return redirect()->route('boreholes.index')
                ->with('error', 'Error permanently deleting borehole: ' . $e->getMessage());
        }
    }

    /**
     * Display trashed boreholes.
     */
    public function trashed(Request $request)
    {
        $this->authorizeRecordDelete($request);
        $boreholes = Borehole::onlyTrashed()
            ->with(['creator', 'approver'])
            ->latest()
            ->paginate(20);

        return view('boreholes.trashed', compact('boreholes'));
    }

    /**
     * Approve a borehole record.
     */
    public function approve(Request $request, Borehole $borehole)
    {
        $this->authorizeAdministrator($request);
        // Check if already approved
        if ($borehole->approved_at) {
            return redirect()->route('boreholes.show', $borehole)
                ->with('info', 'Borehole is already approved.');
        }

        try {
            $borehole->update([
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            return redirect()->route('boreholes.show', $borehole)
                ->with('success', 'Borehole approved successfully!');

        } catch (\Exception $e) {
            return redirect()->route('boreholes.show', $borehole)
                ->with('error', 'Error approving borehole: ' . $e->getMessage());
        }
    }

    /**
     * Unapprove a borehole record.
     */
    public function unapprove(Request $request, Borehole $borehole)
    {
        $this->authorizeAdministrator($request);
        if (!$borehole->approved_at) {
            return redirect()->route('boreholes.show', $borehole)
                ->with('info', 'Borehole is not approved.');
        }

        try {
            $borehole->update([
                'approved_at' => null,
                'approved_by' => null,
            ]);

            return redirect()->route('boreholes.show', $borehole)
                ->with('success', 'Borehole unapproved successfully!');

        } catch (\Exception $e) {
            return redirect()->route('boreholes.show', $borehole)
                ->with('error', 'Error unapproving borehole: ' . $e->getMessage());
        }
    }

    /**
     * Export borehole data as PDF.
     */
    public function exportPdf(Borehole $borehole)
    {
        $borehole->load([
            'creator',
            'approver',
            'geologyLayers' => function ($query) {
                $query->orderBy('description_layer_depth_from');
            },
            'casingDetails' => function ($query) {
                $query->orderBy('position');
            }
        ]);

        $pdf = PDF::loadView('boreholes.pdf.export', compact('borehole'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $filename = "Borehole-{$borehole->borehole_serial_no}-" . now()->format('Y-m-d') . ".pdf";

        return $pdf->download($filename);
    }

    /**
     * Export borehole data as CSV.
     */
    public function exportCsv(Borehole $borehole)
    {
        $borehole->load(['geologyLayers', 'casingDetails']);

        $filename = "Borehole-{$borehole->borehole_serial_no}-" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($borehole) {
            $handle = fopen('php://output', 'w');

            // Borehole data header
            fputcsv($handle, ['BOREHOLE DETAILS']);
            fputcsv($handle, ['Field', 'Value']);

            // Borehole data
            foreach ($borehole->toArray() as $key => $value) {
                if (!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                    $fieldName = str_replace('_', ' ', ucwords($key));
                    fputcsv($handle, [$fieldName, $value]);
                }
            }

            fputcsv($handle, []); // Empty row

            // Geology layers
            if ($borehole->geologyLayers->count() > 0) {
                fputcsv($handle, ['GEOLOGY LAYERS']);
                fputcsv($handle, ['From (m)', 'To (m)', 'Penetration To (m)', 'Penetration Rate', 'Water Strike', 'Yield']);

                foreach ($borehole->geologyLayers as $layer) {
                    fputcsv($handle, [
                        $layer->description_layer_depth_from,
                        $layer->description_layer_depth_to,
                        $layer->penetration_rates_to,
                        $layer->penetration_rates_rate,
                        $layer->water_strike,
                        $layer->yield,
                    ]);
                }

                fputcsv($handle, []); // Empty row
            }

            // Casing details
            if ($borehole->casingDetails->count() > 0) {
                fputcsv($handle, ['CASING DETAILS']);
                fputcsv($handle, ['Description', 'Diameter (mm)', 'Depth From (m)', 'Depth To (m)', 'Position']);

                foreach ($borehole->casingDetails as $casing) {
                    fputcsv($handle, [
                        $casing->description,
                        $casing->diameter,
                        $casing->depth_from,
                        $casing->depth_to,
                        $casing->position,
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get borehole statistics.
     */
    public function statistics()
    {
        $stats = [
            'total' => Borehole::count(),
            'approved' => Borehole::whereNotNull('approved_at')->count(),
            'pending' => Borehole::whereNull('approved_at')->count(),
            'trashed' => Borehole::onlyTrashed()->count(),
            'by_province' => Borehole::select('province', DB::raw('count(*) as total'))
                ->groupBy('province')
                ->orderBy('total', 'desc')
                ->get(),
            'by_month' => Borehole::select(DB::raw('strftime("%Y-%m", date) as month'), DB::raw('count(*) as total'))
                ->whereRaw('strftime("%Y", date) = ?', [date('Y')])
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        return view('boreholes.statistics', compact('stats'));
    }

    /**
     * Validate borehole data.
     */
    private function validateBoreholeData(Request $request, $excludeId = null)
    {
        $rules = [
            'borehole_serial_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('boreholes')->ignore($excludeId),
            ],
            'prospecting_site_id' => 'nullable|exists:prospecting_sites,id',
            'province' => 'required|string|max:255',
            'admin_district' => 'required|string|max:255',
            'locality' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'village' => 'nullable|string|max:255',
            'vidco' => 'nullable|string|max:255',
            'usage' => 'nullable|string|max:255',
            'dth_bit_serial_no' => 'nullable|string|max:255',
            'hammer_serial_no' => 'nullable|string|max:255',
            'date' => 'required|date',
            'rig_number' => 'nullable|string|max:255',
            'rig_type' => 'nullable|string|max:255',
            'rdd_depth' => 'nullable|numeric|min:0',
            'rig_chasis_number' => 'nullable|string|max:255',
            'siting_agency' => 'nullable|in:Government,PISP',
            'pisp_specification' => 'nullable|string|max:255',
            'drilling_date_from' => 'nullable|date',
            'drilling_date_to' => 'nullable|date|after_or_equal:drilling_date_from',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'grid_reference' => 'nullable|string|max:255',
            'elevation' => 'nullable|string|max:255',
            'first_drilled_diameter_mm' => 'nullable|integer|min:0',
            'second_drilled_diameter_mm' => 'nullable|integer|min:0',
            'third_drilled_diameter_mm' => 'nullable|integer|min:0',
            'final_borehole_depth_m' => 'nullable|numeric|min:0',
            'yield_lps' => 'nullable|numeric|min:0',
            'development_hours' => 'nullable|integer|min:0',
            'gravel_packing_from_m' => 'nullable|numeric|min:0',
            'gravel_packing_to_m' => 'nullable|numeric|min:0',
            'gravel_type' => 'nullable|string|max:255',
            'gravel_thickness_m' => 'nullable|numeric|min:0',
            'cement_sealing_from_m' => 'nullable|numeric|min:0',
            'cement_sealing_to_m' => 'nullable|numeric|min:0',
            'cement_type' => 'nullable|string|max:255',
            'cement_volume_bags' => 'nullable|numeric|min:0',
            'borehole_design' => 'nullable|string|max:255',
            'final_borehole_depth' => 'nullable|numeric|min:0',
            'final_cased_depth' => 'nullable|numeric|min:0',
            'blowing_yield' => 'nullable|numeric|min:0',
            'cement_sealing' => 'nullable|numeric|min:0',
            'tds_mg_l' => 'nullable|integer|min:0',
            'ph' => 'nullable|numeric|between:0,14',
            'turbidity_ntu' => 'nullable|numeric|min:0',
            'electrical_conductivity' => 'nullable|numeric|min:0',
            'water_color' => 'nullable|string|max:255',
            'water_appearance' => 'nullable|string|max:255',
            'odor' => 'nullable|string|max:255',
            'taste' => 'nullable|string|max:255',
            'water_quality_remarks' => 'nullable|string',
            'pipe_length_m' => 'nullable|numeric|min:0',
            'cylinder_size_m' => 'nullable|numeric|min:0',
            'swl_m' => 'nullable|numeric|min:0',
            'dwl_m' => 'nullable|numeric|min:0',
            'pump_type' => 'nullable|string|max:255',
            'pump_model' => 'nullable|string|max:255',
            'pump_depth_m' => 'nullable|numeric|min:0',
            'pump_installation_date' => 'nullable|date',
            'pump_installation_status' => 'nullable|in:successful,unsuccessful',
            'installed_by_name' => 'nullable|string|max:255',
            'installed_by_organisation' => 'nullable|string|max:255',
            'rig_supervisor_remarks' => 'nullable|string',
            'record_date' => 'nullable|date',
            'fuel_type' => 'nullable|in:DIESEL,PETROL',
            'electrodes' => 'nullable|string|max:255',
            'prospectors' => 'nullable|string|max:255',
            'hydraulic_oil' => 'nullable|string|max:255',
            'fuel_liters' => 'nullable|numeric|min:0',
            'hydraulic_oil_liters' => 'nullable|numeric|min:0',
            'gear_oil_liters' => 'nullable|numeric|min:0',
            'grease_kg' => 'nullable|numeric|min:0',
            'drilling_mileage_km' => 'nullable|numeric|min:0',
            'drilling_bits_used' => 'nullable|integer|min:0',
            'hammer_rings_used' => 'nullable|integer|min:0',
            'consumables_remarks' => 'nullable|string',
            'funding_agency' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'contractor' => 'nullable|string|max:255',
            'number_of_beneficiaries' => 'nullable|integer|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'cost_currency' => 'nullable|string|max:10',
            'drilling_supervisor' => 'nullable|string|max:255',
            'hydrogeologist' => 'nullable|string|max:255',
            'technician' => 'nullable|string|max:255',
            'operator' => 'nullable|string|max:255',
            'assistant_operator' => 'nullable|string|max:255',
            'drilling_start_date' => 'nullable|date',
            'drilling_end_date' => 'nullable|date|after_or_equal:drilling_start_date',
            'development_date' => 'nullable|date',
            'test_pumping_date' => 'nullable|date',
            'total_drilling_days' => 'nullable|integer|min:0',
            'supervisor_remarks' => 'nullable|string',
            'client_remarks' => 'nullable|string',
            'technical_remarks' => 'nullable|string',
            'challenges_encountered' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'references' => 'nullable|string|max:255',
            'research' => 'nullable|string|max:255',
            'geology_layers' => 'nullable|array',
            'geology_layers.*.sample_description' => 'nullable|string|max:255',
            'geology_layers.*.description_layer_depth_from' => 'nullable|numeric|min:0',
            'geology_layers.*.description_layer_depth_to' => 'nullable|numeric|min:0',
            'geology_layers.*.penetration_rates_to' => 'nullable|numeric|min:0',
            'geology_layers.*.penetration_rates_rate' => 'nullable|string|max:255',
            'geology_layers.*.water_strike' => 'nullable|string|max:255',
            'casing_details' => 'nullable|array',
            'casing_details.*.description' => 'nullable|in:Plain,Slotted',
            'casing_details.*.diameter' => 'nullable|integer|min:0',
            'casing_details.*.depth_from' => 'nullable|numeric|min:0',
            'casing_details.*.depth_to' => 'nullable|numeric|min:0',
            'casing_details.*.position' => 'nullable|integer|min:1',
            'casing_details.*.casing_type' => 'nullable|in:PVC,Steel',
            'casing_details.*.casing_class' => 'nullable|string|max:255',
            'casing_details.*.gravel_packed' => 'nullable|boolean',
            'casing_details.*.cement_sealed' => 'nullable|boolean',
            'consumables' => 'nullable|array',
            'consumables.*.equipment_type' => 'required|in:bit,hammer',
            'consumables.*.serial_number' => 'required|string|max:255',
            'consumables.*.quantity_used' => 'required|integer|min:1',
            'bit_size_intervals' => 'nullable|array',
            'bit_size_intervals.*.bit_size_mm' => 'required|integer|min:1',
            'bit_size_intervals.*.depth_from_m' => 'required|numeric|min:0',
            'bit_size_intervals.*.depth_to_m' => 'required|numeric|min:0',
        ];

        $messages = [
            'borehole_serial_no.unique' => 'This borehole serial number already exists.',
            'drilling_date_to.after_or_equal' => 'Drilling end date must be after or equal to start date.',
            'drilling_end_date.after_or_equal' => 'Drilling end date must be after or equal to start date.',
            'ph.between' => 'pH value must be between 0 and 14.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Create geology layers for a borehole.
     */
    private function createGeologyLayers(Borehole $borehole, array $layers)
    {
        $geologyLayers = [];

        foreach ($layers as $layerData) {
            if (empty(array_filter($layerData))) {
                continue;
            }

            $geologyLayers[] = new GeologyLayer([
                'sample_description' => $layerData['sample_description'] ?? null,
                'description_layer_depth_from' => $layerData['description_layer_depth_from'] ?? null,
                'description_layer_depth_to' => $layerData['description_layer_depth_to'] ?? null,
                'penetration_rates_to' => $layerData['penetration_rates_to'] ?? null,
                'penetration_rates_rate' => $layerData['penetration_rates_rate'] ?? null,
                'water_strike' => $layerData['water_strike'] ?? null,
            ]);
        }

        if (!empty($geologyLayers)) {
            $borehole->geologyLayers()->saveMany($geologyLayers);
        }
    }

    /**
     * Create casing details for a borehole.
     */
    private function createCasingDetails(Borehole $borehole, array $casingDetails)
    {
        $casingData = [];

        foreach ($casingDetails as $index => $casingDetail) {
            if (empty(array_filter($casingDetail))) {
                continue;
            }

            $casingData[] = new CasingDetail([
                'description' => $casingDetail['description'] ?? null,
                'diameter' => $casingDetail['diameter'] ?? null,
                'depth_from' => $casingDetail['depth_from'] ?? null,
                'depth_to' => $casingDetail['depth_to'] ?? null,
                'position' => $casingDetail['position'] ?? ($index + 1),
                'casing_type' => $casingDetail['casing_type'] ?? null,
                'casing_class' => $casingDetail['casing_class'] ?? null,
                'gravel_packed' => !empty($casingDetail['gravel_packed']),
                'cement_sealed' => !empty($casingDetail['cement_sealed']),
            ]);
        }

        if (!empty($casingData)) {
            $borehole->casingDetails()->saveMany($casingData);
        }
    }

    /**
     * Sync geology layers for a borehole.
     */
    private function syncGeologyLayers(Borehole $borehole, array $layers)
    {
        $borehole->geologyLayers()->delete();

        if (!empty($layers)) {
            $this->createGeologyLayers($borehole, $layers);
        }
    }

    /**
     * Sync casing details for a borehole.
     */
    private function syncCasingDetails(Borehole $borehole, array $casingDetails)
    {
        $borehole->casingDetails()->delete();

        if (!empty($casingDetails)) {
            $this->createCasingDetails($borehole, $casingDetails);
        }
    }

    private function normalizeConsumableRows(Request $request): void
    {
        $rows = $request->input('consumables', []);
        if (!is_array($rows)) {
            $request->merge(['consumables' => $rows]);
            return;
        }

        $rows = array_filter($rows, function ($row) {
            return is_array($row) && count(array_filter($row, fn ($value) => $value !== null && $value !== '')) > 0;
        });

        $request->merge(['consumables' => array_values($rows)]);
    }

    private function syncConsumableUsages(Borehole $borehole, array $usages): void
    {
        $borehole->consumableUsages()->delete();

        foreach ($usages as $usage) {
            if (!is_array($usage) || empty($usage['serial_number'])) {
                continue;
            }

            $borehole->consumableUsages()->create([
                'equipment_type' => $usage['equipment_type'],
                'serial_number' => trim($usage['serial_number']),
                'quantity_used' => $usage['quantity_used'],
            ]);
        }
    }

    private function normalizeBitSizeIntervals(Request $request): void
    {
        $intervals = $request->input('bit_size_intervals', []);
        if (!is_array($intervals)) {
            $request->merge(['bit_size_intervals' => $intervals]);
            return;
        }

        $intervals = array_filter($intervals, function ($interval) {
            return is_array($interval) && count(array_filter($interval, fn ($value) => $value !== null && $value !== '')) > 0;
        });
        $request->merge(['bit_size_intervals' => array_values($intervals)]);
    }

    private function syncBitSizeIntervals(Borehole $borehole, array $intervals): void
    {
        $borehole->bitSizeIntervals()->delete();
        foreach (array_values($intervals) as $index => $interval) {
            if (!is_array($interval) || empty($interval['bit_size_mm'])) {
                continue;
            }
            $borehole->bitSizeIntervals()->create([
                'bit_size_mm' => $interval['bit_size_mm'],
                'depth_from_m' => $interval['depth_from_m'],
                'depth_to_m' => $interval['depth_to_m'],
                'position' => $index + 1,
            ]);
        }
    }

    /**
     * Check borehole serial number availability.
     */
    public function checkSerialNumber(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
            'exclude_id' => 'nullable|integer',
        ]);

        $exists = Borehole::where('borehole_serial_no', $request->serial_number)
            ->when($request->exclude_id, function ($query) use ($request) {
                $query->where('id', '!=', $request->exclude_id);
            })
            ->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists
                ? 'This serial number is already in use.'
                : 'Serial number is available.'
        ]);
    }

    /**
     * Get borehole coordinates for map.
     */
    public function getCoordinates(Request $request)
    {
        $boreholes = Borehole::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'borehole_serial_no', 'province', 'locality', 'latitude', 'longitude', 'yield_lps')
            ->get();

        return response()->json($boreholes);
    }
}