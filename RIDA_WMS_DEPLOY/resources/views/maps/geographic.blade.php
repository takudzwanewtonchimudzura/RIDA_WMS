@extends('layouts.app')

@section('title', 'Geographic Borehole Map')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-map-marked-alt me-2"></i>Geographic Borehole Map
                </h4>
                <p class="text-muted mb-0">
                    Interactive map showing {{ $totalBoreholes }} boreholes with geographic coordinates
                </p>
            </div>
        </div>
    </div>

    <!-- Filters and Stats -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="provinceFilter" class="form-label">Filter by Province</label>
                            <select id="provinceFilter" class="form-select">
                                <option value="">All Provinces</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province }}">{{ $province }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="yieldFilter" class="form-label">Filter by Yield</label>
                            <select id="yieldFilter" class="form-select">
                                <option value="">All Yields</option>
                                <option value="high">High Yield (≥10 l/s)</option>
                                <option value="medium">Medium Yield (5-10 l/s)</option>
                                <option value="low">Low Yield (<5 l/s)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button id="resetFilters" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-refresh me-1"></i> Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="mb-0" id="mapVisibleCount">{{ $totalBoreholes }}</h4>
                            <small class="text-muted">Visible</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0">{{ $boreholesWithCoords }}</h4>
                            <small class="text-muted">With Coords</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0">{{ $provinces->count() }}</h4>
                            <small class="text-muted">Provinces</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Borehole Locations</h5>
                </div>
                <div class="card-body p-0">
                    <div id="boreholeMap" class="map-canvas"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Map Legend</h6>
                    <div class="row">
                        <div class="col-auto">
                            <span class="legend-item">
                                <i class="fas fa-circle text-success me-1"></i> High Yield (≥10 l/s)
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="legend-item">
                                <i class="fas fa-circle text-warning me-1"></i> Medium Yield (5-10 l/s)
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="legend-item">
                                <i class="fas fa-circle text-danger me-1"></i> Low Yield (<5 l/s)
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="legend-item">
                                <i class="fas fa-circle text-secondary me-1"></i> Unknown Yield
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
#boreholeMap {
    width: 100%;
    height: min(68vh, 600px);
    min-height: 360px;
}
@media (max-width: 768px) {
    #boreholeMap { height: 55vh; min-height: 320px; }
}
.legend-item {
    display: inline-flex;
    align-items: center;
    margin-right: 15px;
}
.borehole-popup {
    min-width: 200px;
}
.borehole-popup h6 {
    margin-bottom: 8px;
    color: #2c7fb8;
}
</style>
@endpush

@push('scripts')
<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    const map = L.map('boreholeMap').setView([-19.0, 29.5], 6);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const boreholes = @json($mapData);
    const markers = L.layerGroup().addTo(map);
    const markerColors = {
        high_yield: '#21834a',
        medium_yield: '#c17a12',
        low_yield: '#bd3d42',
        unknown: '#65777b'
    };

    function renderMarkers() {
        const province = document.getElementById('provinceFilter').value;
        const yieldFilter = document.getElementById('yieldFilter').value;
        const visible = boreholes.filter((borehole) => {
            const provinceMatches = !province || borehole.province === province;
            const yieldMatches = !yieldFilter || borehole.status === `${yieldFilter}_yield`;
            return provinceMatches && yieldMatches;
        });

        markers.clearLayers();
        visible.forEach((borehole) => {
            const color = markerColors[borehole.status] || markerColors.unknown;
            L.circleMarker([borehole.latitude, borehole.longitude], {
                radius: 8,
                color,
                fillColor: color,
                fillOpacity: 0.82,
                weight: 2
            }).bindPopup(borehole.popup_content).addTo(markers);
        });

        document.getElementById('mapVisibleCount').textContent = visible.length;
        if (visible.length > 0) {
            map.fitBounds(L.latLngBounds(visible.map((borehole) => [borehole.latitude, borehole.longitude])), { padding: [24, 24], maxZoom: 12 });
        } else {
            map.setView([-19.0, 29.5], 6);
        }
    }

    // Filter functionality
    document.getElementById('provinceFilter').addEventListener('change', function() {
        renderMarkers();
    });

    document.getElementById('yieldFilter').addEventListener('change', function() {
        renderMarkers();
    });

    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('provinceFilter').value = '';
        document.getElementById('yieldFilter').value = '';
        renderMarkers();
    });

    renderMarkers();
});
</script>
@endpush