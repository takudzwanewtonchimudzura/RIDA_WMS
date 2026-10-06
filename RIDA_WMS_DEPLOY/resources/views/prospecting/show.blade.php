@extends('layouts.app')

@section('title', $prospectingSite->reference)
@section('page-title', 'Prospecting')
@section('content')
<div class="container-fluid" style="max-width: 1100px">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><a href="{{ route('prospecting.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Prospecting sites</a><h1 class="h3 mt-2 mb-0">{{ $prospectingSite->reference }}</h1></div>
        <span class="badge text-bg-{{ ['submitted' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'drilled' => 'primary'][$prospectingSite->status] }} fs-6">{{ str_replace('_', ' ', ucfirst($prospectingSite->status)) }}</span>
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    <div class="border rounded p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-6"><div class="small text-muted">Project</div><div>{{ $prospectingSite->project_name ?: 'Unassigned project' }}</div></div>
            <div class="col-md-6"><div class="small text-muted">Survey date</div><div>{{ $prospectingSite->survey_date->format('d M Y') }}</div></div>
            <div class="col-md-3"><div class="small text-muted">Village</div><div>{{ $prospectingSite->village }}</div></div>
            <div class="col-md-3"><div class="small text-muted">Ward</div><div>{{ $prospectingSite->ward }}</div></div>
            <div class="col-md-3"><div class="small text-muted">District</div><div>{{ $prospectingSite->district }}</div></div>
            <div class="col-md-3"><div class="small text-muted">Province</div><div>{{ $prospectingSite->province }}</div></div>
            <div class="col-md-4"><div class="small text-muted">Siting agency</div><div>{{ $prospectingSite->siting_agency ?? 'N/A' }}</div></div>
            <div class="col-md-4"><div class="small text-muted">Prospector</div><div>{{ $prospectingSite->prospector_name ?? 'N/A' }}</div></div>
            <div class="col-md-4"><div class="small text-muted">Coordinates</div><div>{{ $prospectingSite->latitude ?? 'N/A' }}, {{ $prospectingSite->longitude ?? 'N/A' }}</div></div>
            <div class="col-12"><div class="small text-muted">Survey notes</div><div>{{ $prospectingSite->remarks ?: 'No notes recorded.' }}</div></div>
            <div class="col-12 small text-muted">Submitted by {{ $prospectingSite->creator->name }} · {{ $prospectingSite->created_at->format('d M Y H:i') }}</div>
        </div>
    </div>

    @if(auth()->user()->isAdmin() && $prospectingSite->status === 'submitted')
        <form method="POST" action="{{ route('prospecting.decide', $prospectingSite) }}" class="border rounded p-4 mb-3">
            @csrf @method('PATCH')
            <h2 class="h5">Review site</h2>
            <div class="mb-3"><label for="review_note" class="form-label">Review note</label><textarea id="review_note" name="review_note" rows="3" class="form-control"></textarea></div>
            <div class="d-flex gap-2"><button class="btn btn-success" name="status" value="approved">Approve</button><button class="btn btn-outline-danger" name="status" value="rejected">Reject</button></div>
        </form>
    @endif

    @if($prospectingSite->status === 'approved' && (auth()->user()->isAdmin() || $prospectingSite->created_by === auth()->id()))
        @if($prospectingSite->borehole)
            <a href="{{ route('boreholes.show', $prospectingSite->borehole) }}" class="btn btn-primary">Open borehole record</a>
        @else
            <a href="{{ route('boreholes.create', ['prospecting_site' => $prospectingSite->id]) }}" class="btn btn-primary">Start borehole record</a>
        @endif
        <form method="POST" action="{{ route('prospecting.mark-drilled', $prospectingSite) }}" onsubmit="return confirm('Mark this approved prospecting site as drilled?')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-primary">Mark as drilled</button>
        </form>
    @endif
</div>
@endsection