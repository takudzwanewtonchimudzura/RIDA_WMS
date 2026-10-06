@extends('layouts.app')

@section('title', 'Create New Dam')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="h3 mb-0">Create New Dam</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">Dams</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h2 class="mb-0"><i class="fas fa-water me-2"></i>Create New Dam Record</h2>
                    <p class="mb-0 mt-2">Complete all sections to create a comprehensive dam record</p>
                </div>
                <div class="card-body">
                    <!-- Progress Bar -->
                    <div class="progress mb-4">
                        <div class="progress-bar" id="form-progress" role="progressbar" style="width: 16.6%;" aria-valuenow="16.6" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <!-- Navigation Pills -->
                    <div class="form-navigation">
                        <div class="nav-pills-custom">
                            <div class="nav-pills-container">
                                <button class="nav-pill active" data-section="0">
                                    <i class="fas fa-info-circle"></i> 
                                    <span>Basic Info</span>
                                </button>
                                <button class="nav-pill" data-section="1">
                                    <i class="fas fa-layer-group"></i> 
                                    <span>Dam Wall</span>
                                </button>
                                <button class="nav-pill" data-section="2">
                                    <i class="fas fa-water"></i> 
                                    <span>Spillway</span>
                                </button>
                                <button class="nav-pill" data-section="3">
                                    <i class="fas fa-calculator"></i> 
                                    <span>Capacity</span>
                                </button>
                                <button class="nav-pill" data-section="4">
                                    <i class="fas fa-tools"></i> 
                                    <span>Structure</span>
                                </button>
                                <button class="nav-pill" data-section="5">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <span>Location</span>
                                </button>
                                <button class="nav-pill" data-section="6">
                                    <i class="fas fa-clipboard-check"></i> 
                                    <span>Review</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Sections -->
                    <form method="POST" action="{{ route('dams.store') }}" id="damForm">
                        @csrf
                        @method('POST')
                        
                        <!-- Section 1: Basic Information -->
                        <div class="form-section active" id="section-0">
                            <h4 class="section-title"><i class="fas fa-info-circle text-primary"></i> Basic Information</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label required">Dam Name</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-tag"></i>
                                        <input type="text" name="name" id="name" required
                                            value="{{ old('name') }}" class="form-control" placeholder="Enter dam name">
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="village" class="form-label required">Village</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-home"></i>
                                        <input type="text" name="village" id="village" required
                                            value="{{ old('village') }}" class="form-control" placeholder="Enter village name">
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="ward" class="form-label required">Ward</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-map-pin"></i>
                                        <input type="text" name="ward" id="ward" required
                                            value="{{ old('ward') }}" class="form-control" placeholder="Enter ward">
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="district" class="form-label required">District</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-map-marked-alt"></i>
                                        <input type="text" name="district" id="district" required
                                            value="{{ old('district') }}" class="form-control" placeholder="Enter district">
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="province" class="form-label required">Province</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-map"></i>
                                        <select name="province" id="province" required class="form-control">
                                            <option value="">Select Province</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province }}" {{ old('province') == $province ? 'selected' : '' }}>
                                                    {{ $province }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- General Comments (from migration) -->
                                <div class="col-12 mb-3">
                                    <label for="general_comments" class="form-label">General Comments</label>
                                    <textarea name="general_comments" id="general_comments" class="form-control" rows="4" 
                                        placeholder="General comments about the dam">{{ old('general_comments') }}</textarea>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <div></div>
                                <button type="button" class="btn btn-primary next-btn" data-next="1">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 2: Dam Wall Details -->
                        <div class="form-section" id="section-1">
                            <h4 class="section-title"><i class="fas fa-layer-group text-primary"></i> Dam Wall Details</h4>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="wall_length" class="form-label">Wall Length (m)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-arrows-alt-h"></i>
                                        <input type="number" step="0.01" name="wall_length" id="wall_length"
                                            value="{{ old('wall_length') }}" class="form-control" placeholder="Length in meters">
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="wall_height" class="form-label">Wall Height (m)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-arrows-alt-v"></i>
                                        <input type="number" step="0.01" name="wall_height" id="wall_height"
                                            value="{{ old('wall_height') }}" class="form-control" placeholder="Height in meters">
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="water_depth" class="form-label">Water Depth (m)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-ruler-vertical"></i>
                                        <input type="number" step="0.01" name="water_depth" id="water_depth"
                                            value="{{ old('water_depth') }}" class="form-control" placeholder="Current water depth">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="wall_condition" class="form-label required">Wall Condition</label>
                                    <select class="form-control" id="wall_condition" name="wall_condition" required>
                                        <option value="">Select Condition</option>
                                        <option value="good" {{ old('wall_condition') == 'good' ? 'selected' : '' }}>Good</option>
                                        <option value="fair" {{ old('wall_condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="poor" {{ old('wall_condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                        <option value="critical" {{ old('wall_condition') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="wall_condition_comment" class="form-label">Wall Condition Comments</label>
                                    <textarea class="form-control" id="wall_condition_comment" name="wall_condition_comment" 
                                        rows="3" placeholder="Detailed comments on wall condition">{{ old('wall_condition_comment') }}</textarea>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="0">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary next-btn" data-next="2">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 3: Spillway Details -->
                        <div class="form-section" id="section-2">
                            <h4 class="section-title"><i class="fas fa-water text-primary"></i> Spillway Details</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="spillway_type" class="form-label required">Spillway Type</label>
                                    <select class="form-control" id="spillway_type" name="spillway_type" required>
                                        <option value="">Select Type</option>
                                        <option value="natural on rocks" {{ old('spillway_type') == 'natural on rocks' ? 'selected' : '' }}>Natural on Rocks</option>
                                        <option value="artificial" {{ old('spillway_type') == 'artificial' ? 'selected' : '' }}>Artificial</option>
                                        <option value="other" {{ old('spillway_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="spillway_condition" class="form-label required">Spillway Condition</label>
                                    <select class="form-control" id="spillway_condition" name="spillway_condition" required>
                                        <option value="">Select Condition</option>
                                        <option value="good" {{ old('spillway_condition') == 'good' ? 'selected' : '' }}>Good</option>
                                        <option value="fair" {{ old('spillway_condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="poor" {{ old('spillway_condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                        <option value="critical" {{ old('spillway_condition') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="spillway_width" class="form-label">Spillway Width (m)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-arrows-alt-h"></i>
                                        <input type="number" step="0.01" name="spillway_width" id="spillway_width"
                                            value="{{ old('spillway_width') }}" class="form-control" placeholder="Width in meters">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="throwback" class="form-label">Throwback Distance (m)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-ruler"></i>
                                        <input type="number" step="0.01" name="throwback" id="throwback"
                                            value="{{ old('throwback') }}" class="form-control" placeholder="Distance in meters">
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="spillway_condition_comment" class="form-label">Spillway Condition Comments</label>
                                    <textarea class="form-control" id="spillway_condition_comment" name="spillway_condition_comment" 
                                        rows="3" placeholder="Detailed comments on spillway condition">{{ old('spillway_condition_comment') }}</textarea>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="1">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary next-btn" data-next="3">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 4: Capacity Details -->
                        <div class="form-section" id="section-3">
                            <h4 class="section-title"><i class="fas fa-calculator text-primary"></i> Capacity & Volume</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="capacity" class="form-label">Total Capacity (m³)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-database"></i>
                                        <input type="number" step="0.01" name="capacity" id="capacity"
                                            value="{{ old('capacity') }}" class="form-control" placeholder="Total capacity in cubic meters">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="wall_volume" class="form-label">Wall Volume (m³)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-cube"></i>
                                        <input type="number" step="0.01" name="wall_volume" id="wall_volume"
                                            value="{{ old('wall_volume') }}" class="form-control" placeholder="Wall volume in cubic meters">
                                    </div>
                                </div>

                                <!-- Calculations -->
                                <div class="col-12 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6><i class="fas fa-calculator text-info"></i> Capacity Calculations</h6>
                                            <div class="row mt-2">
                                                <div class="col-md-4">
                                                    <label>Current Water Level</label>
                                                    <div class="form-control text-center" id="current_water_level_display">0%</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Current Volume (m³)</label>
                                                    <div class="form-control text-center" id="current_volume_display">0</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Storage Status</label>
                                                    <div class="form-control text-center" id="storage_status_display">Empty</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="2">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary next-btn" data-next="4">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 5: Structural Features -->
                        <div class="form-section" id="section-4">
                            <h4 class="section-title"><i class="fas fa-tools text-primary"></i> Structural Features</h4>
                            
                            <div class="row">
                                <!-- Protective Features -->
                                <div class="col-12 mb-3">
                                    <h5><i class="fas fa-shield-alt text-secondary"></i> Protective Features</h5>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="has_stone_pitching" name="has_stone_pitching" value="1"
                                               {{ old('has_stone_pitching') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_stone_pitching">
                                            <i class="fas fa-layer-group me-2"></i> Has Stone Pitching (Upstream)
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="has_star_grass" name="has_star_grass" value="1"
                                               {{ old('has_star_grass') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_star_grass">
                                            <i class="fas fa-seedling me-2"></i> Has Star Grass (Downstream)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="3">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary next-btn" data-next="5">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 6: Location & Coordinates -->
                        <div class="form-section" id="section-5">
                            <h4 class="section-title"><i class="fas fa-map-marker-alt text-primary"></i> Location Details</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-globe-americas"></i>
                                        <input type="number" step="0.000001" name="latitude" id="latitude"
                                            value="{{ old('latitude') }}" class="form-control" 
                                            placeholder="e.g., -15.416667" min="-90" max="90">
                                    </div>
                                    <small class="text-muted">Southern hemisphere: negative values (-)</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-globe-americas"></i>
                                        <input type="number" step="0.000001" name="longitude" id="longitude"
                                            value="{{ old('longitude') }}" class="form-control" 
                                            placeholder="e.g., 28.283333" min="-180" max="180">
                                    </div>
                                    <small class="text-muted">Eastern hemisphere: positive values (+)</small>
                                </div>

                                <!-- Map Preview -->
                                <div class="col-12 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6><i class="fas fa-map-marked-alt text-info"></i> Location Preview</h6>
                                            <div id="map-preview" style="height: 200px; background: #e9ecef; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                                <div class="text-center">
                                                    <i class="fas fa-map-marker-alt fa-2x text-danger mb-2"></i>
                                                    <p class="mb-0">Enter coordinates to preview location</p>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <button type="button" id="use-current-location" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-location-arrow me-1"></i> Use Current Location
                                                </button>
                                                <button type="button" id="pick-on-map" class="btn btn-sm btn-outline-secondary ms-2">
                                                    <i class="fas fa-map me-1"></i> Pick on Map
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="4">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary next-btn" data-next="6">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 7: Review & Submit -->
                        <div class="form-section" id="section-6">
                            <h4 class="section-title"><i class="fas fa-clipboard-check text-primary"></i> Review & Submit</h4>
                            
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Please review all information before submitting. You can go back to edit any section.
                                    </div>
                                </div>

                                <!-- Summary Cards -->
                                <div class="col-md-6 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>Dam Name:</th>
                                                    <td id="review_name">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Village:</th>
                                                    <td id="review_village">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Ward:</th>
                                                    <td id="review_ward">-</td>
                                                </tr>
                                                <tr>
                                                    <th>District:</th>
                                                    <td id="review_district">-</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-ruler-combined"></i> Technical Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>Wall Height:</th>
                                                    <td id="review_height">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Capacity:</th>
                                                    <td id="review_capacity">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Water Depth:</th>
                                                    <td id="review_water_depth">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Wall Condition:</th>
                                                    <td id="review_wall_condition">-</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- General Comments -->
                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-comments"></i> General Comments</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="general_comments" class="form-label">General Comments</label>
                                                <textarea class="form-control" id="general_comments" name="general_comments" 
                                                    rows="4" placeholder="Any general comments or remarks">{{ old('general_comments') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="5">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save me-2"></i> Save Dam Record
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS and JavaScript remain the same -->
<!-- Add the CSS from the borehole form -->
<style>
:root {
    --primary-color: #2c7fb8;
    --secondary-color: #7fcdbb;
    --accent-color: #edf8b1;
    --dark-color: #253237;
    --light-color: #f8f9fa;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #17a2b8;
    --border-radius: 12px;
    --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, #1d6fa5 100%);
    --gradient-success: linear-gradient(135deg, var(--success-color) 0%, #1e7e34 100%);
    --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

* {
    box-sizing: border-box;
}

.container {
    max-width: 1200px;
}

.card {
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border: none;
    overflow: hidden;
    background: white;
    margin: 2rem auto;
    transition: var(--transition);
}

.card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.card-header {
    background: var(--gradient-primary);
    color: white;
    padding: 2.5rem 2rem;
    border-bottom: none;
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.card-header h2, .card-header p {
    position: relative;
    z-index: 1;
    margin: 0;
}

.card-header h2 {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.card-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 400;
}

.card-body {
    padding: 2.5rem;
    background: var(--gradient-card);
    position: relative;
}

/* Progress Bar */
.progress {
    height: 10px;
    border-radius: 5px;
    margin-bottom: 2.5rem;
    background: linear-gradient(90deg, #e9ecef, #f8f9fa);
    overflow: hidden;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    position: relative;
}

.progress-bar {
    background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
    border-radius: 5px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background-image: linear-gradient(
        -45deg,
        rgba(255, 255, 255, 0.2) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255, 255, 255, 0.2) 50%,
        rgba(255, 255, 255, 0.2) 75%,
        transparent 75%,
        transparent
    );
    background-size: 1rem 1rem;
    animation: move 1s linear infinite;
}

@keyframes move {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 1rem 0;
    }
}

/* Navigation Pills */
.form-navigation {
    margin-bottom: 2.5rem;
}

.nav-pills-custom {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(44, 127, 184, 0.1);
    position: relative;
    overflow: hidden;
}

.nav-pills-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.75rem;
}

.nav-pill {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 1rem 1.5rem;
    font-weight: 600;
    color: var(--dark-color);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 140px;
    justify-content: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    font-size: 0.9rem;
}

.nav-pill::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s;
}

.nav-pill:hover::before {
    left: 100%;
}

.nav-pill:hover {
    background: linear-gradient(145deg, rgba(44, 127, 184, 0.1), rgba(127, 205, 187, 0.1));
    border-color: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(44, 127, 184, 0.2);
}

.nav-pill.active {
    background: var(--gradient-primary);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(44, 127, 184, 0.3);
}

.nav-pill.completed {
    background: linear-gradient(145deg, rgba(40, 167, 69, 0.15), rgba(40, 167, 69, 0.1));
    color: var(--success-color);
    border-color: var(--success-color);
    position: relative;
}

.nav-pill.completed::after {
    content: '✓';
    position: absolute;
    top: -6px;
    right: -6px;
    background: var(--success-color);
    color: white;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);
}

.nav-pill i {
    font-size: 1.1rem;
    transition: var(--transition);
}

.nav-pill.active i {
    transform: scale(1.1);
}

/* Form Sections */
.form-section {
    display: none;
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    border-radius: var(--border-radius);
    padding: 2.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
    margin-bottom: 1rem;
}

.form-section.active {
    display: block;
}

@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(30px);
    }
    to { 
        opacity: 1; 
        transform: translateY(0);
    }
}

.section-title {
    color: var(--primary-color);
    border-bottom: 3px solid var(--secondary-color);
    padding-bottom: 1.25rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    font-weight: 700;
    position: relative;
    font-size: 1.5rem;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 80px;
    height: 3px;
    background: var(--primary-color);
    border-radius: 2px;
}

.section-title i {
    font-size: 1.75rem;
    padding: 0.75rem;
    background: linear-gradient(145deg, rgba(44, 127, 184, 0.1), rgba(127, 205, 187, 0.1));
    border-radius: 10px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Form Controls */
.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
    display: block;
}

.form-label.required::after {
    content: " *";
    color: var(--danger-color);
    font-weight: bold;
}

.form-control, .form-select {
    border-radius: 8px;
    padding: 0.875rem 1rem;
    border: 2px solid #e9ecef;
    transition: var(--transition);
    font-size: 0.95rem;
    background: #fafbfc;
    width: 100%;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(44, 127, 184, 0.15);
    background: white;
    transform: translateY(-1px);
    outline: none;
}

.form-control:hover, .form-select:hover {
    border-color: rgba(44, 127, 184, 0.5);
    background: white;
}

/* Input Groups with Icons */
.input-group-icon {
    position: relative;
}

.input-group-icon .form-control {
    padding-left: 3rem;
}

.input-group-icon i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-color);
    z-index: 5;
    font-size: 1.1rem;
}

/* Buttons */
.btn {
    border-radius: 8px;
    padding: 0.875rem 1.75rem;
    font-weight: 600;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    border: none;
    font-size: 0.95rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
    box-shadow: 0 4px 15px rgba(44, 127, 184, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(145deg, #1d6fa5, var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(44, 127, 184, 0.4);
    color: white;
}

.btn-success {
    background: var(--gradient-success);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-success:hover {
    background: linear-gradient(145deg, #1e7e34, var(--success-color));
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(40, 167, 69, 0.4);
    color: white;
}

.btn-outline-primary {
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    background: white;
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(44, 127, 184, 0.3);
}

/* Button Loading State */
.btn.loading {
    position: relative;
    color: transparent !important;
    pointer-events: none;
}

.btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.btn.loading i:not(.fa-spinner) {
    opacity: 0;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Form Footer */
.form-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2.5rem;
    padding-top: 2rem;
    border-top: 2px solid #f1f3f4;
    gap: 1rem;
}

/* Validation States */
.is-valid {
    border-color: var(--success-color) !important;
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.15) !important;
}

.is-invalid {
    border-color: var(--danger-color) !important;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15) !important;
    animation: shake 0.5s ease-in-out;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: var(--danger-color);
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

/* Alert Styles */
.alert {
    border: none;
    border-radius: 8px;
    padding: 1rem 1.5rem;
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, var(--success-color), #1e7e34);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, var(--danger-color), #c82333);
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, var(--info-color), #138496);
    color: white;
}

/* Grid System Enhancements */
.row {
    margin-left: -0.75rem;
    margin-right: -0.75rem;
}

.col-md-6, .col-md-4, .col-md-12, .col-12 {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

.mb-3 {
    margin-bottom: 1.5rem !important;
}

.mt-4 {
    margin-top: 2rem !important;
}

/* Textarea Styles */
textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

/* Select Dropdown Enhancements */
.form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 16px 12px;
    padding-right: 2.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    
    .card {
        margin: 1rem auto;
    }
    
    .card-header {
        padding: 2rem 1.5rem;
    }
    
    .card-header h2 {
        font-size: 1.75rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .form-section {
        padding: 1.5rem;
    }
    
    .form-footer {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-footer .btn {
        width: 100%;
        justify-content: center;
    }

    .nav-pills-container {
        gap: 0.5rem;
    }
    
    .nav-pill {
        min-width: 100px;
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
    }
    
    .nav-pill span {
        display: none;
    }
    
    .section-title {
        font-size: 1.25rem;
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .section-title::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .section-title i {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .btn {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
    }
}
</style>
    

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form navigation variables
    const sections = document.querySelectorAll('.form-section');
    const navPills = document.querySelectorAll('.nav-pill');
    const progressBar = document.getElementById('form-progress');
    const form = document.getElementById('damForm');
    let currentSection = 0;
    
    console.log('Dam Form initialized');

    // Initialize form sections
    function initializeFormSections() {
        sections.forEach((section, index) => {
            section.setAttribute('data-section-id', index);
            section.style.display = 'none';
        });
        showSection(0);
    }
    
    // Show specific section
    function showSection(index) {
        sections.forEach((section, i) => {
            if (i === index) {
                section.style.display = 'block';
                section.classList.add('active');
            } else {
                section.style.display = 'none';
                section.classList.remove('active');
            }
        });
    }
    
    // Initialize form
    initializeFormSections();
    updateProgress(0);
    updateNavigation(0);
    
    // Navigation pill click handlers
    navPills.forEach((pill, index) => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSection(index);
        });
    });
    
    // Next/Previous button handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.next-btn')) {
            const nextSection = parseInt(e.target.closest('.next-btn').dataset.next);
            navigateToSection(nextSection);
        }
        
        if (e.target.closest('.prev-btn')) {
            const prevSection = parseInt(e.target.closest('.prev-btn').dataset.prev);
            navigateToSection(prevSection);
        }
    });
    
    // Navigation function
    function navigateToSection(targetSection) {
        // Validate current section only when moving forward
        if (targetSection > currentSection && !validateCurrentSection()) {
            showSection(currentSection);
            return false;
        }
        
        currentSection = targetSection;
        
        setTimeout(() => {
            showSection(targetSection);
            updateProgress(targetSection);
            updateNavigation(targetSection);
            
            // Scroll to top of form
            document.querySelector('.card-body').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
            
            // Update review section if we're going to it
            if (targetSection === 6) {
                updateReviewSection();
            }
        }, 50);
        
        return true;
    }
    
    // Update progress bar
    function updateProgress(index) {
        const progressPercentage = ((index + 1) / sections.length) * 100;
        progressBar.style.width = `${progressPercentage}%`;
        progressBar.setAttribute('aria-valuenow', progressPercentage);
    }
    
    // Update navigation pills
    function updateNavigation(activeIndex) {
        navPills.forEach((pill, index) => {
            pill.classList.remove('active', 'completed');
            
            if (index === activeIndex) {
                pill.classList.add('active');
            } else if (index < activeIndex) {
                pill.classList.add('completed');
            }
        });
    }
    
    // Validation function for current section
    function validateCurrentSection() {
        const currentSectionEl = sections[currentSection];
        const requiredFields = currentSectionEl.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
                showFieldError(field, 'This field is required');
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                hideFieldError(field);
            }
        });
        
        if (!isValid) {
            // Focus first invalid field
            const firstInvalid = currentSectionEl.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            showNotification('Please fill in all required fields before proceeding.', 'error');
        }
        
        return isValid;
    }
    
    // Field validation helpers
    function showFieldError(field, message) {
        hideFieldError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = message;
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '0.875em';
        errorDiv.style.marginTop = '0.25rem';
        
        field.parentNode.appendChild(errorDiv);
    }
    
    function hideFieldError(field) {
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }
    
    // Real-time validation
    document.querySelectorAll('[required]').forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                hideFieldError(this);
            }
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                hideFieldError(this);
            }
        });
    });
    
    // Notification system
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
    
    // Capacity calculations
    const capacityInput = document.getElementById('capacity');
    const waterDepthInput = document.getElementById('water_depth');
    const currentWaterLevelDisplay = document.getElementById('current_water_level_display');
    const currentVolumeDisplay = document.getElementById('current_volume_display');
    const storageStatusDisplay = document.getElementById('storage_status_display');
    
    function updateCapacityCalculations() {
        const capacity = parseFloat(capacityInput?.value) || 0;
        const waterDepth = parseFloat(waterDepthInput?.value) || 0;
        
        let waterLevelPercentage = 0;
        let currentVolume = 0;
        let status = 'Empty';
        let statusClass = 'text-danger';
        
        if (capacity > 0 && waterDepth > 0) {
            // Simple calculation (assuming linear relationship)
            waterLevelPercentage = Math.min(100, (waterDepth / (capacity * 0.001)) * 100);
            currentVolume = capacity * (waterLevelPercentage / 100);
            
            if (waterLevelPercentage >= 80) {
                status = 'Full';
                statusClass = 'text-success';
            } else if (waterLevelPercentage >= 50) {
                status = 'Moderate';
                statusClass = 'text-warning';
            } else if (waterLevelPercentage >= 20) {
                status = 'Low';
                statusClass = 'text-warning';
            } else {
                status = 'Critical';
                statusClass = 'text-danger';
            }
        }
        
        if (currentWaterLevelDisplay) {
            currentWaterLevelDisplay.textContent = waterLevelPercentage.toFixed(1) + '%';
            currentWaterLevelDisplay.className = 'form-control text-center ' + statusClass;
        }
        
        if (currentVolumeDisplay) {
            currentVolumeDisplay.textContent = currentVolume.toFixed(0) + ' m³';
        }
        
        if (storageStatusDisplay) {
            storageStatusDisplay.textContent = status;
            storageStatusDisplay.className = 'form-control text-center ' + statusClass;
        }
    }
    
    // Add event listeners for capacity calculations
    if (capacityInput) capacityInput.addEventListener('input', updateCapacityCalculations);
    if (waterDepthInput) waterDepthInput.addEventListener('input', updateCapacityCalculations);
    
    // Initialize calculations
    updateCapacityCalculations();
    
    // Location features
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const useCurrentLocationBtn = document.getElementById('use-current-location');
    
    if (useCurrentLocationBtn) {
        useCurrentLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        if (latitudeInput) latitudeInput.value = position.coords.latitude.toFixed(6);
                        if (longitudeInput) longitudeInput.value = position.coords.longitude.toFixed(6);
                        showNotification('Location captured successfully!', 'success');
                        updateMapPreview();
                    },
                    function(error) {
                        showNotification('Unable to get location: ' + error.message, 'error');
                    }
                );
            } else {
                showNotification('Geolocation is not supported by your browser', 'error');
            }
        });
    }
    
    // Map preview update
    function updateMapPreview() {
        const latitude = parseFloat(latitudeInput?.value);
        const longitude = parseFloat(longitudeInput?.value);
        const mapPreview = document.getElementById('map-preview');
        
        if (mapPreview && !isNaN(latitude) && !isNaN(longitude)) {
            // Create a simple map link (for actual maps, you'd use Leaflet or Google Maps API)
            mapPreview.innerHTML = `
                <div class="text-center p-3">
                    <i class="fas fa-map-marker-alt fa-3x text-danger mb-2"></i>
                    <p class="mb-1"><strong>Coordinates:</strong></p>
                    <p class="mb-1">${latitude.toFixed(6)}, ${longitude.toFixed(6)}</p>
                    <a href="https://www.google.com/maps?q=${latitude},${longitude}" 
                       target="_blank" class="btn btn-sm btn-primary mt-2">
                        <i class="fas fa-external-link-alt me-1"></i> View on Google Maps
                    </a>
                </div>
            `;
        }
    }
    
    // Add event listeners for map preview
    if (latitudeInput) latitudeInput.addEventListener('input', updateMapPreview);
    if (longitudeInput) longitudeInput.addEventListener('input', updateMapPreview);
    
    // Initialize map preview
    updateMapPreview();
    
    // Update review section
    function updateReviewSection() {
        // Basic info
        document.getElementById('review_name').textContent = document.getElementById('name')?.value || '-';
        document.getElementById('review_village').textContent = document.getElementById('village')?.value || '-';
        document.getElementById('review_ward').textContent = document.getElementById('ward')?.value || '-';
        document.getElementById('review_district').textContent = document.getElementById('district')?.value || '-';
        
        // Technical details
        document.getElementById('review_height').textContent = document.getElementById('wall_height')?.value ? 
            document.getElementById('wall_height').value + ' m' : '-';
        document.getElementById('review_capacity').textContent = document.getElementById('capacity')?.value ? 
            document.getElementById('capacity').value + ' m³' : '-';
        document.getElementById('review_water_depth').textContent = document.getElementById('water_depth')?.value ? 
            document.getElementById('water_depth').value + ' m' : '-';
        
        const wallCondition = document.getElementById('wall_condition')?.value || '';
        document.getElementById('review_wall_condition').textContent = wallCondition || '-';
    }
    
    // Remove the toggles for fields that don't exist in migration
    // Remove: damTypeSelect toggle
    // Remove: stone pitching details toggle (since only has_stone_pitching checkbox exists)
    // Remove: star grass details toggle (since only has_star_grass checkbox exists)
    
    console.log('Dam form JavaScript initialized successfully');
});
</script>
@endsection