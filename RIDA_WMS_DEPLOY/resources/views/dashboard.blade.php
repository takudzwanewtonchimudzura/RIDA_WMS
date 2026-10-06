@extends('layouts.app')

@section('title', 'RIDA RWIMS Dashboard')

@section('page-title', 'RIDA RWIMS Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <!-- Boreholes Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('boreholes.index') }}" class="card-link text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Boreholes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_boreholes'] ?? 0 }}</div>
                                <div class="mt-2">
                                    <span class="badge bg-success text-white">
                                        <i class="fas fa-arrow-up me-1"></i>
                                        {{ $stats['operational_boreholes'] ?? 0 }} Operational
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-water fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Dams Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('dams.index') }}" class="card-link text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Dams</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_dams'] ?? 0 }}</div>
                                <div class="mt-2">
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $stats['dams_needing_attention'] ?? 0 }} Need Attention
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-water fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Piped Water Schemes Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('piped-water-schemes.index') }}" class="card-link text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2 card-hover">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Piped Water Schemes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_pws'] ?? 0 }}</div>
                                <div class="mt-2">
                                    <span class="badge bg-info text-white">
                                        {{ $stats['pws_functional_rate'] ?? 0 }}% Functional
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-faucet fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Borehole Data Completeness Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Borehole Data Completeness</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['borehole_data_completeness'] ?? 0 }}%</div>
                            <div class="mt-2">
                                <span class="badge bg-{{ $stats['borehole_data_completeness'] >= 80 ? 'success' : ($stats['borehole_data_completeness'] >= 60 ? 'warning' : 'danger') }} text-white">
                                    {{ $stats['borehole_data_completeness'] >= 80 ? 'Good' : ($stats['borehole_data_completeness'] >= 60 ? 'Incomplete' : 'Poor') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heartbeat fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Alerts & Pain Points -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exclamation-triangle me-2"></i>Critical System Alerts
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert-container">
                        @foreach($alerts as $alert)
                        <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-{{ $alert['icon'] }} me-3 fa-lg"></i>
                                <div class="flex-grow-1">
                                    <strong>{{ $alert['title'] }}</strong> - {{ $alert['message'] }}
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $alert['timestamp'] }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $alert['priority'] == 'high' ? 'danger' : ($alert['priority'] == 'medium' ? 'warning' : 'info') }}">
                                    {{ ucfirst($alert['priority']) }} Priority
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Performance Metrics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Borehole approval rate</span>
                            <span class="font-weight-bold">{{ $metrics['borehole_approval_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                               <div class="progress-bar bg-{{ $metrics['borehole_approval_rate'] >= 80 ? 'success' : ($metrics['borehole_approval_rate'] >= 60 ? 'warning' : 'danger') }}" 
                                   role="progressbar" style="width: {{ $metrics['borehole_approval_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Borehole record completeness</span>
                            <span class="font-weight-bold">{{ $metrics['borehole_data_completeness'] ?? 0 }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                               <div class="progress-bar bg-{{ $metrics['borehole_data_completeness'] >= 80 ? 'success' : ($metrics['borehole_data_completeness'] >= 60 ? 'warning' : 'danger') }}" 
                                   role="progressbar" style="width: {{ $metrics['borehole_data_completeness'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Dams in good/fair condition</span>
                            <span class="font-weight-bold">{{ $metrics['dam_condition_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                               <div class="progress-bar bg-{{ $metrics['dam_condition_rate'] >= 80 ? 'success' : ($metrics['dam_condition_rate'] >= 60 ? 'warning' : 'danger') }}" 
                                   role="progressbar" style="width: {{ $metrics['dam_condition_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Water Quality Trends & Infrastructure Distribution -->
    <div class="row mb-4">
        <!-- Water Quality Trends - Left Column -->
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tint me-2"></i>Water Quality Trends
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">View Details</a>
                            <a class="dropdown-item" href="#">Export Data</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Settings</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="waterQualityChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-right">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Latest recorded pH
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $water_quality['current_ph'] ?? 'No data' }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-right">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        TDS (mg/L)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $water_quality['current_tds'] ?? 'No data' }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Turbidity (NTU)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $water_quality['current_turbidity'] ?? 'No data' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Infrastructure Distribution - Right Column -->
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Infrastructure Distribution
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">View Details</a>
                            <a class="dropdown-item" href="#">Export Data</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Settings</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="infrastructureDistributionChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-right">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Boreholes
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_boreholes'] ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-right">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Dams
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_dams'] ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    PWS
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_pws'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Service Coverage -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks me-2"></i>Recent Activities
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($recent_activities as $activity)
                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-sm rounded-circle bg-{{ $activity['type_color'] }} text-white">
                                    <i class="fas fa-{{ $activity['icon'] }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold">{{ $activity['title'] }}</div>
                                <small class="text-muted">{{ $activity['description'] }}</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">{{ $activity['time'] }}</small>
                                <br>
                                <span class="badge bg-{{ $activity['status_color'] }}">{{ $activity['status'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Asset approval and functionality
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="serviceCoverageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Water Quality Trends Chart
        const waterQualityCtx = document.getElementById('waterQualityChart').getContext('2d');
        const waterQualityChart = new Chart(waterQualityCtx, {
            type: 'line',
            data: {
                labels: @json($water_quality['monthly_labels']),
                datasets: [{
                    label: 'Average pH',
                    data: @json($water_quality['monthly_ph']),
                    borderColor: '#14665b',
                    backgroundColor: 'rgba(20, 102, 91, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 14,
                        title: {
                            display: true,
                            text: 'pH'
                        }
                    }
                }
            }
        });

        // Infrastructure Distribution Chart
        const infrastructureCtx = document.getElementById('infrastructureDistributionChart').getContext('2d');
        const infrastructureChart = new Chart(infrastructureCtx, {
            type: 'doughnut',
            data: {
                labels: ['Boreholes', 'Dams', 'Piped Water Schemes'],
                datasets: [{
                    data: [
                        {{ $stats['total_boreholes'] ?? 0 }},
                        {{ $stats['total_dams'] ?? 0 }},
                        {{ $stats['total_pws'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#3b82f6', // Blue for Boreholes
                        '#10b981', // Green for Dams
                        '#8b5cf6'  // Purple for PWS
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // Service Coverage Chart
        const coverageCtx = document.getElementById('serviceCoverageChart').getContext('2d');
        const coverageChart = new Chart(coverageCtx, {
            type: 'bar',
            data: {
                labels: ['Boreholes', 'Dams', 'Piped Water'],
                datasets: [{
                    label: 'Recorded rate (%)',
                    data: [
                        {{ $stats['borehole_approval_rate'] ?? 0 }},
                        {{ $stats['dam_condition_rate'] ?? 0 }},
                        {{ $stats['pws_functional_rate'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#8b5cf6'
                    ],
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Records meeting indicator (%)'
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Add hover effects to clickable cards
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'all 0.3s ease';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>

<style>
    .card-hover {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .card-hover:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .card-link:hover {
        text-decoration: none;
    }

    .avatar {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .border-left-primary {
        border-left: 4px solid #3b82f6 !important;
    }

    .border-left-success {
        border-left: 4px solid #10b981 !important;
    }

    .border-left-info {
        border-left: 4px solid #8b5cf6 !important;
    }

    .border-left-warning {
        border-left: 4px solid #f59e0b !important;
    }

    .alert-container .alert {
        border-left: 4px solid;
        margin-bottom: 0.5rem;
    }

    .card {
        border: none;
        border-radius: 12px;
    }

    .card-header {
        border-bottom: 1px solid #e3e6f0;
        background-color: #f8f9fc;
    }

    .progress {
        border-radius: 10px;
    }

    .list-group-item {
        border: none;
        padding: 1rem 0;
    }

    .list-group-item:first-child {
        padding-top: 0;
    }

    .list-group-item:last-child {
        padding-bottom: 0;
    }

    .border-right {
        border-right: 1px solid #e3e6f0 !important;
    }

    @media (max-width: 768px) {
        .border-right {
            border-right: none !important;
            border-bottom: 1px solid #e3e6f0 !important;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
    }
</style>
@endsection