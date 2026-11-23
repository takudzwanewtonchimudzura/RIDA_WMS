@extends('layouts.app')

@section('title', 'Water Points')
@section('breadcrumb')
    <li class="breadcrumb-item active">Water Points</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-map-marker-alt me-2"></i>Water Points Management</h2>
    <a href="{{ route('water-points.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Water Point
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><label for="search" class="visually-hidden">Search</label><input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, village or borehole code"></div>
            <div class="col-md-2"><label for="filter-province" class="visually-hidden">Province</label><select id="filter-province" name="province" class="form-select"><option value="">All provinces</option>@foreach(array_keys(app(\App\Services\ZimbabweLocations::class)->hierarchy()) as $province)<option @selected(request('province') === $province)>{{ $province }}</option>@endforeach</select></div>
            <div class="col-md-2"><label for="filter-district" class="visually-hidden">District</label><input id="filter-district" name="district" class="form-control" value="{{ request('district') }}" placeholder="District"></div>
            <div class="col-md-2"><label for="filter-status" class="visually-hidden">Status</label><select id="filter-status" name="functionality" class="form-select"><option value="">All statuses</option>@foreach(\App\Models\InfrastructureStatus::VALUES as $status)<option @selected(request('functionality') === $status)>{{ $status }}</option>@endforeach</select></div>
            <div class="col-md-2"><label for="filter-type" class="visually-hidden">Source type</label><select id="filter-type" name="type_of_water_source" class="form-select"><option value="">All sources</option>@foreach(['Borehole','Well','Spring','River'] as $type)<option @selected(request('type_of_water_source') === $type)>{{ $type }}</option>@endforeach</select></div>
            <div class="col-md-1"><button class="btn btn-primary">Filter</button></div>
        </form>
        <a class="btn btn-outline-primary mb-3" href="{{ route('water-points.index', array_merge(request()->except('page'), ['export' => 'csv'])) }}">Export filtered CSV</a>
        <p class="text-muted small">Borehole water points link to existing assets; do not add these rows to the borehole asset total.</p>
        @if($waterPoints->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Village</th>
                        <th>Ward</th>
                        <th>Province / District</th>
                        <th>GPS</th>
                        <th>Linked Borehole</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Population</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($waterPoints as $waterPoint)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $waterPoint->name ?? 'N/A' }}</td>
                        <td>{{ $waterPoint->village_name ?? 'N/A' }}</td>
                        <td>{{ $waterPoint->ward ?? 'N/A' }}</td>
                        <td>{{ $waterPoint->province ?? 'N/A' }} / {{ $waterPoint->district ?? 'N/A' }}</td>
                        <td>{{ $waterPoint->latitude ?? 'N/A' }}, {{ $waterPoint->longitude ?? 'N/A' }}</td>
                        <td>@if($waterPoint->borehole)<a href="{{ route('boreholes.show', $waterPoint->borehole) }}">{{ $waterPoint->borehole->borehole_serial_no }}</a>@else — @endif</td>
                        <td>
                            <span class="badge bg-info">{{ $waterPoint->type_of_water_source ?? 'Unknown' }}</span>
                        </td>
                        <td>
                            @php
                                $status = $waterPoint->functionality ?? 'Unknown';
                            @endphp
                            @if($status == 'Functional')
                                <span class="badge bg-success">Functional</span>
                            @elseif($status == 'Partially Functional')
                                <span class="badge bg-warning">Partially Functional</span>
                            @elseif($status == 'Non Functional')
                                <span class="badge bg-danger">Non Functional</span>
                            @else
                                <span class="badge bg-secondary">Unknown</span>
                            @endif
                        </td>
                        <td>{{ number_format($waterPoint->population_served ?? 0) }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('water-points.show', $waterPoint->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('water-points.edit', $waterPoint->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('water-points.destroy', $waterPoint->id) }}" method="POST" 
                                      style="display:inline" data-confirm="Are you sure you want to delete this water point?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
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
        
        <div class="d-flex justify-content-center mt-3">
            {{ $waterPoints->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
            <h4>No water points found</h4>
            <p class="text-muted">Start by adding your first water point</p>
            <a href="{{ route('water-points.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Water Point
            </a>
        </div>
        @endif
    </div>
</div>
@endsection