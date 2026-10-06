@extends('layouts.app')

@section('title', 'Prospecting Sites')
@section('page-title', 'Prospecting')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><p class="text-uppercase text-muted small fw-semibold mb-1">Pre-drilling workflow</p><h1 class="h3 mb-0">Prospecting sites</h1></div>
        @if(!auth()->user()->isViewer())<a href="{{ route('prospecting.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1" aria-hidden="true"></i>New survey</a>@endif
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    <form method="GET" action="{{ route('prospecting.index') }}" class="row g-3 align-items-end mb-3">
        <div class="col-md-6"><label for="search" class="form-label">Search sites</label><input id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Reference, project, village or district"></div>
        <div class="col-md-3"><label for="status" class="form-label">Status</label><select id="status" name="status" class="form-select"><option value="">All statuses</option>@foreach(['submitted', 'approved', 'rejected', 'drilled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>@endforeach</select></div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-outline-primary" type="submit"><i class="fas fa-filter me-1" aria-hidden="true"></i>Filter</button><a class="btn btn-outline-secondary" href="{{ route('prospecting.index') }}">Clear</a></div>
    </form>
    <div class="table-responsive border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Reference</th><th>Project / village</th><th>District</th><th>Province</th><th>Survey date</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($sites as $site)
                    <tr><td class="fw-semibold">{{ $site->reference }}</td><td>{{ $site->project_name ?: 'Unassigned project' }}<div class="small text-muted">{{ $site->village }}, Ward {{ $site->ward }}</div></td><td>{{ $site->district }}</td><td>{{ $site->province }}</td><td>{{ $site->survey_date->format('d M Y') }}</td><td><span class="badge text-bg-{{ ['submitted' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'drilled' => 'primary'][$site->status] }}">{{ str_replace('_', ' ', ucfirst($site->status)) }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('prospecting.show', $site) }}">Open</a></td></tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">No prospecting sites match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $sites->links() }}</div>
</div>
@endsection