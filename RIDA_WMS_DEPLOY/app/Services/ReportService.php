<?php

namespace App\Services;

use App\Models\Borehole;
use App\Models\GeologyLayer;
use App\Models\CasingDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportService
{
    private const YIELD_EXPRESSION = 'COALESCE(NULLIF(blowing_yield, 0), yield_lps)';

    protected $cacheDuration;

    public function __construct()
    {
        $this->cacheDuration = config('reporting.cache.duration', 3600);
    }

    // ==================== EXECUTIVE DASHBOARD METHODS ====================

    public function getTotalBoreholes(array $filters = [])
    {
        $cacheKey = 'total_boreholes_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            return $this->applyFilters(Borehole::query(), $filters)->count();
        });
    }

    public function getAverageYield(array $filters = [])
    {
        $cacheKey = 'average_yield_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            return $this->applyFilters(Borehole::query(), $filters)
                ->whereRaw(self::YIELD_EXPRESSION . ' > 0')
                ->avg(DB::raw(self::YIELD_EXPRESSION)) ?? 0;
        });
    }

    public function getTotalBeneficiaries(array $filters = [])
    {
        $cacheKey = 'total_beneficiaries_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            return $this->applyFilters(Borehole::query(), $filters)
                ->sum('number_of_beneficiaries') ?? 0;
        });
    }

    public function getTotalInvestment(array $filters = [])
    {
        $cacheKey = 'total_investment_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            return $this->applyFilters(Borehole::query(), $filters)
                ->sum('total_cost') ?? 0;
        });
    }

    public function getBoreholesByProvince(array $filters = [])
    {
        $cacheKey = 'boreholes_by_province_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $data = $this->applyFilters(Borehole::query(), $filters)
                ->select('province', DB::raw('COUNT(*) as count'))
                ->whereNotNull('province')
                ->groupBy('province')
                ->orderBy('count', 'desc')
                ->get();

            return [
                'labels' => $data->pluck('province'),
                'values' => $data->pluck('count')
            ];
        });
    }

    public function getFundingDistribution(array $filters = [])
    {
        $cacheKey = 'funding_distribution_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $data = $this->applyFilters(Borehole::query(), $filters)
                ->select('funding_agency', DB::raw('COUNT(*) as count'))
                ->whereNotNull('funding_agency')
                ->groupBy('funding_agency')
                ->orderBy('count', 'desc')
                ->get();

            return [
                'labels' => $data->pluck('funding_agency'),
                'values' => $data->pluck('count')
            ];
        });
    }

    public function getRecentBoreholes(array $filters = [], $limit = 10)
    {
        $cacheKey = 'recent_boreholes_' . md5(serialize($filters)) . '_' . $limit;
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters, $limit) {
            return $this->applyFilters(Borehole::query(), $filters)
                ->with(['creator', 'approver'])
                ->orderBy('date', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getKeyAchievements(array $filters = [])
    {
        $cacheKey = 'key_achievements_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $query = $this->applyFilters(Borehole::query(), $filters);
            $total = $query->count();
            
            return [
                'boreholes_completed' => $total,
                'success_rate' => $total > 0 ? 
                    round(((clone $query)->whereRaw(self::YIELD_EXPRESSION . ' > 0')->count() / $total) * 100, 1) : 0,
                'water_quality' => $total > 0 ? 
                    round(($query->whereBetween('ph', [6.5, 8.5])->count() / $total) * 100, 1) : 0,
                'cost_savings' => $this->calculateCostSavings($filters),
                'average_completion_time' => $this->getAverageCompletionTime($filters)
            ];
        });
    }

    public function getRecommendations(array $filters = [])
    {
        $cacheKey = 'recommendations_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $lowYieldProvinces = $this->applyFilters(Borehole::query(), $filters)
                ->select('province', DB::raw('AVG(' . self::YIELD_EXPRESSION . ') as avg_yield'))
                ->groupBy('province')
                ->orderBy('avg_yield')
                ->first();

            $highCostRegions = $this->applyFilters(Borehole::query(), $filters)
                ->select('province', DB::raw('AVG(total_cost / NULLIF(final_borehole_depth, 0)) as cost_per_meter'))
                ->groupBy('province')
                ->orderBy('cost_per_meter', 'desc')
                ->first();

            return [
                'focus_province' => $lowYieldProvinces->province ?? 'all regions',
                'low_yield_region' => $lowYieldProvinces->province ?? 'specific areas',
                'high_cost_region' => $highCostRegions->province ?? 'certain areas',
                'technology' => 'advanced geological surveying and real-time monitoring',
                'additional_funding' => 75000,
                'maintenance_priority' => $this->getMaintenancePriorityAreas($filters)
            ];
        });
    }

    // ==================== OPERATIONAL PERFORMANCE METHODS ====================

    public function getDrillingMetrics(array $filters = [])
    {
        $cacheKey = 'drilling_metrics_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $query = $this->applyFilters(Borehole::query(), $filters);
            $total = $query->count();
            
            return [
                'average_depth' => round($query->avg('final_borehole_depth') ?? 0, 1),
                'average_days' => round($query->avg('total_drilling_days') ?? 0, 1),
                'success_rate' => $total > 0 ? 
                    round(((clone $query)->whereRaw(self::YIELD_EXPRESSION . ' > 0')->count() / $total) * 100, 1) : 0,
                'cost_per_meter' => round($query->avg(DB::raw('total_cost / NULLIF(final_borehole_depth, 0)')) ?? 0, 2),
                'development_hours' => round($query->avg('development_hours') ?? 0, 1),
                'rig_utilization' => $this->calculateRigUtilization($filters)
            ];
        });
    }

    public function getEfficiencyAnalysis(array $filters = [])
    {
        $cacheKey = 'efficiency_analysis_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $data = $this->applyFilters(Borehole::query(), $filters)
                ->select('province', 
                    DB::raw('AVG(total_drilling_days) as avg_days'),
                    DB::raw('AVG(final_borehole_depth / NULLIF(total_drilling_days, 0)) as meters_per_day'),
                    DB::raw('(SUM(CASE WHEN ' . self::YIELD_EXPRESSION . ' > 0 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as success_rate'),
                    DB::raw('AVG(total_cost / NULLIF(final_borehole_depth, 0)) as cost_per_meter')
                )
                ->whereNotNull('province')
                ->groupBy('province')
                ->orderBy('meters_per_day', 'desc')
                ->get();

            return [
                'labels' => $data->pluck('province'),
                'drilling_days' => $data->pluck('avg_days'),
                'efficiency_rates' => $data->pluck('meters_per_day'),
                'success_rates' => $data->pluck('success_rate'),
                'cost_efficiency' => $data->pluck('cost_per_meter')
            ];
        });
    }

    public function getCostAnalysis(array $filters = [])
    {
        $cacheKey = 'cost_analysis_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $data = $this->applyFilters(Borehole::query(), $filters)
                ->select('contractor',
                    DB::raw('AVG(total_cost / NULLIF(final_borehole_depth, 0)) as cost_per_meter'),
                    DB::raw('COUNT(*) as total_projects'),
                    DB::raw('AVG(total_drilling_days) as avg_days'),
                    DB::raw('(SUM(CASE WHEN ' . self::YIELD_EXPRESSION . ' > 0 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as success_rate')
                )
                ->whereNotNull('contractor')
                ->groupBy('contractor')
                ->orderBy('cost_per_meter')
                ->get();

            return [
                'contractors' => $data->pluck('contractor'),
                'costs_per_meter' => $data->pluck('cost_per_meter'),
                'total_projects' => $data->pluck('total_projects'),
                'performance_scores' => $data->map(function($item) {
                    return round((
                        (1 / max($item->cost_per_meter, 1)) * 40 + 
                        ($item->success_rate / 100) * 40 +
                        (1 / max($item->avg_days, 1)) * 20
                    ) * 10, 1);
                })
            ];
        });
    }

    public function getEquipmentPerformance(array $filters = [])
    {
        $cacheKey = 'equipment_performance_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $rigData = $this->applyFilters(Borehole::query(), $filters)
                ->select('rig_type',
                    DB::raw('COUNT(*) as usage_count'),
                    DB::raw('AVG(final_borehole_depth / NULLIF(total_drilling_days, 0)) as avg_depth_per_day'),
                    DB::raw('(SUM(CASE WHEN ' . self::YIELD_EXPRESSION . ' > 0 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as success_rate'),
                    DB::raw('AVG(development_hours) as avg_development_hours')
                )
                ->whereNotNull('rig_type')
                ->groupBy('rig_type')
                ->get();

            return $rigData->map(function($rig) {
                $efficiencyScore = round((
                    ($rig->avg_depth_per_day / 15) * 40 + // Normalize against ideal 15m/day
                    ($rig->success_rate / 100) * 40 +
                    (1 / max($rig->avg_development_hours / 24, 1)) * 20
                ) * 100, 1);

                return [
                    'type' => $rig->rig_type,
                    'usage' => $rig->usage_count,
                    'avg_depth_per_day' => round($rig->avg_depth_per_day, 1),
                    'success_rate' => round($rig->success_rate, 1),
                    'avg_development_hours' => round($rig->avg_development_hours, 1),
                    'maintenance_cost' => $this->estimateMaintenanceCost($rig->rig_type, $rig->usage_count),
                    'efficiency_score' => min($efficiencyScore, 100)
                ];
            })->sortByDesc('efficiency_score')->values();
        });
    }

    public function getInstallerPerformance(array $filters = [])
    {
        $cacheKey = 'installer_performance_' . md5(serialize($filters));

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($filters) {
            $yield = self::YIELD_EXPRESSION;
            return $this->applyFilters(Borehole::query(), $filters)
                ->whereNotNull('installed_by_name')
                ->where('installed_by_name', '<>', '')
                ->whereIn('pump_installation_status', ['successful', 'unsuccessful'])
                ->select('installed_by_name', 'installed_by_organisation')
                ->selectRaw('COUNT(*) as installation_count')
                ->selectRaw("SUM(CASE WHEN pump_installation_status = 'successful' THEN 1 ELSE 0 END) as successful_installations")
                ->selectRaw("(SUM(CASE WHEN pump_installation_status = 'successful' THEN 100.0 ELSE 0 END) / COUNT(*)) as success_rate")
                ->groupBy('installed_by_name', 'installed_by_organisation')
                ->orderByDesc('success_rate')
                ->orderBy('installed_by_name')
                ->get();
        });
    }

    // ==================== TECHNICAL ANALYSIS METHODS ====================

    public function getWaterQualityAnalysis(array $filters = [])
    {
        $cacheKey = 'water_quality_analysis_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $query = $this->applyFilters(Borehole::query(), $filters);
            $total = $query->count();
            
            $phOptimal = $query->whereBetween('ph', [6.5, 8.5])->count();
            $tdsOptimal = $query->where('tds_mg_l', '<', 600)->count();
            $ecOptimal = $query->where('electrical_conductivity', '<', 1000)->orWhereNull('electrical_conductivity')->count();

            return [
                'compliance_rate' => $total > 0 ? round(($phOptimal / $total) * 100, 1) : 0,
                'ph_distribution' => [
                    'excellent' => $query->whereBetween('ph', [6.5, 8.5])->count(),
                    'good' => $query->whereBetween('ph', [6.0, 9.0])->whereNotBetween('ph', [6.5, 8.5])->count(),
                    'fair' => $query->where('ph', '<', 6.0)->orWhere('ph', '>', 9.0)->count(),
                    'poor' => $query->whereNull('ph')->count()
                ],
                'tds_analysis' => [
                    'excellent' => $query->where('tds_mg_l', '<', 300)->count(),
                    'good' => $query->whereBetween('tds_mg_l', [300, 600])->count(),
                    'fair' => $query->whereBetween('tds_mg_l', [600, 900])->count(),
                    'poor' => $query->where('tds_mg_l', '>', 900)->orWhereNull('tds_mg_l')->count()
                ],
                'electrical_conductivity' => [
                    'low' => $query->where('electrical_conductivity', '<', 500)->count(),
                    'medium' => $query->whereBetween('electrical_conductivity', [500, 1500])->count(),
                    'high' => $query->where('electrical_conductivity', '>', 1500)->orWhereNull('electrical_conductivity')->count()
                ],
                'good_quality' => $total > 0 ? 
                    round(($query->whereBetween('ph', [6.5, 8.5])
                        ->where('tds_mg_l', '<', 600)
                        ->count() / $total) * 100, 1) : 0,
                'optimal_ph' => $total > 0 ? round(($phOptimal / $total) * 100, 1) : 0,
                'tds_compliance' => $total > 0 ? round(($tdsOptimal / $total) * 100, 1) : 0,
                'ec_compliance' => $total > 0 ? round(($ecOptimal / $total) * 100, 1) : 0
            ];
        });
    }

    public function getYieldAnalysis(array $filters = [])
    {
        $cacheKey = 'yield_analysis_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $query = $this->applyFilters(Borehole::query(), $filters);
            $yieldExpression = DB::raw(self::YIELD_EXPRESSION);
            
            return [
                'average_yield' => round((clone $query)->avg($yieldExpression) ?? 0, 2),
                'max_yield' => (clone $query)->max($yieldExpression) ?? 0,
                'min_yield' => (clone $query)->whereRaw(self::YIELD_EXPRESSION . ' > 0')->min($yieldExpression) ?? 0,
                'median_yield' => $this->calculateMedianYield($filters),
                'distribution' => $this->getYieldRangeCounts($filters),
                'yield_categories' => ['<2 L/s', '2-5 L/s', '5-10 L/s', '10-20 L/s', '>20 L/s']
            ];
        });
    }

    public function getGeologyAnalysis(array $filters = [])
    {
        $cacheKey = 'geology_analysis_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $boreholeIds = $this->applyFilters(Borehole::query(), $filters)->pluck('id');
            
            $layers = GeologyLayer::whereIn('borehole_id', $boreholeIds)->get();
            
            $waterStrikeDepths = $layers->whereNotNull('water_strike')
                ->pluck('description_layer_depth_to')
                ->filter()
                ->values();

            return [
                'total_layers' => $layers->count(),
                'avg_water_strike_depth' => $waterStrikeDepths->isNotEmpty() ? 
                    round($waterStrikeDepths->avg(), 1) : 'N/A',
                'water_strike_range' => $waterStrikeDepths->isNotEmpty() ? 
                    round($waterStrikeDepths->min(), 1) . 'm - ' . round($waterStrikeDepths->max(), 1) . 'm' : 'N/A',
                'productive_layers' => 'Sandstone & Fractured Rock',
                'recommended_depth' => '45-75m',
                'penetration_rates' => $this->analyzePenetrationRates($layers),
                'layer_distribution' => $this->analyzeLayerDistribution($layers)
            ];
        });
    }

    public function getCasingAnalysis(array $filters = [])
    {
        $cacheKey = 'casing_analysis_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            $boreholeIds = $this->applyFilters(Borehole::query(), $filters)->pluck('id');
            
            $casings = CasingDetail::whereIn('borehole_id', $boreholeIds)
                ->select('position', 'description', 
                    DB::raw('AVG(diameter) as avg_diameter'),
                    DB::raw('AVG(depth_to - depth_from) as avg_thickness'),
                    DB::raw('COUNT(*) as usage_count')
                )
                ->groupBy('position', 'description')
                ->orderBy('position')
                ->get();

            $totalCasings = $casings->sum('usage_count');

            return $casings->map(function($casing) use ($totalCasings) {
                return [
                    'position' => $this->getPositionName($casing->position),
                    'diameter' => round($casing->avg_diameter),
                    'avg_thickness' => round($casing->avg_thickness, 1),
                    'type' => $casing->description,
                    'usage_percentage' => $totalCasings > 0 ? 
                        round(($casing->usage_count / $totalCasings) * 100, 1) : 0,
                    'usage_count' => $casing->usage_count
                ];
            });
        });
    }

    // ==================== GEOGRAPHIC ANALYSIS METHODS ====================

    public function getRegionalAnalysis(array $filters = [])
    {
        $cacheKey = 'regional_analysis_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            return $this->applyFilters(Borehole::query(), $filters)
                ->select('province as name',
                    DB::raw('COUNT(*) as total_boreholes'),
                    DB::raw('AVG(' . self::YIELD_EXPRESSION . ') as avg_yield'),
                    DB::raw('(SUM(CASE WHEN ' . self::YIELD_EXPRESSION . ' > 0 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as success_rate'),
                    DB::raw('AVG(COALESCE(NULLIF(final_borehole_depth_m, 0), final_borehole_depth)) as avg_depth'),
                    DB::raw('AVG(CASE WHEN ph BETWEEN 6.5 AND 8.5 THEN 10 
                                 WHEN ph BETWEEN 6.0 AND 9.0 THEN 7 
                                 ELSE 3 END) as quality_score'),
                    DB::raw('SUM(total_cost) as investment'),
                    DB::raw('AVG(total_cost / NULLIF(final_borehole_depth, 0)) as cost_per_meter'),
                    DB::raw('SUM(number_of_beneficiaries) as total_beneficiaries')
                )
                ->whereNotNull('province')
                ->groupBy('province')
                ->orderBy('total_boreholes', 'desc')
                ->get()
                ->map(function($region) {
                    return [
                        'name' => $region->name,
                        'total_boreholes' => $region->total_boreholes,
                        'avg_yield' => round($region->avg_yield, 2),
                        'success_rate' => round($region->success_rate, 1),
                        'avg_depth' => round($region->avg_depth, 1),
                        'quality_score' => round($region->quality_score, 1),
                        'investment' => round($region->investment, 2),
                        'cost_per_meter' => round($region->cost_per_meter, 2),
                        'total_beneficiaries' => $region->total_beneficiaries,
                        'efficiency_score' => $this->calculateRegionEfficiencyScore($region)
                    ];
                });
        });
    }

    public function getBoreholeCoordinates(array $filters = [])
    {
        $cacheKey = 'borehole_coordinates_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
            return $this->applyFilters(Borehole::query(), $filters)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('id', 'borehole_serial_no', 'province', 'latitude', 'longitude', 
                         'blowing_yield', 'yield_lps', 'final_borehole_depth_m', 'final_borehole_depth', 'ph', 'approved_at')
                ->get()
                ->map(function($borehole) {
                    return [
                        'id' => $borehole->id,
                        'serial_no' => $borehole->borehole_serial_no,
                        'province' => $borehole->province,
                        'lat' => (float)$borehole->latitude,
                        'lng' => (float)$borehole->longitude,
                        'yield' => $borehole->blowing_yield ?? $borehole->yield_lps,
                        'depth' => $borehole->final_borehole_depth_m ?? $borehole->final_borehole_depth,
                        'ph' => $borehole->ph,
                        'status' => $borehole->approved_at ? 'approved' : 'pending',
                        'popup_content' => $this->generateMapPopup($borehole)
                    ];
                });
        });
    }

    // ==================== PRIVATE HELPER METHODS ====================

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }
        
        if (!empty($filters['province'])) {
            $query->where('province', $filters['province']);
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

        if (!empty($filters['contractor'])) {
            $query->where('contractor', $filters['contractor']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'approved') {
                $query->whereNotNull('approved_at');
            } elseif ($filters['status'] === 'pending') {
                $query->whereNull('approved_at');
            }
        }

        return $query;
    }

    private function calculateCostSavings(array $filters)
    {
        // This would compare actual costs against budgeted costs
        // For now, return a placeholder calculation
        $avgCostPerMeter = $this->applyFilters(Borehole::query(), $filters)
            ->avg(DB::raw('total_cost / NULLIF(final_borehole_depth, 0)')) ?? 0;
        
        $industryAverage = 250; // USD per meter industry average
        $totalMeters = $this->applyFilters(Borehole::query(), $filters)
            ->sum('final_borehole_depth') ?? 0;
        
        return max(($industryAverage - $avgCostPerMeter) * $totalMeters, 0);
    }

    private function calculateMedianYield(array $filters)
    {
        $yields = $this->applyFilters(Borehole::query(), $filters)
            ->whereRaw(self::YIELD_EXPRESSION . ' > 0')
            ->selectRaw(self::YIELD_EXPRESSION . ' as effective_yield')
            ->pluck('effective_yield')
            ->sort()
            ->values();

        $count = $yields->count();
        if ($count === 0) return 0;

        $mid = floor($count / 2);
        return $count % 2 ? $yields[$mid] : ($yields[$mid-1] + $yields[$mid]) / 2;
    }

    private function getAverageCompletionTime(array $filters)
    {
        return $this->applyFilters(Borehole::query(), $filters)
            ->whereNotNull('drilling_start_date')
            ->whereNotNull('drilling_end_date')
            ->avg(DB::raw('(drilling_end_date - drilling_start_date)')) ?? 0;
    }

    private function calculateRigUtilization(array $filters)
    {
        $totalDays = $this->applyFilters(Borehole::query(), $filters)
            ->sum('total_drilling_days') ?? 0;
        
        // Assuming 25 working days per month per rig
        $estimatedRigMonths = $totalDays / 25;
        return min(round(($estimatedRigMonths / 12) * 100, 1), 100);
    }

    private function estimateMaintenanceCost($rigType, $usageCount)
    {
        $costs = [
            'DTH Rig 1000' => 12500,
            'Rotary Rig 800' => 9800,
            'Crawler Rig 1200' => 15200,
            'Hydraulic Rig 600' => 8500
        ];

        $baseCost = $costs[$rigType] ?? 10000;
        return $baseCost * ($usageCount / 50); // Scale with usage
    }

    private function getMaintenancePriorityAreas(array $filters)
    {
        $highUsageRigs = $this->applyFilters(Borehole::query(), $filters)
            ->select('rig_type', DB::raw('COUNT(*) as usage_count'))
            ->whereNotNull('rig_type')
            ->groupBy('rig_type')
            ->orderBy('usage_count', 'desc')
            ->limit(3)
            ->get()
            ->pluck('rig_type')
            ->toArray();

        return array_slice($highUsageRigs, 0, 2);
    }

    private function analyzePenetrationRates($layers)
    {
        $rates = $layers->whereNotNull('penetration_rates_rate')
            ->pluck('penetration_rates_rate')
            ->countBy();

        return $rates->sortDesc()->take(5);
    }

    private function analyzeLayerDistribution($layers)
    {
        return $layers->groupBy(function($layer) {
            return floor($layer->description_layer_depth_to / 10) * 10;
        })->map(function($group, $depthRange) {
            return [
                'depth_range' => $depthRange . '-' . ($depthRange + 10) . 'm',
                'count' => $group->count(),
                'water_strikes' => $group->whereNotNull('water_strike')->count()
            ];
        })->sortBy('depth_range')->values();
    }

    private function getPositionName($position)
    {
        $positions = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth'];
        return $positions[$position - 1] ?? "Position $position";
    }

    private function calculateRegionEfficiencyScore($region)
    {
        $score = (
            ($region->success_rate / 100) * 30 +
            (min($region->avg_yield / 10, 1)) * 25 +
            (1 / max($region->cost_per_meter / 200, 1)) * 25 +
            ($region->quality_score / 10) * 20
        );
        
        return round($score, 1);
    }

    private function generateMapPopup($borehole)
    {
        $serialNumber = e($borehole->borehole_serial_no);
        $province = e($borehole->province);
        return "
            <div class='map-popup'>
                <h6>{$serialNumber}</h6>
                <p><strong>Province:</strong> {$province}</p>
                <p><strong>Blowing Yield:</strong> " . (($borehole->blowing_yield ?? $borehole->yield_lps) ? ($borehole->blowing_yield ?? $borehole->yield_lps) . ' L/s' : 'N/A') . "</p>
                <p><strong>Depth:</strong> " . ($borehole->final_borehole_depth ? $borehole->final_borehole_depth . 'm' : 'N/A') . "</p>
                <p><strong>pH:</strong> " . ($borehole->ph ?? 'N/A') . "</p>
                <p><strong>Status:</strong> " . ($borehole->approved_at ? 'Approved' : 'Pending') . "</p>
            </div>
        ";
    }

    // Add these methods to the existing ReportService class

public function getYieldDistribution(array $filters = [])
{
    $cacheKey = 'yield_distribution_' . md5(serialize($filters));
    
    return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
        $query = $this->applyFilters(Borehole::query(), $filters);
        
        return [
            'yield_ranges' => ['0-2', '2-5', '5-10', '10-20', '20+'],
            'counts' => $this->getYieldRangeCounts($filters),
        ];
    });
}

public function getWaterQualityMap(array $filters = [])
{
    $cacheKey = 'water_quality_map_' . md5(serialize($filters));
    
    return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
        $query = $this->applyFilters(Borehole::query(), $filters)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'borehole_serial_no', 'province', 'latitude', 'longitude', 'ph', 'tds_mg_l', 'electrical_conductivity');

        return $query->get()->map(function($borehole) {
            $qualityScore = $this->calculateWaterQualityScore($borehole);
            
            return [
                'id' => $borehole->id,
                'serial_no' => $borehole->borehole_serial_no,
                'province' => $borehole->province,
                'lat' => (float)$borehole->latitude,
                'lng' => (float)$borehole->longitude,
                'ph' => $borehole->ph,
                'tds' => $borehole->tds_mg_l,
                'ec' => $borehole->electrical_conductivity,
                'quality_score' => $qualityScore,
                'quality_category' => $this->getQualityCategory($qualityScore),
                'marker_color' => $this->getQualityMarkerColor($qualityScore)
            ];
        });
    });
}

public function getFinancialMetrics(array $filters = [])
{
    $cacheKey = 'financial_metrics_' . md5(serialize($filters));
    
    return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
        $query = $this->applyFilters(Borehole::query(), $filters);
        
        return [
            'total_investment' => $query->sum('total_cost') ?? 0,
            'average_cost_per_borehole' => $query->avg('total_cost') ?? 0,
            'average_cost_per_meter' => $query->avg(DB::raw('total_cost / NULLIF(final_borehole_depth, 0)')) ?? 0,
            'cost_per_beneficiary' => $this->calculateCostPerBeneficiary($filters),
            'return_on_investment' => $this->calculateROI($filters)
        ];
    });
}

public function getInvestmentDistribution(array $filters = [])
{
    $cacheKey = 'investment_distribution_' . md5(serialize($filters));
    
    return Cache::remember($cacheKey, $this->cacheDuration, function() use ($filters) {
        $data = $this->applyFilters(Borehole::query(), $filters)
            ->select('funding_agency', 
                DB::raw('SUM(total_cost) as total_investment'),
                DB::raw('COUNT(*) as project_count'),
                DB::raw('AVG(total_cost) as avg_project_cost')
            )
            ->whereNotNull('funding_agency')
            ->groupBy('funding_agency')
            ->orderBy('total_investment', 'desc')
            ->get();

        return [
            'agencies' => $data->pluck('funding_agency'),
            'investments' => $data->pluck('total_investment'),
            'project_counts' => $data->pluck('project_count'),
            'avg_costs' => $data->pluck('avg_project_cost')
        ];
    });
}

// Private helper methods for the new functions
private function calculateWaterQualityScore($borehole)
{
    $score = 0;
    $factors = 0;

    if ($borehole->ph) {
        if ($borehole->ph >= 6.5 && $borehole->ph <= 8.5) {
            $score += 10;
        } elseif ($borehole->ph >= 6.0 && $borehole->ph <= 9.0) {
            $score += 7;
        } else {
            $score += 3;
        }
        $factors++;
    }

    if ($borehole->tds_mg_l) {
        if ($borehole->tds_mg_l < 300) {
            $score += 10;
        } elseif ($borehole->tds_mg_l < 600) {
            $score += 8;
        } elseif ($borehole->tds_mg_l < 900) {
            $score += 5;
        } else {
            $score += 2;
        }
        $factors++;
    }

    if ($borehole->electrical_conductivity) {
        if ($borehole->electrical_conductivity < 500) {
            $score += 10;
        } elseif ($borehole->electrical_conductivity < 1500) {
            $score += 7;
        } else {
            $score += 3;
        }
        $factors++;
    }

    return $factors > 0 ? round($score / $factors, 1) : 0;
}

private function getQualityCategory($score)
{
    if ($score >= 8) return 'Excellent';
    if ($score >= 6) return 'Good';
    if ($score >= 4) return 'Fair';
    return 'Poor';
}

private function getQualityMarkerColor($score)
{
    if ($score >= 8) return '#28a745'; // Green
    if ($score >= 6) return '#ffc107'; // Yellow
    if ($score >= 4) return '#fd7e14'; // Orange
    return '#dc3545'; // Red
}

private function calculateCostPerBeneficiary(array $filters)
{
    $totalCost = $this->applyFilters(Borehole::query(), $filters)->sum('total_cost') ?? 0;
    $totalBeneficiaries = $this->applyFilters(Borehole::query(), $filters)->sum('number_of_beneficiaries') ?? 0;
    
    return $totalBeneficiaries > 0 ? round($totalCost / $totalBeneficiaries, 2) : 0;
}

private function calculateROI(array $filters)
{
    // Simplified ROI calculation based on water yield and beneficiaries
    $totalInvestment = $this->applyFilters(Borehole::query(), $filters)->sum('total_cost') ?? 0;
    $totalYield = $this->applyFilters(Borehole::query(), $filters)->sum(DB::raw(self::YIELD_EXPRESSION)) ?? 0;
    $totalBeneficiaries = $this->applyFilters(Borehole::query(), $filters)->sum('number_of_beneficiaries') ?? 0;
    
    if ($totalInvestment <= 0) return 0;
    
    // Simple metric: (Yield * Beneficiaries) / Investment
    $valueGenerated = ($totalYield * 86400 * 0.3) + ($totalBeneficiaries * 100); // Daily water value + beneficiary value
    return round(($valueGenerated / $totalInvestment) * 100, 2);
}

    // ==================== DATA CLEANUP METHODS ====================

    public function clearReportCache()
    {
        Cache::flush();
        return true;
    }

    public function getCacheStats()
    {
        return [
            'total_boreholes' => Borehole::count(),
            'cached_reports' => count(Cache::get('cache_keys', [])),
            'cache_size' => $this->getCacheSize()
        ];
    }

    private function getCacheSize()
    {
        // Implementation would depend on cache driver
        return 'N/A';
    }

    private function getYieldRangeCounts(array $filters): array
    {
        $yield = self::YIELD_EXPRESSION;
        $counts = $this->applyFilters(Borehole::query(), $filters)
            ->selectRaw("SUM(CASE WHEN {$yield} < 2 OR {$yield} IS NULL THEN 1 ELSE 0 END) as under_two")
            ->selectRaw("SUM(CASE WHEN {$yield} >= 2 AND {$yield} < 5 THEN 1 ELSE 0 END) as two_to_five")
            ->selectRaw("SUM(CASE WHEN {$yield} >= 5 AND {$yield} < 10 THEN 1 ELSE 0 END) as five_to_ten")
            ->selectRaw("SUM(CASE WHEN {$yield} >= 10 AND {$yield} <= 20 THEN 1 ELSE 0 END) as ten_to_twenty")
            ->selectRaw("SUM(CASE WHEN {$yield} > 20 THEN 1 ELSE 0 END) as over_twenty")
            ->first();

        return [
            (int) ($counts->under_two ?? 0),
            (int) ($counts->two_to_five ?? 0),
            (int) ($counts->five_to_ten ?? 0),
            (int) ($counts->ten_to_twenty ?? 0),
            (int) ($counts->over_twenty ?? 0),
        ];
    }
}