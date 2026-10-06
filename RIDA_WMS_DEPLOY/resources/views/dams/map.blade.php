@extends('layouts.app')

@section('title', 'Dam Map View')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">Dams</a></li>
    <li class="breadcrumb-item active">Map View</li>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 600px;
        width: 100%;
        border-radius: 10px;
        z-index: 1;
    }
    .map-container {
        position: relative;
    }
    .map-overlay {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1000;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 300px;
    }
    .legend {
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    .legend-color {
        width: 20px;
        height: 20px;
        margin-right: 10px;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-map-marked-alt me-2"></i>Dam Location Map</h2>
        <p class="text-muted">View dam locations and details on an interactive map</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-water me-2"></i>Dams Geographic Distribution</h4>
                    <div class="btn-group">
                        <button class="btn btn-light btn-sm" id="resetView">
                            <i class="fas fa-sync-alt me-1"></i>Reset View
                        </button>
                        <button class="btn btn-light btn-sm" id="fitBounds">
                            <i class="fas fa-expand-alt me-1"></i>Fit to Dams
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="map-container">
                    <!-- Map Controls Overlay -->
                    <div class="map-overlay">
                        <h6><i class="fas fa-filter me-2"></i>Filter Dams</h6>
                        <div class="mb-2">
                            <label class="form-label small">By Capacity</label>
                            <select class="form-select form-select-sm" id="capacityFilter">
                                <option value="all">All Capacities</option>
                                <option value="small">Small (< 10,000 m³)</option>
                                <option value="medium">Medium (10,000 - 100,000 m³)</option>
                                <option value="large">Large (> 100,000 m³)</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">By Condition</label>
                            <select class="form-select form-select-sm" id="conditionFilter">
                                <option value="all">All Conditions</option>
                                <option value="good">Good Condition</option>
                                <option value="fair">Fair Condition</option>
                                <option value="poor">Poor Condition</option>
                            </select>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showLabels" checked>
                            <label class="form-check-label small" for="showLabels">
                                Show Dam Names
                            </label>
                        </div>
                    </div>
                    
                    <!-- Map Container -->
                    <div id="map"></div>
                </div>
                
                <!-- Legend -->
                <div class="legend mt-3">
                    <h6 class="mb-3"><i class="fas fa-key me-2"></i>Map Legend</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #28a745;"></div>
                                <span class="small">Good Condition</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #ffc107;"></div>
                                <span class="small">Fair Condition</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #dc3545;"></div>
                                <span class="small">Poor Condition</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #007bff; border-radius: 50%;"></div>
                                <span class="small">Dam Location</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #17a2b8; border-radius: 50%;"></div>
                                <span class="small">Selected Dam</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchLocation" placeholder="Search for location...">
                            <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Showing <span id="damCount">0</span> dams on map
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dam Details Modal -->
<div class="modal fade" id="damDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="damModalTitle"><i class="fas fa-water me-2"></i>Dam Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="damModalBody">
                <!-- Content will be loaded via JavaScript -->
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="viewDetailsBtn">
                    <i class="fas fa-external-link-alt me-2"></i>View Full Details
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Dam data - passed from controller
    // Fix: Use proper PHP syntax for JSON encoding
    var dams = JSON.parse('<?php echo json_encode($dams ?? []); ?>');
    
    // Initialize map
    var map = L.map('map').setView([-17.823, 31.052], 8);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Initialize markers layer group
    var markers = L.layerGroup().addTo(map);
    
    // Function to get marker color based on condition
    function getConditionColor(condition) {
        if (condition) {
            var lowerCondition = condition.toLowerCase();
            if (lowerCondition.includes('good')) return '#28a745';
            if (lowerCondition.includes('fair')) return '#ffc107';
            if (lowerCondition.includes('poor')) return '#dc3545';
        }
        return '#007bff';
    }
    
    // Function to get capacity category
    function getCapacityCategory(capacity) {
        var numCapacity = parseFloat(capacity) || 0;
        if (numCapacity < 10000) return 'small';
        if (numCapacity <= 100000) return 'medium';
        return 'large';
    }
    
    // Function to parse coordinates
    function parseCoordinates(coordString) {
        if (!coordString) return null;
        var coords = coordString.split(',').map(function(coord) {
            return parseFloat(coord.trim());
        });
        if (coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])) {
            return [coords[0], coords[1]];
        }
        return null;
    }
    
    // Function to check if dam matches filter
    function damMatchesFilter(dam, capacityFilter, conditionFilter) {
        // Capacity filter
        var capacity = parseFloat(dam.capacity) || 0;
        var capacityMatch = true;
        if (capacityFilter !== 'all') {
            var category = getCapacityCategory(capacity);
            capacityMatch = category === capacityFilter;
        }
        
        // Condition filter
        var conditionMatch = true;
        if (conditionFilter !== 'all') {
            var condition = (dam.condition_of_the_wall || '').toLowerCase();
            conditionMatch = condition.includes(conditionFilter);
        }
        
        return capacityMatch && conditionMatch;
    }
    
    // Function to add dam markers to map
    function addDamMarkers(filteredDams) {
        markers.clearLayers();
        
        filteredDams.forEach(function(dam, index) {
            var coords = parseCoordinates(dam.coordinates_at_damwall);
            if (coords === null) return;
            
            var condition = dam.condition_of_the_wall || 'Unknown';
            var capacity = parseFloat(dam.capacity) || 0;
            var color = getConditionColor(condition);
            
            // Create custom marker
            var marker = L.circleMarker(coords, {
                radius: 15,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            });
            
            // Create popup content
            var popupContent = '<div class="dam-popup">' +
                '<h6><i class="fas fa-water"></i> ' + dam.name_of_dam + '</h6>' +
                '<hr class="my-2">' +
                '<p class="mb-1"><strong>Location:</strong> ' + dam.village + ', ' + dam.district + '</p>' +
                '<p class="mb-1"><strong>Capacity:</strong> ' + capacity.toLocaleString() + ' m³</p>' +
                '<p class="mb-1"><strong>Condition:</strong> <span class="badge" style="background-color: ' + color + '">' + condition + '</span></p>' +
                '<p class="mb-2"><strong>Coordinates:</strong> ' + (dam.coordinates_at_damwall || 'N/A') + '</p>' +
                '<div class="d-grid">' +
                '<button class="btn btn-sm btn-primary view-details" data-dam-id="' + dam.id + '">' +
                '<i class="fas fa-info-circle me-1"></i> View Details' +
                '</button>' +
                '</div>' +
                '</div>';
            
            // Bind popup to marker
            marker.bindPopup(popupContent);
            
            // Add click event for modal
            marker.on('click', function() {
                // Update modal content
                var modalTitleElement = document.getElementById('damModalTitle');
                var modalBodyElement = document.getElementById('damModalBody');
                var viewDetailsBtnElement = document.getElementById('viewDetailsBtn');
                
                if (modalTitleElement) {
                    modalTitleElement.innerHTML = '<i class="fas fa-water me-2"></i>' + dam.name_of_dam;
                }
                
                if (modalBodyElement) {
                    modalBodyElement.innerHTML = 
                        '<div class="row">' +
                        '<div class="col-md-6">' +
                        '<table class="table table-sm">' +
                        '<tr><th>Village:</th><td>' + dam.village + '</td></tr>' +
                        '<tr><th>District:</th><td>' + dam.district + '</td></tr>' +
                        '<tr><th>Province:</th><td>' + dam.province + '</td></tr>' +
                        '<tr><th>Ward:</th><td>' + dam.ward + '</td></tr>' +
                        '</table>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                        '<table class="table table-sm">' +
                        '<tr><th>Capacity:</th><td>' + capacity.toLocaleString() + ' m³</td></tr>' +
                        '<tr><th>Wall Length:</th><td>' + (dam.length_of_wall || 0) + ' m</td></tr>' +
                        '<tr><th>Wall Height:</th><td>' + (dam.height_of_wall || 0) + ' m</td></tr>' +
                        '<tr><th>Water Depth:</th><td>' + (dam.water_depth || 0) + ' m</td></tr>' +
                        '</table>' +
                        '</div>' +
                        '</div>' +
                        '<div class="mt-3">' +
                        '<h6>Condition Assessment</h6>' +
                        '<p>' + (dam.condition_of_the_wall || 'No assessment available') + '</p>' +
                        '</div>';
                }
                
                if (viewDetailsBtnElement) {
                    viewDetailsBtnElement.href = '/dams/' + dam.id;
                }
                
                // Show modal
                var modalElement = document.getElementById('damDetailsModal');
                if (modalElement) {
                    var modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
                
                // Highlight this marker
                marker.setStyle({color: '#17a2b8', weight: 3});
            });
            
            // Add label if enabled
            var showLabelsCheckbox = document.getElementById('showLabels');
            if (showLabelsCheckbox && showLabelsCheckbox.checked) {
                var label = L.marker(coords, {
                    icon: L.divIcon({
                        className: 'dam-label',
                        html: '<div style="background: white; padding: 2px 5px; border-radius: 3px; border: 1px solid #ccc; font-size: 12px; white-space: nowrap;">' + dam.name_of_dam + '</div>',
                        iconSize: [100, 20]
                    })
                });
                markers.addLayer(label);
            }
            
            markers.addLayer(marker);
        });
        
        // Update dam count
        var damCountElement = document.getElementById('damCount');
        if (damCountElement) {
            damCountElement.textContent = filteredDams.length;
        }
        
        // Fit map to bounds if we have markers
        if (filteredDams.length > 0) {
            var bounds = markers.getBounds();
            if (bounds && typeof bounds.isValid === 'function' && bounds.isValid()) {
                map.fitBounds(bounds, {padding: [50, 50]});
            }
        }
    }
    
    // Function to filter dams
    function filterDams() {
        var capacityFilterElement = document.getElementById('capacityFilter');
        var conditionFilterElement = document.getElementById('conditionFilter');
        
        var capacityFilter = capacityFilterElement ? capacityFilterElement.value : 'all';
        var conditionFilter = conditionFilterElement ? conditionFilterElement.value : 'all';
        
        var filteredDams = dams.filter(function(dam) {
            return damMatchesFilter(dam, capacityFilter, conditionFilter);
        });
        
        addDamMarkers(filteredDams);
    }
    
    // Initialize map when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize with all dams
        addDamMarkers(dams);
        
        // Set up event listeners
        var capacityFilterElement = document.getElementById('capacityFilter');
        var conditionFilterElement = document.getElementById('conditionFilter');
        var showLabelsElement = document.getElementById('showLabels');
        var resetViewElement = document.getElementById('resetView');
        var fitBoundsElement = document.getElementById('fitBounds');
        var searchButtonElement = document.getElementById('searchButton');
        var searchLocationElement = document.getElementById('searchLocation');
        
        if (capacityFilterElement) {
            capacityFilterElement.addEventListener('change', filterDams);
        }
        
        if (conditionFilterElement) {
            conditionFilterElement.addEventListener('change', filterDams);
        }
        
        if (showLabelsElement) {
            showLabelsElement.addEventListener('change', filterDams);
        }
        
        if (resetViewElement) {
            resetViewElement.addEventListener('click', function() {
                map.setView([-17.823, 31.052], 8);
            });
        }
        
        if (fitBoundsElement) {
            fitBoundsElement.addEventListener('click', function() {
                var bounds = markers.getBounds();
                if (bounds && typeof bounds.isValid === 'function' && bounds.isValid()) {
                    map.fitBounds(bounds, {padding: [50, 50]});
                }
            });
        }
        
        if (searchButtonElement && searchLocationElement) {
            searchButtonElement.addEventListener('click', function() {
                var query = searchLocationElement.value;
                if (query) {
                    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query))
                        .then(function(response) {
                            return response.json();
                        })
                        .then(function(data) {
                            if (data && data.length > 0) {
                                var result = data[0];
                                map.setView([result.lat, result.lon], 12);
                            } else {
                                alert('Location not found');
                            }
                        })
                        .catch(function(error) {
                            console.error('Geocoding error:', error);
                        });
                }
            });
            
            searchLocationElement.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchButtonElement.click();
                }
            });
        }
        
        // Event delegation for popup buttons
        document.addEventListener('click', function(e) {
            var target = e.target;
            var viewDetailsButton = target.closest('.view-details');
            
            if (viewDetailsButton) {
                var damId = viewDetailsButton.getAttribute('data-dam-id');
                if (damId) {
                    var dam = dams.find(function(d) {
                        return d.id == damId;
                    });
                    
                    if (dam) {
                        var capacity = parseFloat(dam.capacity) || 0;
                        
                        var modalTitleElement = document.getElementById('damModalTitle');
                        var modalBodyElement = document.getElementById('damModalBody');
                        var viewDetailsBtnElement = document.getElementById('viewDetailsBtn');
                        
                        if (modalTitleElement) {
                            modalTitleElement.innerHTML = '<i class="fas fa-water me-2"></i>' + dam.name_of_dam;
                        }
                        
                        if (modalBodyElement) {
                            modalBodyElement.innerHTML = 
                                '<div class="row">' +
                                '<div class="col-md-6">' +
                                '<table class="table table-sm">' +
                                '<tr><th>Village:</th><td>' + dam.village + '</td></tr>' +
                                '<tr><th>District:</th><td>' + dam.district + '</td></tr>' +
                                '<tr><th>Province:</th><td>' + dam.province + '</td></tr>' +
                                '<tr><th>Ward:</th><td>' + dam.ward + '</td></tr>' +
                                '</table>' +
                                '</div>' +
                                '<div class="col-md-6">' +
                                '<table class="table table-sm">' +
                                '<tr><th>Capacity:</th><td>' + capacity.toLocaleString() + ' m³</td></tr>' +
                                '<tr><th>Wall Length:</th><td>' + (dam.length_of_wall || 0) + ' m</td></tr>' +
                                '<tr><th>Wall Height:</th><td>' + (dam.height_of_wall || 0) + ' m</td></tr>' +
                                '<tr><th>Water Depth:</th><td>' + (dam.water_depth || 0) + ' m</td></tr>' +
                                '</table>' +
                                '</div>' +
                                '</div>' +
                                '<div class="mt-3">' +
                                '<h6>Condition Assessment</h6>' +
                                '<p>' + (dam.condition_of_the_wall || 'No assessment available') + '</p>' +
                                '</div>';
                        }
                        
                        if (viewDetailsBtnElement) {
                            viewDetailsBtnElement.href = '/dams/' + dam.id;
                        }
                        
                        var modalElement = document.getElementById('damDetailsModal');
                        if (modalElement) {
                            var modal = new bootstrap.Modal(modalElement);
                            modal.show();
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection