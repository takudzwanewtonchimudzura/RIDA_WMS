{{-- resources/views/reports/analytics/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-chart-bar me-2"></i>Analytics Dashboard
                </h4>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('analytics.dashboard') }}">
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" name="date_from" id="date_from" 
                                       class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" name="date_to" id="date_to" 
                                       class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="province" class="form-label">Province</label>
                                <select name="province" id="province" class="form-select">
                                    <option value="">All Provinces</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province }}" 
                                            {{ ($filters['province'] ?? '') == $province ? 'selected' : '' }}>
                                            {{ $province }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="district" class="form-label">District</label>
                                <input type="text" name="district" id="district" value="{{ $filters['district'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="village" class="form-label">Village</label>
                                <input type="text" name="village" id="village" value="{{ $filters['village'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="siting_agency" class="form-label">Siting Agency</label>
                                <select name="siting_agency" id="siting_agency" class="form-select">
                                    <option value="">All agencies</option>
                                    <option value="Government" @selected(($filters['siting_agency'] ?? '') === 'Government')>Government</option>
                                    <option value="PISP" @selected(($filters['siting_agency'] ?? '') === 'PISP')>PISP</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="funding_agency" class="form-label">Funding Agency</label>
                                <select name="funding_agency" id="funding_agency" class="form-select">
                                    <option value="">All Agencies</option>
                                    @foreach($agencies as $agency)
                                        <option value="{{ $agency }}" 
                                            {{ ($filters['funding_agency'] ?? '') == $agency ? 'selected' : '' }}>
                                            {{ $agency }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i> Apply Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card widget-box-one border border-primary">
                <div class="card-body">
                    <div class="float-right">
                        <i class="fas fa-water widget-icon"></i>
                    </div>
                    <div class="wigdet-one-content">
                        <p class="m-0 text-uppercase font-weight-bold text-muted" title="Statistics">
                            Total Boreholes
                        </p>
                        <h2><span>{{ $totalBoreholes ?? 0 }}</span></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card widget-box-one border border-success">
                <div class="card-body">
                    <div class="float-right">
                        <i class="fas fa-tint widget-icon"></i>
                    </div>
                    <div class="wigdet-one-content">
                        <p class="m-0 text-uppercase font-weight-bold text-muted">
                            Average Yield
                        </p>
                        <h2><span>{{ number_format($averageYield ?? 0, 2) }} l/s</span></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card widget-box-one border border-info">
                <div class="card-body">
                    <div class="float-right">
                        <i class="fas fa-ruler-vertical widget-icon"></i>
                    </div>
                    <div class="wigdet-one-content">
                        <p class="m-0 text-uppercase font-weight-bold text-muted">
                            Avg Depth
                        </p>
                        <h2><span>{{ number_format($averageDepth ?? 0, 2) }} m</span></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card widget-box-one border border-warning">
                <div class="card-body">
                    <div class="float-right">
                        <i class="fas fa-map-marker-alt widget-icon"></i>
                    </div>
                    <div class="wigdet-one-content">
                        <p class="m-0 text-uppercase font-weight-bold text-muted">
                            Provinces Covered
                        </p>
                        <h2><span>{{ $provincesCount ?? 0 }}</span></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Boreholes by Province</h5>
                </div>
                <div class="card-body">
                    @if(isset($boreholesByProvince) && count($boreholesByProvince) > 0)
                        <div id="province-chart" style="height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-pie fa-3x mb-2"></i>
                            <p>No data available for provinces</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Yield Distribution</h5>
                </div>
                <div class="card-body">
                    @if(isset($yieldDistribution) && count($yieldDistribution) > 0)
                        <div id="yield-chart" style="height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-bar fa-3x mb-2"></i>
                            <p>No yield data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Boreholes</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentBoreholes) && $recentBoreholes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Serial No</th>
                                        <th>Province</th>
                                        <th>District</th>
                                        <th>Depth (m)</th>
                                        <th>Yield (l/s)</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBoreholes as $borehole)
                                        <tr>
                                            <td>{{ $borehole->borehole_serial_no }}</td>
                                            <td>{{ $borehole->province }}</td>
                                            <td>{{ $borehole->admin_district }}</td>
                                            <td>{{ number_format($borehole->final_borehole_depth_m, 2) }}</td>
                                            <td>{{ number_format($borehole->yield_lps, 2) }}</td>
                                            <td>{{ $borehole->date->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-table fa-3x mb-2"></i>
                            <p>No borehole data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if data exists
    @if(isset($boreholesByProvince) && count($boreholesByProvince) > 0)
        initializeProvinceChart();
    @endif
    
    @if(isset($yieldDistribution) && count($yieldDistribution) > 0)
        initializeYieldChart();
    @endif
});

function initializeProvinceChart() {
    // Initialize province chart using Chart.js or your preferred library
    console.log('Initializing province chart with data:', @json($boreholesByProvince));
}

function initializeYieldChart() {
    // Initialize yield distribution chart
    console.log('Initializing yield chart with data:', @json($yieldDistribution));
}
</script>
@endpush

<style>
.widget-box-one .widget-icon {
    font-size: 24px;
    opacity: 0.5;
}
</style>
@endsection