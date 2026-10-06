{{-- resources/views/piped-water-schemes/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-eye"></i> Maintenance Details</h5>
                    <div>
                        <a href="{{ route('piped-water-schemes.edit', $maintenance->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('piped-water-schemes.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-list"></i> All Maintenances
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Scheme Information Card -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-water"></i> Scheme Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Scheme Name:</strong><br>
                                                <span >{{ $maintenance->name }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Village:</strong><br>
                                                {{ $maintenance->village }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Ward:</strong><br>
                                                {{ $maintenance->ward }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Water Source:</strong><br>
                                                
                                            {{ $maintenance->water_source }}
                                        
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Year Established:</strong><br>
                                                {{ $maintenance->year_established ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Parent Scheme:</strong><br>
                                                @if($maintenance->scheme)
                                                    <a href="{{ route('piped_water_schemes.show', $maintenance->scheme->id) }}" 
                                                       class="text-info">
                                                        {{ $maintenance->scheme->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not linked</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Details Card -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-cogs"></i> Technical Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p><strong>Pump Type:</strong><br>
                                                {{ $maintenance->pump_type ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><strong>Power Type:</strong><br>
                                                {{ $maintenance->power_type ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><strong>Network Length:</strong><br>
                                                {{ $maintenance->network_length ? number_format($maintenance->network_length) . ' m' : 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><strong>Storage Capacity:</strong><br>
                                                {{ $maintenance->storage_tanks ? number_format($maintenance->storage_tanks) . ' m³' : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p><strong>Stand Pipes:</strong><br>
                                                {{ $maintenance->stand_pipes_count ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-9">
                                            <p><strong>Works Done:</strong><br>
                                                {{ $maintenance->works_done ?? 'No details provided' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Details Card -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0"><i class="fas fa-tools"></i> Maintenance Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Repaired By:</strong><br>
                                                {{ $maintenance->repaired_by ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Spares Fitted:</strong><br>
                                                {{ $maintenance->spares_fitted ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Spares Received By:</strong><br>
                                                {{ $maintenance->spares_received_by ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Contact Detail:</strong><br>
                                                @if($maintenance->contact_detail)
                                                    <a href="tel:{{ $maintenance->contact_detail }}" class="text-info">
                                                        <i class="fas fa-phone"></i> {{ $maintenance->contact_detail }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Date Repaired:</strong><br>
                                                @if($maintenance->date_repaired)
                                                    <span class="badge badge-success">
                                                        {{ $maintenance->date_repaired->format('d M Y') }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Last Repair Date:</strong><br>
                                                @if($maintenance->date_last_repaired)
                                                    <span class="badge badge-info">
                                                        {{ $maintenance->date_last_repaired->format('d M Y') }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Card -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0"><i class="fas fa-sticky-note"></i> Additional Information</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Remarks:</strong></p>
                                    <div class="alert alert-light border">
                                        {{ $maintenance->remarks ?? 'No remarks provided' }}
                                    </div>
                                    
                                    <!-- Metadata -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-plus"></i> Created: 
                                                {{ $maintenance->created_at->format('d M Y, H:i') }}
                                            </small>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-check"></i> Last Updated: 
                                                {{ $maintenance->updated_at->format('d M Y, H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    @if($maintenance->scheme && $maintenance->scheme->coordinates)
                                        <a href="{{ route('piped-water-schemes.map', $maintenance->id) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-map-marked-alt"></i> View on Map
                                        </a>
                                    @endif
                                    <a href="{{ route('piped-water-schemes.create') }}" 
                                       class="btn btn-outline-success">
                                        <i class="fas fa-plus"></i> New Maintenance
                                    </a>
                                </div>
                                <div>
                                    <form action="{{ route('piped-water-schemes.destroy', $maintenance->id) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this maintenance record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    <a href="{{ route('piped-water-schemes.edit', $maintenance->id) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit Maintenance
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection