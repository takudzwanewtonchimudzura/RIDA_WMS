@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">New Inspection - {{ $dam->name }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">All Dams</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dams.show', $dam->id) }}">{{ $dam->name }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dams.maintenance.index', $dam->id) }}">Inspections</a></li>
                            <li class="breadcrumb-item active">New</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('dams.maintenance.index', $dam->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Inspections
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Inspection Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('dams.maintenance.store', $dam->id) }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="maintenance_type" class="form-label">Inspection Type *</label>
                                <select class="form-control @error('maintenance_type') is-invalid @enderror" 
                                        id="maintenance_type" name="maintenance_type" required>
                                    <option value="">Select Type</option>
                                    @foreach($maintenanceTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('maintenance_type') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('maintenance_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="maintenance_date" class="form-label">Inspection Date *</label>
                                <input type="date" class="form-control @error('maintenance_date') is-invalid @enderror" 
                                       id="maintenance_date" name="maintenance_date" 
                                       value="{{ old('maintenance_date', date('Y-m-d')) }}" required>
                                @error('maintenance_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Wall Repairs Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Wall Repairs</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="wall_repair_details" class="form-label">Nature of Wall Repairs/Rehabilitation</label>
                                    <textarea class="form-control @error('wall_repair_details') is-invalid @enderror" 
                                              id="wall_repair_details" name="wall_repair_details" 
                                              rows="3">{{ old('wall_repair_details') }}</textarea>
                                    <small class="text-muted">Describe any repairs or rehabilitation done on the dam wall</small>
                                    @error('wall_repair_details')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Spillway Repairs Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Spillway Repairs</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="spillway_repair_details" class="form-label">Nature of Spillway Repairs/Rehabilitation</label>
                                    <textarea class="form-control @error('spillway_repair_details') is-invalid @enderror" 
                                              id="spillway_repair_details" name="spillway_repair_details" 
                                              rows="3">{{ old('spillway_repair_details') }}</textarea>
                                    <small class="text-muted">Describe any repairs or rehabilitation done on the spillway</small>
                                    @error('spillway_repair_details')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Structural Features -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Structural Features Maintenance</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="stone_pitching_replaced" name="stone_pitching_replaced" 
                                                   value="1" {{ old('stone_pitching_replaced') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="stone_pitching_replaced">
                                                Stone Pitching Replaced/Repaired
                                            </label>
                                            <small class="d-block text-muted">Upstream stone pitching maintenance</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="star_grass_replanted" name="star_grass_replanted" 
                                                   value="1" {{ old('star_grass_replanted') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="star_grass_replanted">
                                                Star Grass Replanted
                                            </label>
                                            <small class="d-block text-muted">Downstream star grass maintenance</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Costs Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Costs</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="estimated_cost" class="form-label">Estimated Cost (ZMW)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">ZMW</span>
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('estimated_cost') is-invalid @enderror" 
                                                   id="estimated_cost" name="estimated_cost" 
                                                   value="{{ old('estimated_cost') }}">
                                            @error('estimated_cost')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="actual_cost" class="form-label">Actual Cost (ZMW)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">ZMW</span>
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('actual_cost') is-invalid @enderror" 
                                                   id="actual_cost" name="actual_cost" 
                                                   value="{{ old('actual_cost') }}">
                                            @error('actual_cost')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contractor Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Contractor Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="contractor_name" class="form-label">Contractor Name</label>
                                        <input type="text" class="form-control @error('contractor_name') is-invalid @enderror" 
                                               id="contractor_name" name="contractor_name" 
                                               value="{{ old('contractor_name') }}">
                                        @error('contractor_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contractor_contact" class="form-label">Contractor Contact</label>
                                        <input type="text" class="form-control @error('contractor_contact') is-invalid @enderror" 
                                               id="contractor_contact" name="contractor_contact" 
                                               value="{{ old('contractor_contact') }}">
                                        @error('contractor_contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- General Comments -->
                        <div class="mb-4">
                            <label for="general_comments" class="form-label">General Comments</label>
                            <textarea class="form-control @error('general_comments') is-invalid @enderror" 
                                      id="general_comments" name="general_comments" 
                                      rows="4">{{ old('general_comments') }}</textarea>
                            <small class="text-muted">Any additional comments or observations</small>
                            @error('general_comments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="other_works" class="form-label">Other Works Carried Out</label>
                            <textarea class="form-control @error('other_works') is-invalid @enderror" 
                                      id="other_works" name="other_works" 
                                      rows="3">{{ old('other_works') }}</textarea>
                            @error('other_works')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dams.maintenance.index', $dam->id) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Inspection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar - Dam Information -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dam Information</h6>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $dam->name }}</h5>
                    <p class="card-text">
                        <strong>Location:</strong><br>
                        {{ $dam->full_location }}
                    </p>
                    
                    <div class="mb-3">
                        <strong>Current Status:</strong><br>
                        <span class="badge bg-{{ $dam->condition_badge }}">
                            {{ ucfirst($dam->wall_condition) }}
                        </span>
                        @if($dam->wall_condition_comment)
                            <small class="d-block text-muted mt-1">{{ $dam->wall_condition_comment }}</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Water Level:</strong><br>
                        <div class="progress mt-1" style="height: 20px;">
<!-- #region              --><div class="progress-bar bg-info" role="progressbar" 
                                  style="width: {{ $dam->water_level_percentage ?? 0 }}%"
                                  aria-valuenow="{{ $dam->water_level_percentage ?? 0 }}" 
                                  aria-valuemin="0" aria-valuemax="100">
                                  {{ number_format($dam->water_level_percentage ?? 0, 1) }}%
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $dam->water_depth ?? 0 }}m / {{ $dam->capacity ?? 'N/A' }}m³
                        </small>
                    </div>

                    <div class="mb-3">
                        <strong>Structural Features:</strong><br>
                        <span class="badge bg-{{ $dam->has_stone_pitching ? 'success' : 'secondary' }}">
                            Stone Pitching: {{ $dam->has_stone_pitching ? 'Yes' : 'No' }}
                        </span>
                        <span class="badge bg-{{ $dam->has_star_grass ? 'success' : 'secondary' }}">
                            Star Grass: {{ $dam->has_star_grass ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <h6>Previous Inspections:</h6>
                        @if($dam->maintenanceRecords->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($dam->maintenanceRecords->take(3) as $record)
                                    <li class="list-group-item px-0">
                                        <small>
                                            {{ $record->maintenance_date->format('M d, Y') }} - 
                                            {{ $record->maintenance_type_text }}
                                            <span class="badge bg-{{ $record->status_badge }} float-end">
                                                {{ ucfirst($record->status) }}
                                            </span>
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No previous inspections</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Set default date to today
    document.getElementById('maintenance_date').valueAsDate = new Date();
</script>
@endpush
@endsection