@extends('layouts.report')

@section('title', 'Financial Reports - RIDA RWIMS')
@section('report-title', 'Financial Analysis & Investment Reports')
@section('report-period', $period)

@section('content')
@include('reports.partials.filters')

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-primary">
                    <i class="fas fa-dollar-sign fa-2x"></i>
                </div>
                <h3 class="kpi-value">${{ number_format($financialMetrics['total_investment'] ?? 0, 2) }}</h3>
                <p class="kpi-label">Total Investment</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-success">
                    <i class="fas fa-ruler-combined fa-2x"></i>
                </div>
                <h3 class="kpi-value">${{ number_format($financialMetrics['average_cost_per_meter'] ?? 0, 2) }}</h3>
                <p class="kpi-label">Avg Cost per Meter</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-info">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h3 class="kpi-value">${{ number_format($financialMetrics['cost_per_beneficiary'] ?? 0, 2) }}</h3>
                <p class="kpi-label">Cost per Beneficiary</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-warning">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h3 class="kpi-value">{{ number_format($financialMetrics['return_on_investment'] ?? 0, 1) }}%</h3>
                <p class="kpi-label">ROI Estimate</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Investment by Funding Agency</h5>
            <canvas id="investmentChart" height="300"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Projects by Funding Agency</h5>
            <canvas id="projectsChart" height="300"></canvas>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="chart-title">Financial Performance Summary</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Funding Agency</th>
                            <th>Total Investment</th>
                            <th>Number of Projects</th>
                            <th>Average Project Cost</th>
                            <th>Cost per Meter</th>
                            <th>Efficiency Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($investmentDistribution['agencies'] as $index => $agency)
                        <tr>
                            <td><strong>{{ $agency }}</strong></td>
                            <td>${{ number_format($investmentDistribution['investments'][$index], 2) }}</td>
                            <td>{{ $investmentDistribution['project_counts'][$index] }}</td>
                            <td>${{ number_format($investmentDistribution['avg_costs'][$index], 2) }}</td>
                            <td>${{ number_format($costAnalysis['costs_per_meter'][$index] ?? 0, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $costAnalysis['performance_scores'][$index] > 80 ? 'success' : ($costAnalysis['performance_scores'][$index] > 60 ? 'warning' : 'danger') }}">
                                    {{ number_format($costAnalysis['performance_scores'][$index] ?? 0, 1) }}%
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Investment Chart
        initBarChart('investmentChart', {
            labels: @json($investmentDistribution['agencies'] ?? []),
            datasets: [{
                label: 'Total Investment (USD)',
                data: @json($investmentDistribution['investments'] ?? []),
                backgroundColor: chartColors.primary
            }]
        });

        // Projects Chart
        initPieChart('projectsChart', {
            labels: @json($investmentDistribution['agencies'] ?? []),
            datasets: [{
                data: @json($investmentDistribution['project_counts'] ?? []),
                backgroundColor: [
                    chartColors.primary,
                    chartColors.secondary,
                    chartColors.success,
                    chartColors.warning,
                    chartColors.info
                ]
            }]
        });
    });
</script>
@endpush