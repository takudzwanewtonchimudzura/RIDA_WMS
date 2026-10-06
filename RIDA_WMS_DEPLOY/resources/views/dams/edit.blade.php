@extends('layouts.app')

@section('title', 'Update Dam')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="h3 mb-0">Update Dam: {{ $dam->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">Dams</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dams.show', $dam->id) }}">{{ $dam->name }}</a></li>
                    <li class="breadcrumb-item active">Update</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header text-center bg-warning">
                    <h2 class="mb-0 text-white"><i class="fas fa-edit me-2"></i>Update Dam Record</h2>
                    <p class="mb-0 mt-2 text-white">Update the information for {{ $dam->name }}</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dams.update', $dam->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label required">Dam Name</label>
                                            <input type="text" name="name" id="name" required
                                                value="{{ old('name', $dam->name) }}" 
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Enter dam name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="village" class="form-label required">Village</label>
                                                <input type="text" name="village" id="village" required
                                                    value="{{ old('village', $dam->village) }}" 
                                                    class="form-control @error('village') is-invalid @enderror"
                                                    placeholder="Village name">
                                                @error('village')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="ward" class="form-label required">Ward</label>
                                                <input type="text" name="ward" id="ward" required
                                                    value="{{ old('ward', $dam->ward) }}" 
                                                    class="form-control @error('ward') is-invalid @enderror"
                                                    placeholder="Ward">
                                                @error('ward')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="district" class="form-label required">District</label>
                                                <input type="text" name="district" id="district" required
                                                    value="{{ old('district', $dam->district) }}" 
                                                    class="form-control @error('district') is-invalid @enderror"
                                                    placeholder="District">
                                                @error('district')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="province" class="form-label required">Province</label>
                                                <select name="province" id="province" required 
                                                    class="form-control @error('province') is-invalid @enderror">
                                                    <option value="">Select Province</option>
                                                    @foreach($provinces as $province)
                                                        <option value="{{ $province }}" 
                                                            {{ old('province', $dam->province) == $province ? 'selected' : '' }}>
                                                            {{ $province }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('province')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Coordinates -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Location Coordinates</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="latitude" class="form-label">Latitude</label>
                                                <input type="number" step="0.000001" name="latitude" id="latitude"
                                                    value="{{ old('latitude', $dam->latitude) }}" 
                                                    class="form-control @error('latitude') is-invalid @enderror"
                                                    placeholder="e.g., -15.416667" min="-90" max="90">
                                                <small class="text-muted">Southern hemisphere: negative values (-)</small>
                                                @error('latitude')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="longitude" class="form-label">Longitude</label>
                                                <input type="number" step="0.000001" name="longitude" id="longitude"
                                                    value="{{ old('longitude', $dam->longitude) }}" 
                                                    class="form-control @error('longitude') is-invalid @enderror"
                                                    placeholder="e.g., 28.283333" min="-180" max="180">
                                                <small class="text-muted">Eastern hemisphere: positive values (+)</small>
                                                @error('longitude')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Current Coordinates:</strong> 
                                                @if($dam->latitude && $dam->longitude)
                                                    {{ $dam->latitude }}, {{ $dam->longitude }}
                                                    <br>
                                                    <a href="https://www.google.com/maps?q={{ $dam->latitude }},{{ $dam->longitude }}" 
                                                       target="_blank" class="btn btn-sm btn-outline-info mt-2">
                                                        <i class="fas fa-external-link-alt me-1"></i> View on Google Maps
                                                    </a>
                                                @else
                                                    No coordinates set
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dam Wall Details -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0"><i class="fas fa-layer-group"></i> Dam Wall Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="wall_length" class="form-label">Wall Length (m)</label>
                                                <input type="number" step="0.01" name="wall_length" id="wall_length"
                                                    value="{{ old('wall_length', $dam->wall_length) }}" 
                                                    class="form-control @error('wall_length') is-invalid @enderror"
                                                    placeholder="Length in meters">
                                                @error('wall_length')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="wall_height" class="form-label">Wall Height (m)</label>
                                                <input type="number" step="0.01" name="wall_height" id="wall_height"
                                                    value="{{ old('wall_height', $dam->wall_height) }}" 
                                                    class="form-control @error('wall_height') is-invalid @enderror"
                                                    placeholder="Height in meters">
                                                @error('wall_height')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="water_depth" class="form-label">Water Depth (m)</label>
                                                <input type="number" step="0.01" name="water_depth" id="water_depth"
                                                    value="{{ old('water_depth', $dam->water_depth) }}" 
                                                    class="form-control @error('water_depth') is-invalid @enderror"
                                                    placeholder="Current water depth">
                                                @error('water_depth')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="wall_condition" class="form-label required">Wall Condition</label>
                                                <select name="wall_condition" id="wall_condition" required
                                                    class="form-control @error('wall_condition') is-invalid @enderror">
                                                    <option value="">Select Condition</option>
                                                    <option value="good" {{ old('wall_condition', $dam->wall_condition) == 'good' ? 'selected' : '' }}>Good</option>
                                                    <option value="fair" {{ old('wall_condition', $dam->wall_condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                                                    <option value="poor" {{ old('wall_condition', $dam->wall_condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                                                    <option value="critical" {{ old('wall_condition', $dam->wall_condition) == 'critical' ? 'selected' : '' }}>Critical</option>
                                                </select>
                                                @error('wall_condition')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="wall_condition_comment" class="form-label">Wall Condition Comments</label>
                                            <textarea name="wall_condition_comment" id="wall_condition_comment" 
                                                class="form-control @error('wall_condition_comment') is-invalid @enderror"
                                                rows="3" placeholder="Detailed comments on wall condition">{{ old('wall_condition_comment', $dam->wall_condition_comment) }}</textarea>
                                            @error('wall_condition_comment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Spillway Details -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-white">
                                        <h5 class="mb-0"><i class="fas fa-water"></i> Spillway Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="spillway_type" class="form-label required">Spillway Type</label>
                                                <select name="spillway_type" id="spillway_type" required
                                                    class="form-control @error('spillway_type') is-invalid @enderror">
                                                    <option value="">Select Type</option>
                                                    <option value="natural on rocks" {{ old('spillway_type', $dam->spillway_type) == 'natural on rocks' ? 'selected' : '' }}>Natural on Rocks</option>
                                                    <option value="artificial" {{ old('spillway_type', $dam->spillway_type) == 'artificial' ? 'selected' : '' }}>Artificial</option>
                                                    <option value="other" {{ old('spillway_type', $dam->spillway_type) == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                @error('spillway_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="spillway_condition" class="form-label required">Spillway Condition</label>
                                                <select name="spillway_condition" id="spillway_condition" required
                                                    class="form-control @error('spillway_condition') is-invalid @enderror">
                                                    <option value="">Select Condition</option>
                                                    <option value="good" {{ old('spillway_condition', $dam->spillway_condition) == 'good' ? 'selected' : '' }}>Good</option>
                                                    <option value="fair" {{ old('spillway_condition', $dam->spillway_condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                                                    <option value="poor" {{ old('spillway_condition', $dam->spillway_condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                                                    <option value="critical" {{ old('spillway_condition', $dam->spillway_condition) == 'critical' ? 'selected' : '' }}>Critical</option>
                                                </select>
                                                @error('spillway_condition')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="spillway_width" class="form-label">Spillway Width (m)</label>
                                                <input type="number" step="0.01" name="spillway_width" id="spillway_width"
                                                    value="{{ old('spillway_width', $dam->spillway_width) }}" 
                                                    class="form-control @error('spillway_width') is-invalid @enderror"
                                                    placeholder="Width in meters">
                                                @error('spillway_width')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="throwback" class="form-label">Throwback Distance (m)</label>
                                                <input type="number" step="0.01" name="throwback" id="throwback"
                                                    value="{{ old('throwback', $dam->throwback) }}" 
                                                    class="form-control @error('throwback') is-invalid @enderror"
                                                    placeholder="Distance in meters">
                                                @error('throwback')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="spillway_condition_comment" class="form-label">Spillway Condition Comments</label>
                                            <textarea name="spillway_condition_comment" id="spillway_condition_comment" 
                                                class="form-control @error('spillway_condition_comment') is-invalid @enderror"
                                                rows="3" placeholder="Detailed comments on spillway condition">{{ old('spillway_condition_comment', $dam->spillway_condition_comment) }}</textarea>
                                            @error('spillway_condition_comment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Capacity & Structural Features -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0"><i class="fas fa-calculator"></i> Capacity & Volume</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="capacity" class="form-label">Total Capacity (m³)</label>
                                                <input type="number" step="0.01" name="capacity" id="capacity"
                                                    value="{{ old('capacity', $dam->capacity) }}" 
                                                    class="form-control @error('capacity') is-invalid @enderror"
                                                    placeholder="Total capacity in cubic meters">
                                                @error('capacity')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="wall_volume" class="form-label">Wall Volume (m³)</label>
                                                <input type="number" step="0.01" name="wall_volume" id="wall_volume"
                                                    value="{{ old('wall_volume', $dam->wall_volume) }}" 
                                                    class="form-control @error('wall_volume') is-invalid @enderror"
                                                    placeholder="Wall volume in cubic meters">
                                                @error('wall_volume')
                                                    <div="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Capacity Calculation Display -->
                                        @if($dam->capacity && $dam->water_depth)
                                            <div class="alert alert-info">
                                                <i class="fas fa-calculator me-2"></i>
                                                <strong>Current Status:</strong><br>
                                                • Water Level: {{ ($dam->water_depth / ($dam->capacity * 0.001)) * 100 > 100 ? 100 : number_format(($dam->water_depth / ($dam->capacity * 0.001)) * 100, 1) }}%<br>
                                                • Current Volume: {{ number_format($dam->capacity * (min(100, ($dam->water_depth / ($dam->capacity * 0.001)) * 100) / 100), 0) }} m³
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Structural Features -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0"><i class="fas fa-tools"></i> Structural Features</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                           id="has_stone_pitching" name="has_stone_pitching" value="1"
                                                           {{ old('has_stone_pitching', $dam->has_stone_pitching) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="has_stone_pitching">
                                                        <i class="fas fa-layer-group me-2"></i> Has Stone Pitching (Upstream)
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                           id="has_star_grass" name="has_star_grass" value="1"
                                                           {{ old('has_star_grass', $dam->has_star_grass) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="has_star_grass">
                                                        <i class="fas fa-seedling me-2"></i> Has Star Grass (Downstream)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-light">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Current Features:</strong><br>
                                            • Stone Pitching: {{ $dam->has_stone_pitching ? 'Yes' : 'No' }}<br>
                                            • Star Grass: {{ $dam->has_star_grass ? 'Yes' : 'No' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- General Comments -->
                            <div class="col-12 mb-4">
                                <div class="card border-dark">
                                    <div class="card-header bg-dark text-white">
                                        <h5 class="mb-0"><i class="fas fa-comments"></i> General Comments</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="general_comments" class="form-label">General Comments</label>
                                            <textarea name="general_comments" id="general_comments" 
                                                class="form-control @error('general_comments') is-invalid @enderror"
                                                rows="6" placeholder="General comments about the dam">{{ old('general_comments', $dam->general_comments) }}</textarea>
                                            @error('general_comments')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('dams.show', $dam->id) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-save me-2"></i> Update Dam Record
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card-header {
    border-bottom: none;
    padding: 1rem 1.5rem;
}

.card-header h5 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

.form-control:focus, .form-select:focus {
    border-color: #2c7fb8;
    box-shadow: 0 0 0 0.25rem rgba(44, 127, 184, 0.25);
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-weight: 600;
}

.alert {
    border-radius: 8px;
    border: none;
}

.alert-info {
    background-color: #e7f3ff;
    color: #0c5460;
}

.alert-light {
    background-color: #f8f9fa;
    color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple validation
    const form = document.querySelector('form');
    const requiredFields = form.querySelectorAll('[required]');
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
                
                // Add error message if not exists
                if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'This field is required';
                    field.parentNode.appendChild(errorDiv);
                }
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Please fill in all required fields.', 'error');
        }
    });
    
    // Remove invalid class when user starts typing
    requiredFields.forEach(field => {
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
                const errorDiv = this.parentNode.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        });
    });
    
    // Notification function
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existing = document.querySelector('.custom-notification');
        if (existing) existing.remove();
        
        const notification = document.createElement('div');
        notification.className = `custom-notification alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
        `;
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${type === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle'} me-2"></i>
                <span>${message}</span>
            </div>
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    console.log('Update form loaded');
});
</script>
@endsection