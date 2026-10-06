<?php
// app/Http/Controllers/ConsumablesController.php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\ConsumableCategory;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\UsageReport;
use App\Models\UsageReportItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * INVENTORY CONTROLLER
 */
class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Consumable::with(['category', 'inventory'])
            ->when($request->filled('category'), function ($q, $category) {
                $q->where('category_id', $category);
            })
            ->when($request->filled('search'), function ($q, $search) {
                $q->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($q, $status) {
                $q->where('status', $status);
            });

        if ($request->has('low_stock')) {
            $query->whereHas('inventory', function ($q) {
                $q->whereRaw('quantity <= consumables.minimum_stock');
            });
        }

        $consumables = $query->paginate(20);
        $categories = ConsumableCategory::all();

        return view('consumables.inventory.index', compact('consumables', 'categories'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $categories = ConsumableCategory::all();
        $suppliers = Supplier::where('status', 'active')->get();
        return view('consumables.inventory.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:consumables',
            'category_id' => 'required|exists:consumable_categories,id',
            'description' => 'nullable|string',
            'unit' => 'required|string',
            'unit_cost' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'initial_quantity' => 'required|integer|min:0',
            'location' => 'nullable|string',
            'batch_number' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $consumable = Consumable::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'] ?? null,
                'unit' => $validated['unit'],
                'unit_cost' => $validated['unit_cost'],
                'minimum_stock' => $validated['minimum_stock'],
            ]);

            $inventory = Inventory::create([
                'consumable_id' => $consumable->id,
                'quantity' => $validated['initial_quantity'],
                'location' => $validated['location'] ?? null,
                'batch_number' => $validated['batch_number'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'notes' => 'Initial stock added during creation',
            ]);
            $inventory->movements()->create([
                'user_id' => $request->user()->id,
                'movement_type' => 'opening',
                'quantity_delta' => (int) $validated['initial_quantity'],
                'reason' => 'Opening stock',
            ]);
        });

        return redirect()->route('consumables.inventory.index')
            ->with('success', 'Consumable added successfully.');
    }

    public function show(Consumable $consumable)
    {
        $consumable->load([
            'category', 
            'inventory.supplier',
            'inventory.movements.user',
        ]);

        // Manually load related data
        $stockRequestItems = StockRequestItem::where('consumable_id', $consumable->id)
            ->with('stockRequest')
            ->latest()
            ->take(10)
            ->get();

        $usageReportItems = UsageReportItem::where('consumable_id', $consumable->id)
            ->with('usageReport')
            ->latest()
            ->take(10)
            ->get();

        return view('consumables.inventory.show', compact('consumable', 'stockRequestItems', 'usageReportItems'));
    }

    public function edit(Consumable $consumable)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $categories = ConsumableCategory::all();
        return view('consumables.inventory.edit', compact('consumable', 'categories'));
    }

    public function update(Request $request, Consumable $consumable)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:consumables,code,' . $consumable->id,
            'category_id' => 'required|exists:consumable_categories,id',
            'description' => 'nullable|string',
            'unit' => 'required|string',
            'unit_cost' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $consumable->update($validated);

        return redirect()->route('consumables.inventory.index')
            ->with('success', 'Consumable updated successfully.');
    }

    public function adjustStock(Request $request, Consumable $consumable)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,remove',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($consumable, $validated, $request) {
            $inventory = Inventory::query()->where('consumable_id', $consumable->id)->lockForUpdate()->first();
            if (!$inventory) {
                $inventory = Inventory::create(['consumable_id' => $consumable->id, 'quantity' => 0]);
            }

            if ($validated['adjustment_type'] === 'add') {
                $inventory->quantity += $validated['quantity'];
                $quantityDelta = (int) $validated['quantity'];
            } else {
                if ($inventory->quantity < $validated['quantity']) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['quantity' => 'Insufficient stock available.']);
                }
                $inventory->quantity -= $validated['quantity'];
                $quantityDelta = -(int) $validated['quantity'];
            }

            $inventory->notes = ($inventory->notes ? $inventory->notes . "\n" : '')
                . now()->toDateString() . ': ' . $validated['adjustment_type'] . ' '
                . $validated['quantity'] . ' - ' . $validated['reason']
                . (!empty($validated['notes']) ? ' (' . $validated['notes'] . ')' : '');
            $inventory->save();
            $inventory->movements()->create([
                'user_id' => $request->user()->id,
                'movement_type' => 'adjustment',
                'quantity_delta' => $quantityDelta,
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('consumables.inventory.index')
            ->with('success', 'Stock adjusted successfully.');
    }
}

/**
 * STOCK REQUEST CONTROLLER
 */
class StockRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = StockRequest::with(['items.consumable'])
            ->when($request->filled('status'), function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->filled('search'), function ($q, $search) {
                $q->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('request_number', 'like', "%{$search}%")
                        ->orWhere('project_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('project_type'), function ($q, $type) {
                $q->where('project_type', $type);
            });

        // Add user filtering for non-admins
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $requests = $query->latest()->paginate(20);
        
        // Use array directly instead of UsageReport::PROJECT_TYPES
        $projectTypes = [
            'borehole' => 'Borehole',
            'piped_scheme' => 'Piped Water Scheme',
            'irrigation' => 'Irrigation Scheme',
            'dam' => 'Dam'
        ];

        return view('consumables.stock-requests.index', compact('requests', 'projectTypes'));
    }

    public function create()
    {
        abort_if(auth()->user()->isViewer(), 403);
        $consumables = Consumable::where('status', 'active')
            ->with('inventory')
            ->get();
            
        $projectTypes = [
            'borehole' => 'Borehole',
            'piped_scheme' => 'Piped Water Scheme',
            'irrigation' => 'Irrigation Scheme',
            'dam' => 'Dam'
        ];

        return view('consumables.stock-requests.create', compact('consumables', 'projectTypes'));
    }

    public function store(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $request->merge(['items' => array_values(array_filter(
            $request->input('items', []),
            fn ($item) => is_array($item) && isset($item['quantity']) && (int) $item['quantity'] > 0
        ))]);
        $validated = $request->validate([
            'project_type' => 'required|in:borehole,piped_scheme,irrigation,dam',
            'project_name' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'purpose' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.consumable_id' => 'required|exists:consumables,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $stockRequest = StockRequest::create([
                'request_number' => 'SR-' . Str::random(8),
                'user_id' => auth()->id(),
                'project_type' => $validated['project_type'],
                'project_name' => $validated['project_name'],
                'village' => $validated['village'],
                'ward' => $validated['ward'],
                'purpose' => $validated['purpose'],
            ]);

            foreach ($validated['items'] as $item) {
                $stockRequest->items()->create([
                    'consumable_id' => $item['consumable_id'],
                    'requested_quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('consumables.stock-requests.index')
            ->with('success', 'Stock request submitted successfully');
    }

    public function show(Request $request, StockRequest $stockRequest)
    {
        abort_unless($request->user()->isAdmin() || (int) $stockRequest->user_id === (int) $request->user()->id, 403);
        $stockRequest->load(['items.consumable']);
        return view('consumables.stock-requests.show', compact('stockRequest'));
    }

    public function approve(Request $request, StockRequest $stockRequest)
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.approved_quantity' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($stockRequest, $validated, $request) {
            $stockRequest = StockRequest::query()->lockForUpdate()->findOrFail($stockRequest->id);
            foreach ($validated['items'] as $itemId => $itemData) {
                $requestItem = $stockRequest->items()->find($itemId);
                if ($requestItem) {
                    if ($itemData['approved_quantity'] > $requestItem->requested_quantity) {
                        throw \Illuminate\Validation\ValidationException::withMessages(["items.{$itemId}.approved_quantity" => 'Approved quantity cannot exceed requested quantity.']);
                    }
                    $requestItem->update([
                        'approved_quantity' => $itemData['approved_quantity'],
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            $allApproved = $stockRequest->items()->where('approved_quantity', '>', 0)->count();
            $totalItems = $stockRequest->items()->count();

            if ($allApproved == $totalItems) {
                $status = 'approved';
            } elseif ($allApproved > 0) {
                $status = 'partially_fulfilled';
            } else {
                $status = 'rejected';
            }

            $stockRequest->update([
                'status' => $status,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return redirect()->route('consumables.stock-requests.show', $stockRequest)
            ->with('success', 'Stock request approved successfully');
    }

    public function issue(Request $request, StockRequest $stockRequest)
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        abort_unless(in_array($stockRequest->status, ['approved', 'partially_fulfilled'], true), 422, 'Only approved stock requests can be issued.');
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.issued_quantity' => 'required|integer|min:0',
            'items.*.batch_number' => 'nullable|string',
        ]);

        DB::transaction(function () use ($stockRequest, $validated, $request) {
            $stockRequest = StockRequest::query()->lockForUpdate()->findOrFail($stockRequest->id);
            foreach ($validated['items'] as $itemId => $itemData) {
                $requestItem = $stockRequest->items()->with('consumable')->find($itemId);
                if ($requestItem && $itemData['issued_quantity'] > 0) {
                    if ($itemData['issued_quantity'] > $requestItem->approved_quantity - $requestItem->issued_quantity) {
                        throw \Illuminate\Validation\ValidationException::withMessages(["items.{$itemId}.issued_quantity" => 'Issued quantity cannot exceed the approved balance.']);
                    }

                    $inventory = Inventory::query()->where('consumable_id', $requestItem->consumable_id)->lockForUpdate()->first();
                    $currentStock = $inventory?->quantity ?? 0;
                    if ($itemData['issued_quantity'] > $currentStock) {
                        throw \Illuminate\Validation\ValidationException::withMessages(["items.{$itemId}.issued_quantity" => 'Insufficient stock available.']);
                    }

                    if (!$inventory) {
                        $inventory = Inventory::create(['consumable_id' => $requestItem->consumable_id, 'quantity' => 0]);
                    }
                    $inventory->quantity -= $itemData['issued_quantity'];
                    $inventory->save();
                    $inventory->movements()->create([
                        'user_id' => $request->user()->id,
                        'movement_type' => 'issue',
                        'quantity_delta' => -(int) $itemData['issued_quantity'],
                        'reference_type' => StockRequest::class,
                        'reference_id' => $stockRequest->id,
                        'reason' => 'Issue against approved stock request',
                    ]);

                    $requestItem->issued_quantity += $itemData['issued_quantity'];
                    $requestItem->save();
                }
            }

            $allIssued = $stockRequest->items()
                ->whereColumn('issued_quantity', '>=', 'approved_quantity')
                ->count();
            
            $totalItems = $stockRequest->items()->count();

            if ($allIssued == $totalItems) {
                $stockRequest->update(['status' => 'fulfilled']);
            } else {
                $stockRequest->update(['status' => 'partially_fulfilled']);
            }
        });

        return redirect()->route('consumables.stock-requests.show', $stockRequest)
            ->with('success', 'Stock issued successfully');
    }

    public function reject(Request $request, StockRequest $stockRequest)
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        abort_unless($stockRequest->status === 'pending', 422, 'Only pending stock requests can be rejected.');

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $stockRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('consumables.stock-requests.show', $stockRequest)
            ->with('success', 'Stock request rejected');
    }
}

/**
 * SUPPLIER CONTROLLER
 */
class SupplierController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $query = Supplier::query()
            ->when($request->filled('search'), function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->filled('status'), function ($q, $status) {
                $q->where('status', $status);
            });

        $suppliers = $query->latest()->paginate(20);

        return view('consumables.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('consumables.suppliers.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers',
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
        ]);

        Supplier::create(array_merge($validated, ['status' => 'active']));

        return redirect()->route('consumables.suppliers.index')
            ->with('success', 'Supplier created successfully');
    }

    public function edit(Supplier $supplier)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('consumables.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $supplier->update($validated);

        return redirect()->route('consumables.suppliers.index')
            ->with('success', 'Supplier updated successfully');
    }

    public function show(Supplier $supplier)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $supplier->load('purchaseOrders');
        return view('consumables.suppliers.show', compact('supplier'));
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        abort_unless($request->user()->isAdmin(), 403);
        // Check if supplier has associated consumables
        if (Inventory::where('supplier_id', $supplier->id)->exists()) {
            return redirect()->route('consumables.suppliers.index')
                ->with('error', 'Cannot delete supplier with associated consumables');
        }

        $supplier->delete();

        return redirect()->route('consumables.suppliers.index')
            ->with('success', 'Supplier deleted successfully');
    }
}

/**
 * USAGE REPORT CONTROLLER
 */
class UsageReportController extends Controller
{
    // Define constants directly in controller
    private $projectTypes = [
        'borehole' => 'Borehole',
        'piped_scheme' => 'Piped Water Scheme',
        'irrigation' => 'Irrigation Scheme',
        'dam' => 'Dam'
    ];

    private $maintenanceTypes = [
        'repair' => 'Repair',
        'maintenance' => 'Routine Maintenance',
        'rehabilitation' => 'Rehabilitation',
        'installation' => 'Installation'
    ];

    public function index(Request $request)
    {
        $query = UsageReport::with(['items.consumable'])
            ->when($request->filled('project_type'), function ($q, $type) {
                $q->where('project_type', $type);
            })
            ->when($request->filled('maintenance_type'), function ($q, $type) {
                $q->where('maintenance_type', $type);
            })
            ->when($request->filled('search'), function ($q, $search) {
                $q->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('report_number', 'like', "%{$search}%")
                        ->orWhere('project_name', 'like', "%{$search}%")
                        ->orWhere('village', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('date_from'), function ($q, $date) {
                $q->where('date_of_work', '>=', $date);
            })
            ->when($request->filled('date_to'), function ($q, $date) {
                $q->where('date_of_work', '<=', $date);
            });

        // Add user filtering for non-admins
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $reports = $query->latest()->paginate(20);

        return view('consumables.usage-reports.index', compact('reports'))
            ->with('projectTypes', $this->projectTypes)
            ->with('maintenanceTypes', $this->maintenanceTypes);
    }

    public function create()
    {
        abort_if(auth()->user()->isViewer(), 403);
        $consumables = Consumable::where('status', 'active')
            ->with('inventory')
            ->get();

        return view('consumables.usage-reports.create', compact('consumables'))
            ->with('projectTypes', $this->projectTypes)
            ->with('maintenanceTypes', $this->maintenanceTypes);
    }

    public function store(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $request->merge(['items' => array_values(array_filter(
            $request->input('items', []),
            fn ($item) => is_array($item) && isset($item['quantity_used']) && (int) $item['quantity_used'] > 0
        ))]);
        $validated = $request->validate([
            'project_type' => 'required|in:borehole,piped_scheme,irrigation,dam',
            'project_name' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'work_description' => 'required|string|max:4000',
            'maintenance_type' => 'required|in:repair,maintenance,rehabilitation,installation',
            'date_of_work' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.consumable_id' => 'required|exists:consumables,id',
            'items.*.quantity_used' => 'required|integer|min:1',
            'items.*.batch_number' => 'nullable|string',
            'items.*.purpose' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $report = UsageReport::create([
                'report_number' => 'UR-' . Str::random(8),
                'user_id' => auth()->id(),
                'project_type' => $validated['project_type'],
                'project_name' => $validated['project_name'],
                'village' => $validated['village'],
                'ward' => $validated['ward'],
                'work_description' => $validated['work_description'],
                'maintenance_type' => $validated['maintenance_type'],
                'date_of_work' => $validated['date_of_work'],
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $consumable = Consumable::find($item['consumable_id']);
                $inventory = Inventory::query()->where('consumable_id', $consumable->id)->lockForUpdate()->first();
                $currentStock = $inventory?->quantity ?? 0;
                
                if ($currentStock < $item['quantity_used']) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => "Insufficient stock for {$consumable->name}. Available: {$currentStock}, requested: {$item['quantity_used']}.",
                    ]);
                }

                $inventory->quantity -= $item['quantity_used'];
                $inventory->save();
                $inventory->movements()->create([
                    'user_id' => $request->user()->id,
                    'movement_type' => 'usage',
                    'quantity_delta' => -(int) $item['quantity_used'],
                    'reference_type' => UsageReport::class,
                    'reference_id' => $report->id,
                    'reason' => $item['purpose'] ?? 'Reported field usage',
                    'notes' => $item['batch_number'] ?? null,
                ]);

                $report->items()->create([
                    'consumable_id' => $item['consumable_id'],
                    'quantity_used' => $item['quantity_used'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'purpose' => $item['purpose'] ?? null,
                ]);
            }
        });

        return redirect()->route('consumables.usage-reports.index')
            ->with('success', 'Usage report submitted successfully');
    }

    public function show(Request $request, UsageReport $usageReport)
    {
        abort_unless($request->user()->isAdmin() || (int) $usageReport->user_id === (int) $request->user()->id, 403);
        $usageReport->load(['items.consumable']);
        
        // Get project type and maintenance type labels
        $projectTypeLabel = $this->projectTypes[$usageReport->project_type] ?? $usageReport->project_type;
        $maintenanceTypeLabel = $this->maintenanceTypes[$usageReport->maintenance_type] ?? $usageReport->maintenance_type;
        
        return view('consumables.usage-reports.show', compact('usageReport', 'projectTypeLabel', 'maintenanceTypeLabel'));
    }

    public function generateReport(Request $request)
    {
        $query = UsageReport::with(['items.consumable'])
            ->when($request->filled('project_type'), function ($q, $type) {
                $q->where('project_type', $type);
            })
            ->when($request->filled('maintenance_type'), function ($q, $type) {
                $q->where('maintenance_type', $type);
            })
            ->when($request->filled('date_from'), function ($q, $date) {
                $q->where('date_of_work', '>=', $date);
            })
            ->when($request->filled('date_to'), function ($q, $date) {
                $q->where('date_of_work', '<=', $date);
            });

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $reports = $query->get();

        // Calculate summary
        $summary = [
            'total_reports' => $reports->count(),
            'total_items_used' => 0,
            'by_project_type' => [],
            'by_maintenance_type' => [],
            'top_consumables' => [],
        ];

        $consumableUsage = [];
        
        foreach ($reports as $report) {
            // Calculate total items used for this report
            $reportTotal = $report->items->sum('quantity_used');
            $summary['total_items_used'] += $reportTotal;
            
            // Group by project type
            if (!isset($summary['by_project_type'][$report->project_type])) {
                $summary['by_project_type'][$report->project_type] = [
                    'count' => 0,
                    'items_used' => 0
                ];
            }
            $summary['by_project_type'][$report->project_type]['count']++;
            $summary['by_project_type'][$report->project_type]['items_used'] += $reportTotal;
            
            // Group by maintenance type
            if (!isset($summary['by_maintenance_type'][$report->maintenance_type])) {
                $summary['by_maintenance_type'][$report->maintenance_type] = [
                    'count' => 0,
                    'items_used' => 0
                ];
            }
            $summary['by_maintenance_type'][$report->maintenance_type]['count']++;
            $summary['by_maintenance_type'][$report->maintenance_type]['items_used'] += $reportTotal;
            
            // Calculate consumable usage
            foreach ($report->items as $item) {
                $consumableId = $item->consumable_id;
                if (!isset($consumableUsage[$consumableId])) {
                    $consumableUsage[$consumableId] = [
                        'name' => $item->consumable->name ?? 'Unknown',
                        'total_used' => 0,
                        'cost' => 0,
                    ];
                }
                $consumableUsage[$consumableId]['total_used'] += $item->quantity_used;
                $unitCost = $item->consumable->unit_cost ?? 0;
                $consumableUsage[$consumableId]['cost'] += $item->quantity_used * $unitCost;
            }
        }

        // Sort by total used
        usort($consumableUsage, function ($a, $b) {
            return $b['total_used'] <=> $a['total_used'];
        });

        $summary['top_consumables'] = array_slice($consumableUsage, 0, 10);

        return view('consumables.usage-reports.report', compact('reports', 'summary'))
            ->with('projectTypes', $this->projectTypes)
            ->with('maintenanceTypes', $this->maintenanceTypes);
    }

    public function export(Request $request)
    {
        $query = UsageReport::with(['items.consumable'])
            ->when($request->filled('project_type'), function ($q, $type) {
                $q->where('project_type', $type);
            })
            ->when($request->filled('date_from'), function ($q, $date) {
                $q->where('date_of_work', '>=', $date);
            })
            ->when($request->filled('date_to'), function ($q, $date) {
                $q->where('date_of_work', '<=', $date);
            });

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $reports = $query->get();

        $filename = 'usage_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($reports) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Report Number', 'Date', 'Project Type', 'Project Name',
                'Village', 'Ward', 'Maintenance Type', 'Work Description',
                'Consumable', 'Quantity Used', 'Batch Number', 'Purpose'
            ]);

            foreach ($reports as $report) {
                $projectTypeLabel = $this->projectTypes[$report->project_type] ?? $report->project_type;
                $maintenanceTypeLabel = $this->maintenanceTypes[$report->maintenance_type] ?? $report->maintenance_type;
                
                foreach ($report->items as $item) {
                    fputcsv($file, [
                        $report->report_number,
                        $report->date_of_work,
                        $projectTypeLabel,
                        $report->project_name,
                        $report->village,
                        $report->ward,
                        $maintenanceTypeLabel,
                        $report->work_description,
                        $item->consumable->name ?? 'Unknown',
                        $item->quantity_used,
                        $item->batch_number ?? '',
                        $item->purpose ?? '',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

/**
 * CONSUMABLE CATEGORY CONTROLLER
 */
class ConsumableCategoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $categories = ConsumableCategory::withCount('consumables')->paginate(20);
        return view('consumables.categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        return view('consumables.categories.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:consumable_categories',
            'description' => 'nullable|string',
        ]);

        ConsumableCategory::create($validated);

        return redirect()->route('consumables.categories.index')
            ->with('success', 'Category created successfully');
    }

    public function edit(Request $request, ConsumableCategory $category)
    {
        abort_unless($request->user()->isAdmin(), 403);
        return view('consumables.categories.edit', compact('category'));
    }

    public function update(Request $request, ConsumableCategory $category)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:consumable_categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('consumables.categories.index')
            ->with('success', 'Category updated successfully');
    }

    public function destroy(Request $request, ConsumableCategory $category)
    {
        abort_unless($request->user()->isAdmin(), 403);
        if ($category->consumables()->count() > 0) {
            return redirect()->route('consumables.categories.index')
                ->with('error', 'Cannot delete category with associated consumables');
        }

        $category->delete();

        return redirect()->route('consumables.categories.index')
            ->with('success', 'Category deleted successfully');
    }
}