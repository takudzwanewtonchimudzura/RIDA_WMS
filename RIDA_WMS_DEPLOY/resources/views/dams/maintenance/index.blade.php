@extends('layouts.app')

@section('title', 'Dam Maintenance')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">Dams</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dams.show', $dam) }}">{{ $dam->name }}</a></li>
    <li class="breadcrumb-item active">Maintenance</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Maintenance: {{ $dam->name }}</h2>
    <a href="{{ route('dams.maintenance.create', $dam) }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Record</a>
</div>
<div class="row mb-3">
    <div class="col-md-3"><strong>Total Records:</strong> {{ $stats['total'] }}</div>
    <div class="col-md-3"><strong>Completed:</strong> {{ $stats['completed'] }}</div>
    <div class="col-md-3"><strong>Planned:</strong> {{ $stats['planned'] }}</div>
    <div class="col-md-3"><strong>Total Cost:</strong> {{ number_format($stats['total_cost'], 2) }}</div>
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Date</th><th>Type</th><th>Status</th><th>Actual Cost</th><th>Actions</th></tr></thead>
    <tbody>
        @forelse($maintenanceRecords as $maintenance)
            <tr>
                <td>{{ $maintenance->maintenance_date->format('Y-m-d') }}</td>
                <td>{{ $maintenance->maintenance_type_text }}</td>
                <td><span class="badge bg-{{ $maintenance->status_badge }}">{{ str_replace('_', ' ', ucfirst($maintenance->status)) }}</span></td>
                <td>{{ $maintenance->actual_cost !== null ? number_format($maintenance->actual_cost, 2) : 'N/A' }}</td>
                <td>
                    <a href="{{ route('dams.maintenance.show', [$dam, $maintenance]) }}" class="btn btn-sm btn-info" aria-label="View record"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('dams.maintenance.edit', [$dam, $maintenance]) }}" class="btn btn-sm btn-warning" aria-label="Edit record"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center py-4">No maintenance records found.</td></tr>
        @endforelse
    </tbody>
</table></div><div class="card-body">{{ $maintenanceRecords->links() }}</div></div>
@endsection