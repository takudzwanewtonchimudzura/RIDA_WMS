@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Inspection Details - {{ $dam->name }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">All Dams</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dams.show', $dam->id) }}">{{ $dam->name }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dams.maintenance.index', $dam->id) }}">Inspections</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('dams.maintenance.index', $dam->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Inspections
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Inspection Header Card -->
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Inspection Record #{{ $maintenance->id }}
                    </h6>
                    <div class="btn-group" role="group">
                        <a href="{{ route('dams.maintenance.edit', [$dam->id, $maintenance->id]) }}" 
                           class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('dams.maintenance.destroy', [$dam->id, $maintenance->id]) }}" 
                              method="POST" class="d-inline" 
                              onsubmit="return confirm('Delete this inspection record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>Type:</strong><br>
                            <span class="badge bg-primary">
                                {{ $maintenance->maintenance_type_text }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Date:</strong><br>
                            {{ $maintenance->maintenance_date->format('F d, Y') }}
                        </div>
                        <div class="col-md-4">
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{ $maintenance->status_badge }}">
                                {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Wall Repairs Section -->
                    @if($maintenance->wall_repair_details)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Wall Repairs & Rehabilitation</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $maintenance->wall_repair_details }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Spillway Repairs Section -->
                    @if($maintenance->spillway_repair_details)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Spillway Repairs & Rehabilitation</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $maintenance->spillway_repair_details }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Structural Features -->
                    @if($maintenance->stone_pitching_replaced || $maintenance->star_grass_replanted)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Structural Features Maintenance</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($maintenance->stone_pitching_replaced)
                                <div class="col-md-6">
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>Stone Pitching Replaced/Repaired</strong><br>
                                        <small>Upstream stone pitching maintenance completed</small>
                                    </div>
                                </div>
                                @endif
                                
                                @if($maintenance->star_grass_replanted)
                                <div class="col-md-6">
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>Star Grass Replanted</strong><br>
                                        <small>Downstream star grass maintenance completed</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Costs Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Cost Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Estimated Cost</h6>
                                    <p class="h4">
                                        @if($maintenance->estimated_cost)
                                            ZMW {{ number_format($maintenance->estimated_cost, 2) }}
                                        @else
                                            <span class="text-muted">Not estimated</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Actual Cost</h6>
                                    <p class="h4 text-primary">
                                        @if($maintenance->actual_cost)
                                            <strong>ZMW {{ number_format($maintenance->actual_cost, 2) }}</strong>
                                        @else
                                            <span class="text-muted">Not recorded</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            @if($maintenance->estimated_cost && $maintenance->actual_cost)
                                @php
                                    $costDifference = $maintenance->actual_cost - $maintenance->estimated_cost;
                                    $percentageDifference = ($costDifference / $maintenance->estimated_cost) * 100;
                                @endphp
                                <div class="mt-3">
                                    <strong>Cost Variance:</strong>
                                    <span class="badge bg-{{ $costDifference <= 0 ? 'success' : 'danger' }}">
                                        {{ $costDifference >= 0 ? '+' : '' }}{{ number_format($costDifference, 2) }}
                                        ({{ number_format($percentageDifference, 1) }}%)
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contractor Information -->
                    @if($maintenance->contractor_name)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Contractor Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Contractor Name:</strong><br>
                                    {{ $maintenance->contractor_name }}
                                </div>
                                @if($maintenance->contractor_contact)
                                <div class="col-md-6">
                                    <strong>Contact:</strong><br>
                                    {{ $maintenance->contractor_contact }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Other Works -->
                    @if($maintenance->other_works)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Other Works Carried Out</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $maintenance->other_works }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- General Comments -->
                    @if($maintenance->general_comments)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">General Comments & Observations</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $maintenance->general_comments }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- System Information -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">System Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Created:</small><br>
                                    {{ $maintenance->created_at->format('F d, Y H:i') }}
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Last Updated:</small><br>
                                    {{ $maintenance->updated_at->format('F d, Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Dam Information -->
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
                        @php
                            // Calculate percentage manually
                            $waterLevelPercentage = 0;
                            if ($dam->capacity && $dam->water_depth && $dam->capacity > 0) {
                                $waterLevelPercentage = min(100, ($dam->water_depth / $dam->capacity) * 100);
                            }
                        @endphp
                        <div class="progress mt-1" style="height: 20px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $waterLevelPercentage }}%"
                                 aria-valuenow="{{ $waterLevelPercentage }}" 
                                 aria-valuemin="0" aria-valuemax="100">
                                {{ number_format($waterLevelPercentage, 1) }}%
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $dam->water_depth ?? 0 }}m / {{ $dam->capacity ?? 'N/A' }}m³
                        </small>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('dams.show', $dam->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-water"></i> View Dam Details
                        </a>
                        <a href="{{ route('dams.maintenance.create', $dam->id) }}" class="btn btn-outline-success">
                            <i class="fas fa-plus"></i> New Inspection
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dams.maintenance.edit', [$dam->id, $maintenance->id]) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit This Inspection
                        </a>
                        <form action="{{ route('dams.maintenance.destroy', [$dam->id, $maintenance->id]) }}" 
                              method="POST" 
                              onsubmit="return confirm('Delete this inspection record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Delete Inspection
                            </button>
                        </form>
                        <a href="{{ route('dams.maintenance.index', $dam->id) }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-list"></i> All Inspections
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection