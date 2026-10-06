@extends('layouts.app')

@section('title', 'Water Point Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('water-points.index') }}">Water Points</a></li>
    <li class="breadcrumb-item active">{{ $waterPoint->name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>{{ $waterPoint->name }}</h2>
    <div>
        <a href="{{ route('water-points.edit', $waterPoint->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <a href="{{ route('water-points.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to list
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white"><i class="fas fa-info-circle me-2"></i>Basic Information</div>
    <div class="card-body row">
        <div class="col-md-3 mb-3"><strong>Village:</strong><br>{{ $waterPoint->village_name ?? 'N/A' }}</div>
        <div class="col-md-3 mb-3"><strong>Ward:</strong><br>{{ $waterPoint->ward ?? 'N/A' }}</div>
        <div class="col-md-3 mb-3"><strong>Latitude:</strong><br>{{ $waterPoint->latitude ?? 'N/A' }}</div>
        <div class="col-md-3 mb-3"><strong>Longitude:</strong><br>{{ $waterPoint->longitude ?? 'N/A' }}</div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-info text-white"><i class="fas fa-cogs me-2"></i>Technical Details</div>
    <div class="card-body row">
        <div class="col-md-3 mb-3"><strong>Water Source Type:</strong><br>{{ $waterPoint->type_of_water_source ?? 'N/A' }}</div>
        <div class="col-md-3 mb-3">
            <strong>Status:</strong><br>
            @php $status = $waterPoint->functionality ?? 'Unknown'; @endphp
            <span class="badge bg-{{ $status == 'Functional' ? 'success' : ($status == 'Partially Functional' ? 'warning' : 'danger') }}">{{ $status }}</span>
        </div>
        <div class="col-md-3 mb-3"><strong>Year Drilled:</strong><br>{{ $waterPoint->year_drilled ?? 'N/A' }}</div>
        <div class="col-md-3 mb-3"><strong>Seasonality:</strong><br>{{ $waterPoint->seasonality ?? 'N/A' }}</div>
        <div class="col-md-3 mb-3"><strong>Lifting Device:</strong><br>{{ $waterPoint->type_of_lifting_device ?? 'N/A' }}</div>
        <div class="col-md-3 mb-3"><strong>Number of Pipes:</strong><br>{{ $waterPoint->number_of_pipes ?? 'N/A' }}</div>
        <div class="col-md-3 mb-3"><strong>Strokes Before Water:</strong><br>{{ $waterPoint->strokes_before_water ?? 'N/A' }}</div>
        <div class="col-md-3 mb-3"><strong>Strokes to Fill 20L:</strong><br>{{ $waterPoint->strokes_to_fill_20l ?? 'N/A' }}</div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-secondary text-white"><i class="fas fa-tools me-2"></i>Infrastructure Condition</div>
    <div class="card-body row">
        <div class="col-md-4 mb-3"><strong>Apron Condition:</strong><br>{{ ucfirst($waterPoint->apron_condition ?? 'N/A') }}</div>
        <div class="col-md-4 mb-3"><strong>Runoff Channel:</strong><br>{{ ucfirst($waterPoint->runoff_channel_condition ?? 'N/A') }}</div>
        <div class="col-md-4 mb-3"><strong>Soakaway:</strong><br>{{ ucfirst($waterPoint->soakaway_condition ?? 'N/A') }}</div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-success text-white"><i class="fas fa-users me-2"></i>Service Information</div>
    <div class="card-body row">
        <div class="col-md-2 mb-3"><strong>Population:</strong><br>{{ number_format($waterPoint->population_served ?? 0) }}</div>
        <div class="col-md-2 mb-3"><strong>Households:</strong><br>{{ number_format($waterPoint->households_served ?? 0) }}</div>
        <div class="col-md-2 mb-3"><strong>Women:</strong><br>{{ number_format($waterPoint->women_served ?? 0) }}</div>
        <div class="col-md-2 mb-3"><strong>Men:</strong><br>{{ number_format($waterPoint->men_served ?? 0) }}</div>
        <div class="col-md-2 mb-3"><strong>Children &lt;18:</strong><br>{{ number_format($waterPoint->children_below_18 ?? 0) }}</div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-dark text-white"><i class="fas fa-people-group me-2"></i>Water Committee</div>
    <div class="card-body row">
        <div class="col-md-2 mb-3"><strong>Committee:</strong><br>{{ $waterPoint->has_water_committee ? 'Yes' : 'No' }}</div>
        <div class="col-md-2 mb-3"><strong>Functional:</strong><br>{{ $waterPoint->committee_functional ? 'Yes' : 'No' }}</div>
        <div class="col-md-2 mb-3"><strong>Total Members:</strong><br>{{ $waterPoint->committee_total_members ?? 'N/A' }}</div>
        <div class="col-md-2 mb-3"><strong>Women:</strong><br>{{ $waterPoint->committee_women ?? 'N/A' }}</div>
        <div class="col-md-2 mb-3"><strong>Men:</strong><br>{{ $waterPoint->committee_men ?? 'N/A' }}</div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-warning text-dark"><i class="fas fa-wrench me-2"></i>Maintenance History</div>
    <div class="card-body row">
        <div class="col-md-4 mb-3"><strong>Last Repaired By:</strong><br>{{ $waterPoint->last_repaired_by ?? 'N/A' }}</div>
        <div class="col-md-4 mb-3"><strong>Last Spares Fitted:</strong><br>{{ $waterPoint->last_spares_fitted ?? 'N/A' }}</div>
        <div class="col-md-4 mb-3"><strong>Last Repair Date:</strong><br>{{ $waterPoint->last_repair_date?->format('d M Y') ?? 'N/A' }}</div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <small class="text-muted"><i class="fas fa-calendar-plus me-1"></i>Created: {{ $waterPoint->created_at->format('d M Y, H:i') }}</small>
    </div>
    <div class="col-md-6 text-md-end">
        <small class="text-muted"><i class="fas fa-calendar-check me-1"></i>Last Updated: {{ $waterPoint->updated_at->format('d M Y, H:i') }}</small>
    </div>
</div>
@endsection
