@extends('layouts.app')

@section('title', 'Maintenance Record')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">Dams</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dams.show', $dam) }}">{{ $dam->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dams.maintenance.index', $dam) }}">Maintenance</a></li>
    <li class="breadcrumb-item active">Record</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $maintenance->maintenance_type_text }}: {{ $dam->name }}</h2>
    <div>
        <a href="{{ route('dams.maintenance.index', $dam) }}" class="btn btn-outline-secondary">Back</a>
        <a href="{{ route('dams.maintenance.edit', [$dam, $maintenance]) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Edit</a>
    </div>
</div>
<div class="card"><div class="card-body"><div class="row">
    <div class="col-md-4"><strong>Date</strong><p>{{ $maintenance->maintenance_date->format('Y-m-d') }}</p></div>
    <div class="col-md-4"><strong>Status</strong><p>{{ str_replace('_', ' ', ucfirst($maintenance->status)) }}</p></div>
    <div class="col-md-4"><strong>Estimated / Actual Cost</strong><p>{{ $maintenance->estimated_cost ?? 'N/A' }} / {{ $maintenance->actual_cost ?? 'N/A' }}</p></div>
    <div class="col-md-6"><strong>Wall Repairs</strong><p>{{ $maintenance->wall_repair_details ?? 'N/A' }}</p></div>
    <div class="col-md-6"><strong>Spillway Repairs</strong><p>{{ $maintenance->spillway_repair_details ?? 'N/A' }}</p></div>
    <div class="col-md-6"><strong>Other Works</strong><p>{{ $maintenance->other_works ?? 'N/A' }}</p></div>
    <div class="col-md-6"><strong>Contractor</strong><p>{{ $maintenance->contractor_name ?? 'N/A' }} {{ $maintenance->contractor_contact }}</p></div>
    <div class="col-md-12"><strong>Comments</strong><p>{{ $maintenance->general_comments ?? 'N/A' }}</p></div>
</div></div></div>
<form class="mt-3" method="POST" action="{{ route('dams.maintenance.destroy', [$dam, $maintenance]) }}" onsubmit="return confirm('Delete this maintenance record?')">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Delete Record</button>
</form>
@endsection