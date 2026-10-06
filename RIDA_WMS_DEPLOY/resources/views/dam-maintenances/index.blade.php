@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Dam Inspections - {{ $dam->name }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">All Dams</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dams.show', $dam->id) }}">{{ $dam->name }}</a></li>
                            <li class="breadcrumb-item active">Inspections</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('dams.show', $dam->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dam
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Dam Info Card -->
    <div class="card mb-4">
        <div class="card-body bg-light">
            <div class="row">
                <div class="col-md-3">
                    <strong>Location:</strong> {{ $dam->full_location }}
                </div>
                <div class="col-md-3">
                    <strong>Wall Condition:</strong>
                    <span class="badge bg-{{ $dam->condition_badge }}">
                        {{ ucfirst($dam->wall_condition) }}
                    </span>
                </div>
                <div class="col-md-3">
                    <strong>Water Level:</strong>
                    <span class="badge bg-info">
                        {{ $dam->water_level_status }} ({{ number_format($dam->water_level_percentage, 1) }}%)
                    </span>
                </div>
                <div class="col-md-3">
                    <strong>Last Maintenance:</strong>
                    @if($dam->maintenanceRecords->count() > 0)
                        {{ $dam->maintenanceRecords->first()->maintenance_date->format('M d, Y') }}
                    @else
                        <span class="text-muted">No records</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Inspections</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Planned</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['planned'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Cost</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ZMW {{ number_format($stats['total_cost'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspections Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Inspection Records</h6>
            <a href="{{ route('dams.maintenance.create', $dam->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Inspection
            </a>
        </div>
        <div class="card-body">
            @if($maintenanceRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Cost</th>
                                <th>Contractor</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maintenanceRecords as $record)
                                <tr>
                                    <td>{{ $record->maintenance_date->format('M d, Y') }}</td>
                                    <td>{{ $record->maintenance_type_text }}</td>
                                    <td>
                                        @php
                                            $description = '';
                                            if($record->wall_repair_details) {
                                                $description .= 'Wall repairs';
                                            }
                                            if($record->spillway_repair_details) {
                                                $description .= $description ? ', Spillway repairs' : 'Spillway repairs';
                                            }
                                            if(!$description) {
                                                $description = 'General inspection';
                                            }
                                        @endphp
                                        {{ $description }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $record->status_badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($record->actual_cost)
                                            <strong>ZMW {{ number_format($record->actual_cost, 2) }}</strong>
                                        @elseif($record->estimated_cost)
                                            <span class="text-muted">Est: ZMW {{ number_format($record->estimated_cost, 2) }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->contractor_name)
                                            {{ $record->contractor_name }}
                                        @else
                                            <span class="text-muted">Internal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('dams.maintenance.show', [$dam->id, $record->id]) }}" 
                                               class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('dams.maintenance.edit', [$dam->id, $record->id]) }}" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('dams.maintenance.destroy', [$dam->id, $record->id]) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Delete this inspection record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $maintenanceRecords->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h4>No inspection records found</h4>
                    <p class="text-muted">Start by adding your first inspection record.</p>
                    <a href="{{ route('dams.maintenance.create', $dam->id) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Inspection
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection