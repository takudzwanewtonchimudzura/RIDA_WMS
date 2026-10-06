@extends('layouts.app')

@section('title', 'Irrigation Maintenance')
@section('page-title', 'Irrigation Schemes')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><p class="text-uppercase text-muted small fw-semibold mb-1">Asset maintenance</p><h1 class="h3 mb-0">Irrigation maintenance</h1></div>
        @if(!auth()->user()->isViewer())<a href="{{ route('irrigation-maintenances.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1" aria-hidden="true"></i>New record</a>@endif
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    <div class="table-responsive border rounded bg-white"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>Scheme</th><th>Village / Ward</th><th>Repair date</th><th>Repaired by</th><th>Work performed</th><th></th></tr></thead>
        <tbody>@forelse($maintenances as $maintenance)
            <tr><td class="fw-semibold">{{ $maintenance->scheme_name }}</td><td>{{ $maintenance->village }}, Ward {{ $maintenance->ward }}</td><td>{{ $maintenance->date_repaired?->format('d M Y') ?: '—' }}</td><td>{{ $maintenance->repaired_by }}</td><td>{{ \Illuminate\Support\Str::limit($maintenance->works_done, 90) }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('irrigation-maintenances.show', $maintenance) }}">Open</a></td></tr>
        @empty<tr><td colspan="6" class="text-center py-5 text-muted">No maintenance records found.</td></tr>@endforelse</tbody>
    </table></div>
    <div class="mt-3">{{ $maintenances->links() }}</div>
</div>
@endsection