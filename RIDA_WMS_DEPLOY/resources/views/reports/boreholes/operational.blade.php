@extends('layouts.report')

@section('title', 'Operational Performance Report - RIDA RWIMS')
@section('report-title', 'Operational Performance Analysis')
@section('report-period', $period)

@section('content')
<!-- Filters -->
@include('reports.partials.filters', [
    'provinces' => $provinces ?? [],
    'agencies' => $agencies ?? []
])

<!-- Key Metrics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-primary">
                    <i class="fas fa-ruler-combined fa-2x"></i>
                </div>
                <h3 class="kpi-value">{{ number_format($drillingMetrics['average_depth'] ?? 0, 1) }}m</h3>
                <p class="kpi-label">Average Depth</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-success">
                    <i class="fas fa-calendar-day fa-2x"></i>
                </div>
                <h3 class="kpi-value">{{ number_format($drillingMetrics['average_days'] ?? 0, 1) }}</h3>
                <p class="kpi-label">Avg. Drilling Days</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-info">
                    <i class="fas fa-percentage fa-2x"></i>
                </div>
                <h3 class="kpi-value">{{ number_format($drillingMetrics['success_rate'] ?? 0, 1) }}%</h3>
                <p class="kpi-label">Success Rate</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-warning">
                    <i class="fas fa-dollar-sign fa-2x"></i>
                </div>
                <h3 class="kpi-value">${{ number_format($drillingMetrics['cost_per_meter'] ?? 0, 2) }}</h3>
                <p class="kpi-label">Cost per Meter</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Drilling Efficiency by Province</h5>
            <canvas id="efficiencyChart" height="300"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Cost Analysis by Contractor</h5>
            <canvas id="costChart" height="300"></canvas>
        </div>
    </div>
</div>

<!-- Equipment Performance -->
<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="chart-title">Equipment Utilization & Performance</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Equipment Type</th>
                            <th>Total Usage</th>
                            <th>Avg. Depth/Day</th>
                            <th>Success Rate</th>
                            <th>Maintenance Cost</th>
                            <th>Efficiency Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipmentPerformance as $equipment)
                        <tr>
                            <td>{{ $equipment['type'] }}</td>
                            <td>{{ $equipment['usage'] }} boreholes</td>
                            <td>{{ number_format($equipment['avg_depth_per_day'], 1) }}m</td>
                            <td>{{ number_format($equipment['success_rate'], 1) }}%</td>
                            <td>${{ number_format($equipment['maintenance_cost'], 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $equipment['efficiency_score'] > 80 ? 'success' : ($equipment['efficiency_score'] > 60 ? 'warning' : 'danger') }}">
                                    {{ number_format($equipment['efficiency_score'], 1) }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<section class="row mt-4" aria-labelledby="installer-success-heading">
    <div class="col-12">
        <div class="chart-container">
            <h5 id="installer-success-heading" class="chart-title">Pump installation success by installer</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Installer</th><th>Organisation</th><th class="text-end">Recorded installations</th><th class="text-end">Successful installations</th><th class="text-end">Success rate</th></tr></thead>
                    <tbody>
                        @forelse($installerPerformance as $installer)
                            <tr>
                                <td>{{ $installer->installed_by_name }}</td>
                                <td>{{ $installer->installed_by_organisation ?: 'Not recorded' }}</td>
                                <td class="text-end">{{ number_format($installer->installation_count) }}</td>
                                <td class="text-end">{{ number_format($installer->successful_installations) }}</td>
                                <td class="text-end">{{ number_format($installer->success_rate, 1) }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No installer records match the selected filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Recommendations -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Operational Recommendations</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Optimize drilling schedules in high-performing regions</li>
                    <li>Consider equipment upgrades for underperforming rigs</li>
                    <li>Implement preventive maintenance programs</li>
                    <li>Standardize operational procedures across teams</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Efficiency Chart
        initBarChart('efficiencyChart', {
            labels: @json($efficiencyData['labels'] ?? []),
            datasets: [{
                label: 'Average Drilling Days',
                data: @json($efficiencyData['drilling_days'] ?? []),
                backgroundColor: chartColors.primary
            }, {
                label: 'Success Rate (%)',
                data: @json($efficiencyData['success_rates'] ?? []),
                backgroundColor: chartColors.success,
                type: 'line',
                yAxisID: 'y1'
            }]
        }, {
            scales: {
                y: {
                    title: { display: true, text: 'Drilling Days' }
                },
                y1: {
                    position: 'right',
                    title: { display: true, text: 'Success Rate (%)' },
                    min: 0,
                    max: 100
                }
            }
        });

        // Cost Chart
        initBarChart('costChart', {
            labels: @json($costAnalysis['contractors'] ?? []),
            datasets: [{
                label: 'Cost per Meter (USD)',
                data: @json($costAnalysis['costs_per_meter'] ?? []),
                backgroundColor: chartColors.warning
            }]
        });
    });
</script>
@endpush