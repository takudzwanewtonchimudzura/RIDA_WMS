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
        @if($waterPoints->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Village</th>
                        <th>Ward</th>
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
                            @elseif($status == 'Nonfunctional')
                                <span class="badge bg-danger">Nonfunctional</span>
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