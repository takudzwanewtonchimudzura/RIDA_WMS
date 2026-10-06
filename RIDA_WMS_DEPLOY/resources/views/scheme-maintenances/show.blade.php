@extends('layouts.app')

@section('title', 'Scheme Maintenance Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('scheme-maintenances.index') }}">Scheme Maintenance</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-eye me-2"></i>Scheme Maintenance Details</h4>
                <div class="btn-group">
                    @if(auth()->user()->isAdmin() || (auth()->user()->isOperator() && (int) $maintenance->created_by === (int) auth()->id()))<a href="{{ route('scheme-maintenances.edit', $maintenance->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>@endif
                    <a href="{{ route('scheme-maintenances.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Scheme Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Scheme Name:</th>
                                        <td>{{ $maintenance->scheme_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Village:</th>
                                        <td>{{ $maintenance->village }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ward:</th>
                                        <td>{{ $maintenance->ward }}</td>
                                    </tr>
                                    <tr>
                                        <th>Water Source Type:</th>
                                        <td>{{ $maintenance->water_source_type ?? 'Not specified' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Year Established:</th>
                                        <td>{{ $maintenance->year_established ?? 'Not specified' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Pump Type:</th>
                                        <td>{{ $maintenance->pump_type ?? 'Not specified' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Power Source:</th>
                                        <td>{{ $maintenance->power_type ?? 'Not specified' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>{{ $maintenance->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-ruler-combined me-2"></i>Scheme Infrastructure</h5>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6>Network Length</h6>
                                        <h3>{{ $maintenance->total_network_metres ?? 0 }} m</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6>Storage Tanks</h6>
                                        <h3>{{ $maintenance->total_storage_tanks_m3 ?? 0 }} m³</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6>Stand Pipes</h6>
                                        <h3>{{ $maintenance->number_of_standpipes ?? 0 }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-tools me-2"></i>Maintenance Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-user-cog me-2"></i>Repair Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="40%">Repaired By:</th>
                                                <td>{{ $maintenance->repaired_by }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date Repaired:</th>
                                                <td>
                                                    @if($maintenance->date_repaired)
                                                        {{ $maintenance->date_repaired->format('d/m/Y') }}
                                                    @else
                                                        Not specified
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date Last Repaired:</th>
                                                <td>
                                                    @if($maintenance->date_last_repaired)
                                                        {{ $maintenance->date_last_repaired->format('d/m/Y') }}
                                                    @else
                                                        Not specified
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Spares Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="40%">Spares Received By:</th>
                                                <td>{{ $maintenance->spares_received_by ?? 'Not specified' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Contact Details:</th>
                                                <td>{{ $maintenance->contact_details ?? 'Not specified' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-tasks me-2"></i>Works Performed</h5>
                        <div class="card">
                            <div class="card-body">
                                <h6><i class="fas fa-wrench me-2"></i>Works Done</h6>
                                <p class="mb-4">{{ $maintenance->works_done ?? 'No details provided' }}</p>
                                
                                @if($maintenance->spares_fitted)
                                <h6><i class="fas fa-cog me-2"></i>Spares Fitted</h6>
                                <p class="mb-0">{{ $maintenance->spares_fitted }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($maintenance->remarks)
                    <div class="col-md-12 mb-4">
                        <h5 class="border-bottom pb-2"><i class="fas fa-comment me-2"></i>Remarks</h5>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0">{{ $maintenance->remarks }}</p>
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
                            <i class="fas fa-calendar me-1"></i>Created: {{ $maintenance->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <i class="fas fa-sync-alt me-1"></i>Last Updated: {{ $maintenance->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Scheme Information -->
        @if($maintenance->piped_water_scheme_id)
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-water me-2"></i>Related Scheme Information</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This maintenance record is linked to a piped water scheme. 
                    <a href="{{ route('piped-water-schemes.show', $maintenance->piped_water_scheme_id) }}" class="alert-link">
                        View scheme details
                    </a>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <div>
                        @if(auth()->user()->isAdmin() || auth()->user()->isOperator())<a href="{{ route('scheme-maintenances.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Maintenance
                        </a>@endif
                        <button class="btn btn-success" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </button>
                    </div>
                    <div>
                        @if($maintenance->piped_water_scheme_id)
                        <a href="{{ route('piped-water-schemes.show', $maintenance->piped_water_scheme_id) }}" class="btn btn-info">
                            <i class="fas fa-external-link-alt me-2"></i>View Scheme
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection