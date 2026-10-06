@extends('layouts.app')

@section('title', 'Scheme Maintenance')
@section('breadcrumb')
    <li class="breadcrumb-item active">Scheme Maintenance</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-wrench me-2"></i>Scheme Maintenance Records</h2>
    @if(auth()->user()->isAdmin() || auth()->user()->isOperator())<a href="{{ route('scheme-maintenances.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Maintenance Record
    </a>@endif
</div>

<div class="card">
    <div class="card-body">
        @if($maintenances->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Scheme Name</th>
                        <th>Village</th>
                        <th>Ward</th>
                        <th>Repaired By</th>
                        <th>Date Repaired</th>
                        <th>Works Done</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maintenances as $maintenance)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $maintenance->scheme_name ?? 'N/A' }}</td>
                        <td>{{ $maintenance->village ?? 'N/A' }}</td>
                        <td>{{ $maintenance->ward ?? 'N/A' }}</td>
                        <td>{{ $maintenance->repaired_by ?? 'Unknown' }}</td>
                        <td>{{ $maintenance->date_repaired ? \Carbon\Carbon::parse($maintenance->date_repaired)->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            @if($maintenance->works_done)
                                {{ Str::limit($maintenance->works_done, 50) }}
                            @else
                                <span class="text-muted">No details</span>
                            @endif
                        </td>
                        <td>
                            @if(auth()->user()->isAdmin() || (auth()->user()->isOperator() && (int) $maintenance->created_by === (int) auth()->id()))<div class="btn-group">
                                <a href="{{ route('scheme-maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('scheme-maintenances.edit', $maintenance->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('scheme-maintenances.destroy', $maintenance->id) }}" method="POST" 
                                      style="display:inline" data-confirm="Are you sure you want to delete this maintenance record?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $maintenances->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
            <h4>No maintenance records found</h4>
            <p class="text-muted">Start by adding your first maintenance record</p>
            @if(auth()->user()->isAdmin() || auth()->user()->isOperator())<a href="{{ route('scheme-maintenances.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Record
            </a>@endif
        </div>
        @endif
    </div>
</div>
@endsection