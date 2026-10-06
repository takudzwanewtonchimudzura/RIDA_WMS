<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borehole;
use App\Models\Dam;
use App\Models\PipedWaterScheme;

class DashboardController extends Controller
{

    private function getWaterQualityData(Request $request)
    {
        $start = now()->startOfMonth()->subMonths(11);
        $readings = $this->scopeRecords($request, Borehole::query())
            ->whereNotNull('ph')
            ->whereDate('date', '>=', $start->toDateString())
            ->orderByDesc('date')
            ->first(['ph', 'tds_mg_l', 'turbidity_ntu', 'date']);
        $monthlyReadings = $this->scopeRecords($request, Borehole::query())
            ->whereNotNull('ph')
            ->whereDate('date', '>=', $start->toDateString())
            ->get(['ph', 'date'])
            ->groupBy(fn ($reading) => $reading->date?->format('Y-m'))
            ->map(fn ($group) => round((float) $group->avg('ph'), 2));
        $months = collect(range(0, 11))->map(fn ($offset) => $start->copy()->addMonths($offset));

        return [
            'current_ph' => $readings?->ph,
            'current_tds' => $readings?->tds_mg_l,
            'current_turbidity' => $readings?->turbidity_ntu,
            'last_test_date' => $readings?->date?->format('M j, Y'),
            'monthly_labels' => $months->map(fn ($month) => $month->format('M Y')),
            'monthly_ph' => $months->map(fn ($month) => $monthlyReadings[$month->format('Y-m')] ?? null),
        ];
    }

    public function index(Request $request)
    {
        // Get basic statistics
        $stats = $this->getDashboardStats($request);
        $alerts = $this->getSystemAlerts($request);
        $metrics = $this->getPerformanceMetrics($request);
        $recent_activities = $this->getRecentActivities($request);
        $water_quality = $this->getWaterQualityData($request);

        return view('dashboard', compact('stats', 'alerts', 'metrics', 'recent_activities', 'water_quality'));
    }

    private function getDashboardStats(Request $request)
    {
        $boreholes = $this->scopeRecords($request, Borehole::query());
        $dams = $this->scopeRecords($request, Dam::query());
        $pipedSchemes = $this->scopeRecords($request, PipedWaterScheme::query());
        $totalBoreholes = (clone $boreholes)->count();
        $operationalBoreholes = (clone $boreholes)->whereNotNull('approved_at')->count();
        $totalDams = (clone $dams)->count();
        $damsNeedingAttention = (clone $dams)->where(function ($query) {
            $query->whereIn('wall_condition', ['poor', 'critical'])
                ->orWhereIn('spillway_condition', ['poor', 'critical']);
        })->count();
        $totalPWS = (clone $pipedSchemes)->count();
        $functionalPWS = (clone $pipedSchemes)->where('functionality', 'Functional')->count();
        $pwsFunctionalRate = $totalPWS > 0 ? round(($functionalPWS / $totalPWS) * 100) : 0;

        return [
            // Boreholes
            'total_boreholes' => $totalBoreholes,
            'operational_boreholes' => $operationalBoreholes,
            'borehole_approval_rate' => $this->calculateBoreholeApprovalRate($request),
            
            // Dams
            'total_dams' => $totalDams,
            'dams_needing_attention' => $damsNeedingAttention,
            'dam_condition_rate' => $totalDams > 0
                ? round(((clone $dams)->whereIn('wall_condition', ['good', 'fair'])->whereIn('spillway_condition', ['good', 'fair'])->count() / $totalDams) * 100)
                : 0,
            
            // Piped Water Schemes
            'total_pws' => $totalPWS,
            'pws_functional_rate' => $pwsFunctionalRate,
            
            // System Health
            'population_served' => $this->calculatePopulationServed($request),
            'borehole_data_completeness' => $this->calculateBoreholeDataCompleteness($request),
        ];
    }

    private function calculateBoreholeApprovalRate(Request $request)
    {
        $query = $this->scopeRecords($request, Borehole::query());
        $totalBoreholes = (clone $query)->count();
        $approvedBoreholes = (clone $query)->whereNotNull('approved_at')->count();
        
        if ($totalBoreholes === 0) {
            return 0;
        }
        
        return round(($approvedBoreholes / $totalBoreholes) * 100);
    }

    private function calculatePopulationServed(Request $request)
    {
        // Calculate based on all infrastructure types
        $totalPopulation = 0;
        
        // Boreholes population
        $totalPopulation += (int) $this->scopeRecords($request, Borehole::query())->whereNotNull('approved_at')->sum('number_of_beneficiaries');
        $totalPopulation += (int) $this->scopeRecords($request, PipedWaterScheme::query())->sum('population_served');
        
        return $totalPopulation;
    }

    private function calculateBoreholeDataCompleteness(Request $request)
    {
        $query = $this->scopeRecords($request, Borehole::query());
        $totalBoreholes = (clone $query)->count();
        if ($totalBoreholes === 0) {
            return 0;
        }

        $completeBoreholes = (clone $query)->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotNull('blowing_yield')
            ->count();

        return round(($completeBoreholes / $totalBoreholes) * 100);
    }

    private function getSystemAlerts(Request $request)
    {
        $alerts = [];
        
        // Check for unapproved boreholes
        $unapprovedCount = $this->scopeRecords($request, Borehole::query())->whereNull('approved_at')->count();
        if ($unapprovedCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'clock',
                'title' => 'Pending Borehole Approvals',
                'message' => "{$unapprovedCount} boreholes awaiting approval",
                'timestamp' => 'Requires attention',
                'priority' => 'medium'
            ];
        }
        
        // Check for boreholes without coordinates
        $boreholesWithoutCoords = $this->scopeRecords($request, Borehole::query())
            ->where(fn ($query) => $query->whereNull('latitude')->orWhereNull('longitude'))
                                        ->count();
        
        if ($boreholesWithoutCoords > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'map-marker-alt',
                'title' => 'Missing Location Data',
                'message' => "{$boreholesWithoutCoords} boreholes missing GPS coordinates",
                'timestamp' => 'Data quality issue',
                'priority' => 'medium'
            ];
        }
        
        $damsNeedingAttention = $this->scopeRecords($request, Dam::query())->where(function ($query) {
            $query->whereIn('wall_condition', ['poor', 'critical'])
                ->orWhereIn('spillway_condition', ['poor', 'critical']);
        })->count();
        if ($damsNeedingAttention > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'title' => 'Dams need attention',
                'message' => "{$damsNeedingAttention} dams have poor or critical condition ratings",
                'timestamp' => 'Current condition records',
                'priority' => 'high',
            ];
        }

        return $alerts;
    }

    private function getPerformanceMetrics(Request $request)
    {
        $boreholes = $this->scopeRecords($request, Borehole::query());
        $totalBoreholes = (clone $boreholes)->count();
        
        $approvedBoreholes = (clone $boreholes)->whereNotNull('approved_at')->count();
        $approvalRate = $totalBoreholes > 0 ? ($approvedBoreholes / $totalBoreholes) * 100 : 0;
        
        $completeBoreholes = (clone $boreholes)->whereNotNull('latitude')
                                   ->whereNotNull('longitude')
                                   ->whereNotNull('blowing_yield')
                                   ->count();
        $completenessRate = $totalBoreholes > 0 ? ($completeBoreholes / $totalBoreholes) * 100 : 0;

        $dams = $this->scopeRecords($request, Dam::query());
        $totalDams = (clone $dams)->count();
        $goodDams = (clone $dams)->whereIn('wall_condition', ['good', 'fair'])
            ->whereIn('spillway_condition', ['good', 'fair'])
            ->count();
        $pipedSchemes = $this->scopeRecords($request, PipedWaterScheme::query());
        $totalPipedSchemes = (clone $pipedSchemes)->count();
        $functionalPipedSchemes = (clone $pipedSchemes)->where('functionality', 'Functional')->count();

        return [
            'borehole_approval_rate' => round($approvalRate),
            'borehole_data_completeness' => round($completenessRate),
            'dam_condition_rate' => $totalDams > 0 ? round(($goodDams / $totalDams) * 100) : 0,
            'pws_functional_rate' => $totalPipedSchemes > 0 ? round(($functionalPipedSchemes / $totalPipedSchemes) * 100) : 0,
        ];
    }

    public function getChartData(Request $request)
    {
        $boreholes = $this->scopeRecords($request, Borehole::query());
        $dams = $this->scopeRecords($request, Dam::query());
        $pipedSchemes = $this->scopeRecords($request, PipedWaterScheme::query());

        return response()->json([
            'boreholes' => $boreholes->count(),
            'dams' => $dams->count(),
            'piped_water_schemes' => $pipedSchemes->count(),
            'population_served' => $this->calculatePopulationServed($request),
            'water_quality' => $this->getWaterQualityData($request),
        ]);
    }

    private function getRecentActivities(Request $request)
    {
        $activities = [];
        
        // Get recent borehole activities
        $recentBoreholes = $this->scopeRecords($request, Borehole::with('creator'))
                                 ->latest()
                                 ->take(2)
                                 ->get();
        
        foreach ($recentBoreholes as $borehole) {
            $status = $borehole->approved_at ? 'Approved' : 'Pending Approval';
            $statusColor = $borehole->approved_at ? 'success' : 'warning';
            
            $activities[] = [
                'icon' => 'water',
                'type_color' => 'primary',
                'title' => 'Borehole Record ' . ($borehole->approved_at ? 'Approved' : 'Created'),
                'description' => "BH#{$borehole->borehole_serial_no} in {$borehole->locality}",
                'time' => $borehole->created_at->diffForHumans(),
                'status' => $status,
                'status_color' => $statusColor
            ];
        }
        
        $recentDamMaintenance = \App\Models\DamMaintenance::with('dam')
            ->when($request->user()->isOperator(), fn ($query) => $query->where('created_by', $request->user()->id))
            ->latest('maintenance_date')->first();
        if ($recentDamMaintenance) {
            $activities[] = [
                'icon' => 'water',
                'type_color' => 'success',
                'title' => 'Dam maintenance recorded',
                'description' => ($recentDamMaintenance->dam?->name ?? 'Dam') . ': ' . $recentDamMaintenance->maintenance_type,
                'time' => $recentDamMaintenance->created_at->diffForHumans(),
                'status' => str_replace('_', ' ', ucfirst($recentDamMaintenance->status)),
                'status_color' => $recentDamMaintenance->status === 'completed' ? 'success' : 'warning',
            ];
        }

        return $activities;
    }

    private function scopeRecords(Request $request, $query)
    {
        if ($request->user()->isOperator()) {
            $query->where('created_by', $request->user()->id);
        }

        return $query;
    }
}