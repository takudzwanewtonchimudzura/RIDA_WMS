@extends('layouts.report')

@section('title', 'Technical Analysis Report - RIDA RWIMS')
@section('report-title', 'Technical Analysis & Geological Assessment')
@section('report-period', $period)

@section('content')
@include('reports.partials.filters')

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-info">
                    <i class="fas fa-mountain fa-2x"></i>
                </div>
                <h3 class="kpi-value">{{ $geologyAnalysis['total_layers'] ?? 0 }}</h3>
                <p class="kpi-label">Geological Layers Analyzed</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-success">
                    <i class="fas fa-tint fa-2x"></i>
                </div>
                <h3 class="kpi-value">{{ number_format($waterQuality['compliance_rate'] ?? 0, 1) }}%</h3>
                <p class="kpi-label">Water Quality Compliance</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card kpi-card">
            <div class="card-body text-center">
                <div class="kpi-icon text-primary">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h3 class="kpi-value">{{ number_format($yieldAnalysis['average_yield'] ?? 0, 2) }} L/s</h3>
                <p class="kpi-label">Average Yield</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Water Quality Distribution</h5>
            <canvas id="qualityChart" height="250"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Yield Distribution Analysis</h5>
            <canvas id="yieldChart" height="250"></canvas>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="chart-title">Geological Layer Analysis</h5>
            <canvas id="geologyChart" height="300"></canvas>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Casing Specifications</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Diameter (mm)</th>
                            <th>Average Depth (m)</th>
                            <th>Type</th>
                            <th>Usage %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($casingAnalysis as $casing)
                        <tr>
                            <td>{{ $casing['position'] ?? '' }}</td>
                            <td>{{ $casing['diameter'] ?? '' }}</td>
                            <td>{{ isset($casing['avg_depth']) && !is_null($casing['avg_depth']) ? number_format($casing['avg_depth'], 1) : '0.0' }}</td>
                            <td>
                                <span class="badge bg-{{ ($casing['type'] ?? 'Plain') == 'Slotted' ? 'info' : 'secondary' }}">
                                    {{ $casing['type'] ?? 'Plain' }}
                                </span>
                            </td>
                            <td>{{ isset($casing['usage_percentage']) ? number_format($casing['usage_percentage'], 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Technical Recommendations</h5>
            <div class="alert alert-info">
                <h6><i class="fas fa-flask me-2"></i>Geological Insights</h6>
                <ul class="mb-2">
                    <li>Primary water strikes at {{ $geologyAnalysis['avg_water_strike_depth'] ?? 'N/A' }}m depth</li>
                    <li>Most productive layers: {{ $geologyAnalysis['productive_layers'] ?? 'N/A' }}</li>
                    <li>Recommended drilling depth: {{ $geologyAnalysis['recommended_depth'] ?? 'N/A' }}m</li>
                </ul>
            </div>
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle me-2"></i>Quality Assurance</h6>
                <ul class="mb-0">
                    <li>{{ $waterQuality['good_quality'] ?? 0 }}% meet all quality standards</li>
                    <li>pH levels within optimal range: {{ $waterQuality['optimal_ph'] ?? 0 }}%</li>
                    <li>TDS compliance: {{ $waterQuality['tds_compliance'] ?? 0 }}%</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Water Quality Chart
        initPieChart('qualityChart', {
            labels: ['Excellent', 'Good', 'Fair', 'Poor'],
            datasets: [{
                data: @json(array_values($waterQuality['ph_distribution'] ?? [])),
                backgroundColor: [
                    chartColors.success,
                    chartColors.info,
                    chartColors.warning,
                    chartColors.danger
                ]
            }]
        });

        // Yield Distribution Chart
        initBarChart('yieldChart', {
            labels: ['0-2 L/s', '2-5 L/s', '5-10 L/s', '10-20 L/s', '20+ L/s'],
            datasets: [{
                label: 'Number of Boreholes',
                data: @json($yieldAnalysis['distribution'] ?? []),
                backgroundColor: chartColors.primary
            }]
        });
    });
</script>
@endpush