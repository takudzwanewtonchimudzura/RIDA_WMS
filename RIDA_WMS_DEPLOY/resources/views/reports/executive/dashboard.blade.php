@extends('layouts.report')

@section('title', 'Executive Dashboard - RIDA RWIMS')

@section('content')
<div class="container-fluid">
    <!-- Report Header -->
    @include('reports.partials.report-header', [
        'title' => 'Executive Dashboard',
        'subtitle' => 'Comprehensive Water Resource Management Overview',
        'period' => $period ?? 'All Time'
    ])

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body text-center">
                    <div class="kpi-icon text-primary">
                        <i class="fas fa-water fa-2x"></i>
                    </div>
                    <h3 class="kpi-value">{{ number_format($totalBoreholes) }}</h3>
                    <p class="kpi-label">Total Boreholes</p>
                    <div class="kpi-trend text-success">
                        <i class="fas fa-arrow-up"></i> 12% vs last period
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body text-center">
                    <div class="kpi-icon text-success">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <h3 class="kpi-value">{{ number_format($averageYield, 2) }} L/s</h3>
                    <p class="kpi-label">Average Yield</p>
                    <div class="kpi-trend text-info">
                        <i class="fas fa-arrow-up"></i> 5% improvement
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body text-center">
                    <div class="kpi-icon text-warning">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h3 class="kpi-value">{{ number_format($totalBeneficiaries) }}</h3>
                    <p class="kpi-label">Beneficiaries Served</p>
                    <div class="kpi-trend text-success">
                        <i class="fas fa-arrow-up"></i> 8% increase
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body text-center">
                    <div class="kpi-icon text-info">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                    <h3 class="kpi-value">${{ number_format($totalInvestment, 2) }}</h3>
                    <p class="kpi-label">Total Investment</p>
                    <div class="kpi-trend text-success">
                        <i class="fas fa-arrow-up"></i> 15% vs budget
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Boreholes by Province
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="provinceChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Funding Agency Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="fundingChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trend-up me-2"></i>Monthly Drilling Performance
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-circle me-2"></i>Success Rate by Depth
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="depthChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>Recent Borehole Completions
                    </h5>
                    <div class="export-buttons">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportTableToExcel('recentBoreholes')">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="recentBoreholes">
                            <thead class="table-light">
                                <tr>
                                    <th>Serial No</th>
                                    <th>Province</th>
                                    <th>District</th>
                                    <th>Depth (m)</th>
                                    <th>Yield (L/s)</th>
                                    <th>Cost (USD)</th>
                                    <th>Status</th>
                                    <th>Completion Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBoreholes as $borehole)
                                <tr>
                                    <td><strong>{{ $borehole->borehole_serial_no }}</strong></td>
                                    <td>{{ $borehole->province }}</td>
                                    <td>{{ $borehole->admin_district }}</td>
                                    <td class="text-end">{{ number_format($borehole->final_borehole_depth_m ?? $borehole->final_borehole_depth ?? 0, 1) }}</td>
                                    <td class="text-end">{{ number_format($borehole->yield_lps, 2) }}</td>
                                    <td class="text-end">{{ $borehole->formatted_total_cost }}</td>
                                    <td>
                                        <span class="badge bg-{{ $borehole->approved_at ? 'success' : 'warning' }}">
                                            {{ $borehole->approved_at ? 'Approved' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td>{{ $borehole->date->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card executive-summary">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Executive Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Key Achievements</h6>
                            <ul class="list-unstyled">
                                <li>✓ {{ $achievements['boreholes_completed'] }} boreholes completed this period</li>
                                <li>✓ {{ $achievements['success_rate'] }}% success rate in drilling operations</li>
                                <li>✓ {{ $achievements['water_quality'] }}% meet water quality standards</li>
                                <li>✓ ${{ number_format($achievements['cost_savings']) }} under budget</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Recommendations</h6>
                            <ul class="list-unstyled">
                                <li>• Focus on {{ $recommendations['focus_province'] }} for next phase</li>
                                <li>• Improve yield rates in {{ $recommendations['low_yield_region'] }}</li>
                                <li>• Consider {{ $recommendations['technology'] }} for challenging geology</li>
                                <li>• Allocate additional ${{ number_format($recommendations['additional_funding']) }} for maintenance</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('provinceChart'), {
        type: 'bar',
        data: { labels: @json($provinceData['labels'] ?? []), datasets: [{ label: 'Boreholes', data: @json($provinceData['values'] ?? []), backgroundColor: '#2c7fb8' }] },
        options: { responsive: true, maintainAspectRatio: false }
    });
    new Chart(document.getElementById('fundingChart'), {
        type: 'doughnut',
        data: { labels: @json($fundingData['labels'] ?? []), datasets: [{ data: @json($fundingData['values'] ?? []), backgroundColor: ['#2c7fb8', '#28a745', '#ffc107', '#dc3545', '#6f42c1'] }] },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush

@push('styles')
<style>
.kpi-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}
.kpi-card:hover {
    transform: translateY(-5px);
}
.kpi-icon {
    margin-bottom: 1rem;
}
.kpi-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}
.kpi-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}
.kpi-trend {
    font-size: 0.8rem;
    font-weight: 600;
}
.executive-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-left: 4px solid var(--primary-color);
}
.card-header {
    background: white;
    border-bottom: 2px solid #e9ecef;
    padding: 1rem 1.5rem;
}
.card-title {
    color: var(--primary-color);
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    // Province Distribution Chart
    const provinceCtx = document.getElementById('provinceChart').getContext('2d');
    new Chart(provinceCtx, {
        type: 'bar',
        data: {
            labels: @json($provinceData['labels']),
            datasets: [{
                label: 'Boreholes',
                data: @json($provinceData['values']),
                backgroundColor: '#2c7fb8',
                borderColor: '#1a5276',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Funding Distribution Chart
    const fundingCtx = document.getElementById('fundingChart').getContext('2d');
    new Chart(fundingCtx, {
        type: 'doughnut',
        data: {
            labels: @json($fundingData['labels']),
            datasets: [{
                data: @json($fundingData['values']),
                backgroundColor: ['#2c7fb8', '#7fcdbb', '#edf8b1', '#2ca25f', '#99d8c9']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});

// Export functionality
function exportTableToExcel(tableId) {
    // Implementation for Excel export
    console.log('Exporting table:', tableId);
}
</script>
@endpush