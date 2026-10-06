<?php

namespace App\Http\Controllers;

use App\Models\Borehole;
use App\Models\BoreholeConsumableUsage;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function executiveDashboard(Request $request)
    {
        $filters = $this->getFilters($request);
        
        $data = [
            'totalBoreholes' => $this->reportService->getTotalBoreholes($filters),
            'averageYield' => $this->reportService->getAverageYield($filters),
            'totalBeneficiaries' => $this->reportService->getTotalBeneficiaries($filters),
            'totalInvestment' => $this->reportService->getTotalInvestment($filters),
            'provinceData' => $this->reportService->getBoreholesByProvince($filters),
            'fundingData' => $this->reportService->getFundingDistribution($filters),
            'recentBoreholes' => $this->reportService->getRecentBoreholes($filters, 10),
            'achievements' => $this->reportService->getKeyAchievements($filters),
            'recommendations' => $this->reportService->getRecommendations($filters),
            'period' => $this->getPeriodLabel($filters),
            // Add filter data
            'provinces' => $this->getProvinces(),
            'agencies' => $this->getFundingAgencies()
        ];

        if ($request->has('export')) {
            return $this->exportExecutiveReport($data, $filters);
        }

        return view('reports.executive.dashboard', $data);
    }

    public function operationalPerformance(Request $request)
    {
        $filters = $this->getFilters($request);
        
        $data = [
            'drillingMetrics' => $this->reportService->getDrillingMetrics($filters),
            'efficiencyData' => $this->reportService->getEfficiencyAnalysis($filters),
            'costAnalysis' => $this->reportService->getCostAnalysis($filters),
            'equipmentPerformance' => $this->reportService->getEquipmentPerformance($filters),
            'installerPerformance' => $this->reportService->getInstallerPerformance($filters),
            'period' => $this->getPeriodLabel($filters),
            // Add filter data
            'provinces' => $this->getProvinces(),
            'agencies' => $this->getFundingAgencies()
        ];

        return view('reports.boreholes.operational', $data);
    }

    public function technicalAnalysis(Request $request)
    {
        $filters = $this->getFilters($request);
        
        $data = [
            'geologyAnalysis' => $this->reportService->getGeologyAnalysis($filters),
            'waterQuality' => $this->reportService->getWaterQualityAnalysis($filters),
            'yieldAnalysis' => $this->reportService->getYieldAnalysis($filters),
            'casingAnalysis' => $this->reportService->getCasingAnalysis($filters),
            'period' => $this->getPeriodLabel($filters),
            // Add filter data
            'provinces' => $this->getProvinces(),
            'agencies' => $this->getFundingAgencies()
        ];

        return view('reports.boreholes.technical', $data);
    }

    public function consumablesPerformance(Request $request)
    {
        $filters = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'project_name' => 'nullable|string|max:255',
        ]);

        $filteredBoreholes = Borehole::query();
        $this->applyConsumablesReportFilters($filteredBoreholes, $request, $filters);

        $bitSummary = $this->getConsumableSerialSummary(
            $request,
            $filters,
            'bit',
            'dth_bit_serial_no',
            'drilling_bits_used'
        );
        $hammerSummary = $this->getConsumableSerialSummary(
            $request,
            $filters,
            'hammer',
            'hammer_serial_no',
            'hammer_rings_used'
        );
        $totalBoreholes = $filteredBoreholes->count();

        return view('reports.boreholes.consumables', compact(
            'bitSummary',
            'hammerSummary',
            'totalBoreholes',
            'filters'
        ));
    }

    private function applyConsumablesReportFilters($query, Request $request, array $filters): void
    {
        if (!$request->user()->isAdmin()) {
            $query->where('created_by', $request->user()->id);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }
        if (!empty($filters['project_name'])) {
            $query->where('project_name', 'like', '%' . trim($filters['project_name']) . '%');
        }
    }

    private function getConsumableSerialSummary(
        Request $request,
        array $filters,
        string $equipmentType,
        string $legacySerialColumn,
        string $legacyQuantityColumn
    ) {
        $detailedQuery = BoreholeConsumableUsage::query()
            ->join('boreholes', 'boreholes.id', '=', 'borehole_consumable_usages.borehole_id')
            ->where('borehole_consumable_usages.equipment_type', $equipmentType);
        $this->applyConsumablesReportFilters($detailedQuery, $request, $filters);

        $detailedRows = $detailedQuery
            ->selectRaw('borehole_consumable_usages.serial_number as serial_no, boreholes.project_name, COUNT(DISTINCT boreholes.id) as borehole_count, SUM(borehole_consumable_usages.quantity_used) as units_used')
            ->groupBy('borehole_consumable_usages.serial_number', 'boreholes.project_name')
            ->get();

        $legacyQuery = Borehole::query()
            ->whereNotNull($legacySerialColumn)
            ->where($legacySerialColumn, '<>', '')
            ->whereDoesntHave('consumableUsages', function ($query) use ($equipmentType) {
                $query->where('equipment_type', $equipmentType);
            });
        $this->applyConsumablesReportFilters($legacyQuery, $request, $filters);

        $legacyRows = $legacyQuery
            ->selectRaw("{$legacySerialColumn} as serial_no, project_name, COUNT(*) as borehole_count, SUM(COALESCE({$legacyQuantityColumn}, 0)) as units_used")
            ->groupBy($legacySerialColumn, 'project_name')
            ->get();

        return $detailedRows
            ->concat($legacyRows)
            ->groupBy(fn ($row) => $row->serial_no . "\0" . ($row->project_name ?? ''))
            ->map(fn ($rows) => (object) [
                'serial_no' => $rows->first()->serial_no,
                'project_name' => $rows->first()->project_name,
                'borehole_count' => $rows->sum('borehole_count'),
                'units_used' => $rows->sum('units_used'),
            ])
            ->sortBy('serial_no')
            ->values();
    }

    public function geographicDistribution(Request $request)
    {
        $filters = $this->getFilters($request);
        
        $data = [
            'boreholeCoordinates' => $this->reportService->getBoreholeCoordinates($filters),
            'yieldDistribution' => $this->reportService->getYieldDistribution($filters),
            'waterQualityMap' => $this->reportService->getWaterQualityMap($filters),
            'regionalAnalysis' => $this->reportService->getRegionalAnalysis($filters),
            'period' => $this->getPeriodLabel($filters),
            // Add filter data
            'provinces' => $this->getProvinces(),
            'agencies' => $this->getFundingAgencies()
        ];

        return view('reports.boreholes.geographic', $data);
    }

    public function financialReports(Request $request)
    {
        $filters = $this->getFilters($request);
        
        $data = [
            'financialMetrics' => $this->reportService->getFinancialMetrics($filters),
            'costAnalysis' => $this->reportService->getCostAnalysis($filters),
            'investmentDistribution' => $this->reportService->getInvestmentDistribution($filters),
            'period' => $this->getPeriodLabel($filters),
            // Add filter data
            'provinces' => $this->getProvinces(),
            'agencies' => $this->getFundingAgencies()
        ];

        return view('reports.financial.index', $data);
    }

    
    // In your ReportController - analyticsDashboard method
public function analyticsDashboard(Request $request)
{
    $filters = $this->getFilters($request);
    
    // Get filtered data
    $query = Borehole::query();
    
    // Apply filters
    if (!empty($filters['date_from'])) {
        $query->whereDate('date', '>=', $filters['date_from']);
    }
    if (!empty($filters['date_to'])) {
        $query->whereDate('date', '<=', $filters['date_to']);
    }
    if (!empty($filters['province'])) {
        $query->where('province', $filters['province']);
    }
    if (!empty($filters['created_by'])) {
        $query->where('created_by', $filters['created_by']);
    }
    if (!empty($filters['district'])) {
        $query->where('admin_district', $filters['district']);
    }
    if (!empty($filters['village'])) {
        $query->where('village', 'like', '%' . trim($filters['village']) . '%');
    }
    if (!empty($filters['siting_agency'])) {
        $query->where('siting_agency', $filters['siting_agency']);
    }
    if (!empty($filters['funding_agency'])) {
        $query->where('funding_agency', $filters['funding_agency']);
    }

    // Calculate yield distribution
    $yieldDistribution = $this->calculateYieldDistribution($query);

    $data = [
        'filters' => $filters,
        'totalBoreholes' => $query->count(),
        'averageYield' => $query->avg(DB::raw('COALESCE(NULLIF(blowing_yield, 0), yield_lps)')),
        'averageDepth' => $query->avg(DB::raw('COALESCE(NULLIF(final_borehole_depth_m, 0), final_borehole_depth)')),
        'provincesCount' => (clone $query)->distinct()->count('province'),
        'provinces' => $this->getProvinces(),
        'agencies' => $this->getFundingAgencies(),
        'recentBoreholes' => $query->latest()->take(10)->get(),
        'boreholesByProvince' => (clone $query)->reorder()->groupBy('province')
            ->selectRaw('province, count(*) as count')
            ->pluck('count', 'province')
            ->toArray(),
        'yieldDistribution' => $yieldDistribution, // Add this line
    ];

    return view('reports.analytics.dashboard', $data);
}

// yield distribution
    private function calculateYieldDistribution($query)
    {
        // Define yield ranges (in liters per second)
        $yieldExpression = 'COALESCE(NULLIF(blowing_yield, 0), yield_lps)';
        $yieldRanges = [
            '0-5' => [0, 5],
            '5-10' => [5, 10],
            '10-20' => [10, 20],
            '20-50' => [20, 50],
            '50+' => [50, null],
        ];

        $distribution = [];

        foreach ($yieldRanges as $range => $limits) {
            $rangeQuery = clone $query;
            
            if ($limits[1] === null) {
                // For the last range (50+)
                $count = $rangeQuery->whereRaw($yieldExpression . ' >= ?', [$limits[0]])->count();
            } else {
                $count = $rangeQuery
                    ->whereRaw($yieldExpression . ' >= ? AND ' . $yieldExpression . ' < ?', $limits)
                    ->count();
            }
            
            $distribution[$range] = $count;
        }

        return $distribution;
    }

    private function getFilters(Request $request)
    {
        $filters = [
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'province' => $request->get('province'),
            'district' => $request->get('district'),
            'village' => $request->get('village'),
            'siting_agency' => $request->get('siting_agency'),
            'funding_agency' => $request->get('funding_agency'),
            'contractor' => $request->get('contractor'),
            'status' => $request->get('status')
        ];

        if ($request->user() && $request->user()->isOperator()) {
            $filters['created_by'] = $request->user()->id;
        }

        return $filters;
    }

    private function getPeriodLabel($filters)
    {
        if ($filters['date_from'] && $filters['date_to']) {
            return Carbon::parse($filters['date_from'])->format('M d, Y') . ' - ' . 
                   Carbon::parse($filters['date_to'])->format('M d, Y');
        }
        
        return 'All Time';
    }

    private function getProvinces()
    {
        $query = Borehole::query();
        if (auth()->check() && auth()->user()->isOperator()) {
            $query->where('created_by', auth()->id());
        }
        return $query->distinct()
            ->whereNotNull('province')
            ->orderBy('province')
            ->pluck('province');
    }

    private function getFundingAgencies()
    {
        $query = Borehole::query();
        if (auth()->check() && auth()->user()->isOperator()) {
            $query->where('created_by', auth()->id());
        }
        return $query->distinct()
            ->whereNotNull('funding_agency')
            ->orderBy('funding_agency')
            ->pluck('funding_agency');
    }

    private function getDistricts()
    {
        $query = Borehole::query();
        if (auth()->check() && auth()->user()->isOperator()) {
            $query->where('created_by', auth()->id());
        }
        return $query->distinct()
            ->whereNotNull('admin_district')
            ->orderBy('admin_district')
            ->pluck('admin_district');
    }

    private function getContractors()
    {
        $query = Borehole::query();
        if (auth()->check() && auth()->user()->isOperator()) {
            $query->where('created_by', auth()->id());
        }
        return $query->distinct()
            ->whereNotNull('contractor')
            ->orderBy('contractor')
            ->pluck('contractor');
    }

    // Export methods
    public function exportExecutive(Request $request, $format)
    {
        $filters = $this->getFilters($request);
        // Implementation for executive report export
        return response()->json(['message' => 'Executive export to be implemented for ' . $format]);
    }

    public function exportOperational(Request $request, $format)
    {
        $filters = $this->getFilters($request);
        // Implementation for operational report export
        return response()->json(['message' => 'Operational export to be implemented for ' . $format]);
    }

    public function exportTechnical(Request $request, $format)
    {
        $filters = $this->getFilters($request);
        // Implementation for technical report export
        return response()->json(['message' => 'Technical export to be implemented for ' . $format]);
    }

    public function exportGeographic(Request $request, $format)
    {
        $filters = $this->getFilters($request);
        // Implementation for geographic report export
        return response()->json(['message' => 'Geographic export to be implemented for ' . $format]);
    }

    // API Data methods
    public function getExecutiveData(Request $request)
    {
        $filters = $this->getFilters($request);
        
        return response()->json([
            'totalBoreholes' => $this->reportService->getTotalBoreholes($filters),
            'averageYield' => $this->reportService->getAverageYield($filters),
            'totalInvestment' => $this->reportService->getTotalInvestment($filters),
            'provinceData' => $this->reportService->getBoreholesByProvince($filters),
            'fundingData' => $this->reportService->getFundingDistribution($filters),
        ]);
    }

    public function getOperationalData(Request $request)
    {
        $filters = $this->getFilters($request);
        
        return response()->json([
            'drillingMetrics' => $this->reportService->getDrillingMetrics($filters),
            'efficiencyData' => $this->reportService->getEfficiencyAnalysis($filters),
            'costAnalysis' => $this->reportService->getCostAnalysis($filters),
        ]);
    }
}