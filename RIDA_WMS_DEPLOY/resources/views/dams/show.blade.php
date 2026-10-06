@extends('layouts.app')

@section('title', 'Dam Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">Dams</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-eye me-2"></i>Dam Details: {{ $dam->name }}</h4>
                <div class="btn-group">
                    @if(auth()->user()->isAdmin() || (auth()->user()->isOperator() && (int) $dam->created_by === (int) auth()->id()))
                        <a href="{{ route('dams.edit', $dam->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit me-1"></i>Edit</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route($dam->approved_at ? 'dams.unapprove' : 'dams.approve', $dam) }}">@csrf @method('PATCH')<button class="btn btn-{{ $dam->approved_at ? 'outline-light' : 'success' }} btn-sm" type="submit">{{ $dam->approved_at ? 'Remove approval' : 'Approve' }}</button></form>
                    @endif
                    <a href="{{ route('dams.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>


<!-- In your create.blade.php or edit.blade.php -->
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Dam Name:</th>
                                        <td>{{ $dam->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Village:</th>
                                        <td>{{ $dam->village }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ward:</th>
                                        <td>{{ $dam->ward }}</td>
                                    </tr>
                                    <tr>
                                        <th>District:</th>
                                        <td>{{ $dam->district }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Province:</th>
                                        <td>{{ $dam->province }}</td>
                                    </tr>
                                    <tr>
                                        <th>Coordinates:</th>
                                        <td>{{ $dam->coordinates_at_damwall ?? 'Not specified' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>{{ $dam->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td>{{ $dam->updated_at->format('d/m/Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-ruler-combined me-2"></i>Dam Dimensions</h5>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6>Length of Wall</h6>
                                        <h3>{{ $dam->length_of_wall ?? 0 }} m</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6>Height of Wall</h6>
                                        <h3>{{ $dam->height_of_wall ?? 0 }} m</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6>Water Depth</h6>
                                        <h3>{{ $dam->water_depth ?? 0 }} m</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-tachometer-alt me-2"></i>Capacity Information</h5>
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6>Capacity</h6>
                                        <h3>{{ number_format($dam->capacity ?? 0) }} m³</h3>
                                        <small>Water Storage Capacity</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body">
                                        <h6>Wall Volume</h6>
                                        <h3>{{ number_format($dam->volume_of_wall ?? 0) }} m³</h3>
                                        <small>Wall Construction Volume</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-water me-2"></i>Spillway Details</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-arrows-alt-h me-2"></i>Spillway Width</h6>
                                        <p class="mb-0">{{ $dam->spillway_width ?? 0 }} m</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-layer-group me-2"></i>Spillway Type</h6>
                                        <p class="mb-0">{{ $dam->spillway_type ?? 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-exchange-alt me-2"></i>Throwback</h6>
                                        <p class="mb-0">{{ $dam->throwback ?? 0 }} m</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-clipboard-check me-2"></i>Condition Assessment</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-water me-2"></i>Wall Condition</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $dam->condition_of_the_wall ?? 'No condition assessment available' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-water me-2"></i>Spillway Condition</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $dam->condition_of_spillway ?? 'No condition assessment available' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6><i class="fas fa-layer-group me-2"></i>Upstream Protection</h6>
                                        <p class="mb-0">
                                            @if($dam->stone_pitching_available_upstream)
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Stone Pitching Available</span>
                                            @else
                                                <span class="badge bg-warning"><i class="fas fa-times me-1"></i>No Stone Pitching</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6><i class="fas fa-seedling me-2"></i>Downstream Protection</h6>
                                        <p class="mb-0">
                                            @if($dam->star_grass_available_downstream)
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Star Grass Available</span>
                                            @else
                                                <span class="badge bg-warning"><i class="fas fa-times me-1"></i>No Star Grass</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($dam->general_comments)
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2"><i class="fas fa-comment me-2"></i>General Comments</h5>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0">{{ $dam->general_comments }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>Created: {{ $dam->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <i class="fas fa-sync-alt me-1"></i>Last Updated: {{ $dam->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('dams.maintenance.create', $dam->id) }}" class="btn btn-primary">
                            <i class="fas fa-tools me-2"></i>Add Maintenance Record
                        </a>
                        <a href="{{ route('dams.maintenance.index', $dam->id) }}" class="btn btn-secondary">
                            <i class="fas fa-history me-2"></i>View Maintenance History
                        </a>
                    </div>
                    <div>
                        <a href="#" class="btn btn-info">
                            <i class="fas fa-map-marked-alt me-2"></i>View on Map
                        </a>
                        <button class="btn btn-success" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection