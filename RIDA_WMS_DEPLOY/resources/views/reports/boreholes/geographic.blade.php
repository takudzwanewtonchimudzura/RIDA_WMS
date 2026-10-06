@extends('layouts.report')

@section('title', 'Geographic Distribution Report - RIDA RWIMS')
@section('report-title', 'Geographic Distribution & Spatial Analysis')
@section('report-period', $period)

@section('content')
@include('reports.partials.filters')

<div class="row mb-4">
    <div class="col-md-12">
        <div class="chart-container">
            <h5 class="chart-title">Borehole Distribution Map</h5>
            <div id="mapContainer" style="height: 500px; border-radius: 8px;"></div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Regional Distribution</h5>
            <canvas id="regionalChart" height="300"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Yield Capacity by Region</h5>
            <canvas id="yieldCapacityChart" height="300"></canvas>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="chart-title">Spatial Analysis Summary</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>Total Boreholes</th>
                            <th>Average Yield (L/s)</th>
                            <th>Success Rate</th>
                            <th>Avg. Depth (m)</th>
                            <th>Water Quality Score</th>
                            <th>Investment (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regionalAnalysis as $region)
                        <tr>
                            <td><strong>{{ $region['name'] }}</strong></td>
                            <td>{{ $region['total_boreholes'] }}</td>
                            <td>{{ number_format($region['avg_yield'], 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $region['success_rate'] > 80 ? 'success' : ($region['success_rate'] > 60 ? 'warning' : 'danger') }}">
                                    {{ number_format($region['success_rate'], 1) }}%
                                </span>
                            </td>
                            <td>{{ number_format($region['avg_depth'], 1) }}</td>
                            <td>
                                <span class="badge bg-{{ $region['quality_score'] > 8 ? 'success' : ($region['quality_score'] > 6 ? 'warning' : 'danger') }}">
                                    {{ number_format($region['quality_score'], 1) }}/10
                                </span>
                            </td>
                            <td>${{ number_format($region['investment'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- GIS Integration Note -->
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle me-2"></i>GIS Integration</h6>
            <p class="mb-0">This report can be integrated with GIS services like Leaflet, Google Maps, or OpenStreetMap for interactive spatial analysis and real-time mapping capabilities.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('mapContainer').setView([-19.0154, 29.1549], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        const markers = @json($boreholeCoordinates ?? []);
        const bounds = [];
        markers.forEach(function(point) {
            const marker = L.marker([point.lat, point.lng]).addTo(map);
            marker.bindPopup(point.popup_content);
            bounds.push([point.lat, point.lng]);
        });
        if (bounds.length) map.fitBounds(bounds, { padding: [20, 20] });

        // Regional Distribution Chart
        initBarChart('regionalChart', {
            labels: @json($regionalAnalysis->pluck('name') ?? []),
            datasets: [{
                label: 'Number of Boreholes',
                data: @json($regionalAnalysis->pluck('total_boreholes') ?? []),
                backgroundColor: chartColors.primary
            }]
        });

        // Yield Capacity Chart
        initBarChart('yieldCapacityChart', {
            labels: @json($regionalAnalysis->pluck('name') ?? []),
            datasets: [{
                label: 'Average Yield (L/s)',
                data: @json($regionalAnalysis->pluck('avg_yield') ?? []),
                backgroundColor: chartColors.success
            }]
        });
    });
</script>
@endpush