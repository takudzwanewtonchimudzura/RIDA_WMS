@extends('layouts.app')

@section('title', 'Irrigation Schemes')
@section('breadcrumb')
    <li class="breadcrumb-item active">Irrigation Schemes</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tint me-2"></i>Irrigation Schemes Management</h2>
    <a href="{{ route('irrigation-schemes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Scheme
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($schemes->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Scheme Name</th>
                        <th>Village</th>
                        <th>Ward</th>
                        <th>Irrigation Type</th>
                        <th>Size (Ha)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schemes as $scheme)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $scheme->name ?? 'N/A' }}</td>
                        <td>{{ $scheme->village ?? 'N/A' }}</td>
                        <td>{{ $scheme->ward ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $scheme->irrigation_type ?? 'Unknown' }}</span>
                        </td>
                        <td>{{ $scheme->size_hectares ?? 0 }} Ha</td>
                        <td>
                            @php
                                $status = $scheme->functionality ?? 'Unknown';
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
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('irrigation-schemes.show', $scheme->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('irrigation-schemes.edit', $scheme->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('irrigation-schemes.destroy', $scheme->id) }}" method="POST" 
                                      style="display:inline" data-confirm="Are you sure you want to delete this irrigation scheme?">
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
            {{ $schemes->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-tint fa-3x text-muted mb-3"></i>
            <h4>No irrigation schemes found</h4>
            <p class="text-muted">Start by adding your first irrigation scheme</p>
            <a href="{{ route('irrigation-schemes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Scheme
            </a>
        </div>
        @endif
    </div>
</div>
@endsection