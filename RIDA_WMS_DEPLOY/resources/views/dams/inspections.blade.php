@extends('layouts.app')

@section('title', 'Dam Inspections')
@section('page-title', 'Dams')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><p class="text-uppercase text-muted small fw-semibold mb-1">Maintenance and condition</p><h1 class="h3 mb-0">Dam inspections</h1></div>
        <a href="{{ route('dams.index') }}" class="btn btn-outline-secondary">All dams</a>
    </div>
    <div class="table-responsive border rounded bg-white">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Dam</th><th>Location</th><th>Wall</th><th>Spillway</th><th>Inspections</th><th></th></tr></thead>
            <tbody>
                @forelse($dams as $dam)
                    <tr>
                        <td class="fw-semibold">{{ $dam->name }}</td>
                        <td>{{ collect([$dam->village, $dam->district, $dam->province])->filter()->join(', ') }}</td>
                        <td><span class="badge text-bg-{{ in_array($dam->wall_condition, ['poor', 'critical'], true) ? 'danger' : 'success' }}">{{ ucfirst($dam->wall_condition) }}</span></td>
                        <td><span class="badge text-bg-{{ in_array($dam->spillway_condition, ['poor', 'critical'], true) ? 'danger' : 'success' }}">{{ ucfirst($dam->spillway_condition) }}</span></td>
                        <td>{{ $dam->maintenance_records_count }}</td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('dams.maintenance.index', $dam) }}">View inspections</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No dams have been recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection