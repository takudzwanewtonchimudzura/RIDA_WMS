@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="h3 mb-0">Dam Water Levels</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">Dams</a></li>
                    <li class="breadcrumb-item active">Water Levels</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Dams</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_dams'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-water fa-2x text-gray-300"></i>
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
                                Full Dams (≥80%)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['full_dams'] }}</div>
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
                                Low Dams (<30%)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['low_dams'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Critical Condition</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['critical_dams'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dams Levels Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Dam Water Levels</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Dam Name</th>
                                    <th>Location</th>
                                    <th>Capacity (m³)</th>
                                    <th>Current Depth (m)</th>
                                    <th>Water Level</th>
                                    <th>Wall Condition</th>
                                    <th>Spillway Condition</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dams as $dam)
                                @php
                                    $waterLevelPercentage = 0;
                                    $waterLevelStatus = 'No Data';
                                    $waterLevelColor = 'secondary';
                                    
                                    if ($dam->capacity && $dam->water_depth) {
                                        $waterLevelPercentage = min(100, ($dam->water_depth / $dam->capacity) * 100);
                                        
                                        if ($waterLevelPercentage >= 80) {
                                            $waterLevelStatus = 'Full';
                                            $waterLevelColor = 'success';
                                        } elseif ($waterLevelPercentage >= 50) {
                                            $waterLevelStatus = 'Moderate';
                                            $waterLevelColor = 'info';
                                        } elseif ($waterLevelPercentage >= 20) {
                                            $waterLevelStatus = 'Low';
                                            $waterLevelColor = 'warning';
                                        } else {
                                            $waterLevelStatus = 'Critical';
                                            $waterLevelColor = 'danger';
                                        }
                                    }
                                    
                                    // Replace match() with switch or if-else for PHP < 8.0
                                    $wallBadgeColor = 'secondary';
                                    switch($dam->wall_condition) {
                                        case 'good':
                                            $wallBadgeColor = 'success';
                                            break;
                                        case 'fair':
                                            $wallBadgeColor = 'info';
                                            break;
                                        case 'poor':
                                            $wallBadgeColor = 'warning';
                                            break;
                                        case 'critical':
                                            $wallBadgeColor = 'danger';
                                            break;
                                    }
                                    
                                    $spillwayBadgeColor = 'secondary';
                                    switch($dam->spillway_condition) {
                                        case 'good':
                                            $spillwayBadgeColor = 'success';
                                            break;
                                        case 'fair':
                                            $spillwayBadgeColor = 'info';
                                            break;
                                        case 'poor':
                                            $spillwayBadgeColor = 'warning';
                                            break;
                                        case 'critical':
                                            $spillwayBadgeColor = 'danger';
                                            break;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $dam->name }}</strong><br>
                                        <small class="text-muted">{{ $dam->village }}</small>
                                    </td>
                                    <td>{{ $dam->district }}, {{ $dam->province }}</td>
                                    <td class="text-right">
                                        @if($dam->capacity)
                                            {{ number_format($dam->capacity, 2) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($dam->water_depth)
                                            {{ number_format($dam->water_depth, 2) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dam->capacity && $dam->water_depth)
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $waterLevelColor }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $waterLevelPercentage }}%"
                                                         aria-valuenow="{{ $waterLevelPercentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($waterLevelPercentage, 1) }}%
                                                    </div>
                                                </div>
                                                <span class="badge bg-{{ $waterLevelColor }}">
                                                    {{ $waterLevelStatus }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">No Data</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $wallBadgeColor }}">
                                            {{ ucfirst($dam->wall_condition) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $spillwayBadgeColor }}">
                                            {{ ucfirst($dam->spillway_condition) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dams.show', $dam->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('dams.maintenance.create', $dam->id) }}" 
                                           class="btn btn-sm btn-success" 
                                           title="Add Inspection">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <a href="{{ route('dams.maintenance.index', $dam->id) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="View Inspections">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
.progress {
    border-radius: 10px;
    background-color: #f0f0f0;
}
.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}
.table td, .table th {
    vertical-align: middle;
}
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 25,
            "order": [[0, "asc"]],
            "language": {
                "search": "Search dams:",
                "lengthMenu": "Show _MENU_ dams per page",
                "zeroRecords": "No dams found",
                "info": "Showing _START_ to _END_ of _TOTAL_ dams",
                "infoEmpty": "Showing 0 to 0 of 0 dams",
                "infoFiltered": "(filtered from _MAX_ total dams)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
    });
</script>
@endsection
@endsection