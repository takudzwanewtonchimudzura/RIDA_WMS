@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="h3 mb-0">All Dams</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Dams</li>
                </ol>
            </nav>
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Dams</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-water fa-2x text-gray-300"></i>
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
                                Need Attention</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['needs_attention'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                By Province</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['by_province']->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
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
                                Quick Actions</div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('dams.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Add Dam
                                </a>
                                <a href="{{ route('dams.levels') }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-chart-line"></i> Water Levels
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dams Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Dams List</h6>
            <div>
                <a href="{{ route('dams.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Dam
                </a>
                <a href="{{ route('dams.export') }}?format=csv" class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>

<!-- Simple Search Input -->
<div class="row mb-4 ps-3 pe-3">
    <div class="col-md-12">
        <form action="{{ route('dams.index') }}" method="GET" class="search-form">
            <div class="input-group input-group-lg shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" 
                       name="q" 
                       class="form-control border-start-0 ps-0" 
                       placeholder="Search dams by name, village, ward, or district..."
                       value="{{ request('q') }}"
                       autocomplete="off">
                @if(request()->has('q'))
                <a href="{{ route('dams.index') }}" class="input-group-text bg-white border-start-0 text-danger" 
                   title="Clear search">
                    <i class="fas fa-times"></i>
                </a>
                @endif
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-search me-2"></i>Search
                </button>
            </div>
        </form>
    </div>
</div>


        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Capacity (m³)</th>
                            <th>Water Depth (m)</th>
                            <th>Condition</th>
                            <th>Maintenance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dams as $dam)
                        <tr>
                            <td>{{ $dam->id }}</td>
                            <td>
                                <strong>{{ $dam->name }}</strong><br>
                                <small class="text-muted">{{ $dam->village }}</small>
                            </td>
                            <td>
                                {{ $dam->district }}, {{ $dam->province }}<br>
                                <small class="text-muted">Ward: {{ $dam->ward }}</small>
                                @if($dam->latitude && $dam->longitude)
                                    <br><small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> 
                                        {{ number_format($dam->latitude, 4) }}, {{ number_format($dam->longitude, 4) }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($dam->capacity)
                                    {{ number_format($dam->capacity, 2) }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($dam->water_depth)
                                    {{ number_format($dam->water_depth, 2) }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                            <td>
                                @if($dam->wall_condition)
                                    @php
                                        $badgeColor = 'secondary';
                                        if ($dam->wall_condition === 'good') {
                                            $badgeColor = 'success';
                                        } elseif ($dam->wall_condition === 'fair') {
                                            $badgeColor = 'warning';
                                        } elseif ($dam->wall_condition === 'poor') {
                                            $badgeColor = 'danger';
                                        } elseif ($dam->wall_condition === 'critical') {
                                            $badgeColor = 'dark';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}">
                                        {{ ucfirst($dam->wall_condition) }}
                                    </span>
                                @endif
                                
                                @if($dam->needs_attention)
                                    <br><small class="text-danger">
                                        <i class="fas fa-exclamation-circle"></i> Needs attention
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $dam->maintenance_records_count }} records
                                </span>
                                <br>
                                <a href="{{ route('dams.maintenance.index', $dam->id) }}" 
                                   class="btn btn-sm btn-outline-info mt-1">
                                    <i class="fas fa-clipboard-check"></i> View
                                </a>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('dams.show', $dam->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dams.edit', $dam->id) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('dams.destroy', $dam->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this dam?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Delete"
                                                {{ $dam->maintenance_records_count > 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-water fa-3x mb-3"></i>
                                    <h4>No dams found</h4>
                                    <p>Start by adding your first dam</p>
                                    <a href="{{ route('dams.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Dam
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($dams->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $dams->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Provinces Distribution -->
    @if($stats['by_province']->count() > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dams by Province</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($stats['by_province'] as $province)
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $province->province }}</h6>
                                    <h3 class="text-primary">{{ $province->count }}</h3>
                                    <a href="{{ route('dams.index') }}?province={{ urlencode($province->province) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        View Dams
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .badge {
        font-size: 0.8em;
        padding: 0.4em 0.8em;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    .card {
        border-radius: 0.35rem;
    }
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable if needed
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }
        
        $('#dataTable').DataTable({
            "pageLength": 20,
            "order": [[0, "desc"]],
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

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
</script>
@endsection