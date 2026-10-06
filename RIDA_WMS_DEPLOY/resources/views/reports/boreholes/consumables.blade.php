@extends('layouts.app')

@section('title', 'Consumables Performance')
@section('page-title', 'Consumables Performance')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <p class="text-uppercase text-muted small fw-semibold mb-1">Operational reporting</p>
            <h1 class="h3 mb-0">Bit and hammer utilization</h1>
        </div>
        <a href="{{ route('reports.operational.performance') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Operational reports
        </a>
    </div>

    <form method="GET" action="{{ route('reports.consumables.performance') }}" class="row g-3 align-items-end mb-4">
        <div class="col-sm-6 col-lg-3">
            <label for="project_name" class="form-label">Project</label>
            <input id="project_name" name="project_name" value="{{ $filters['project_name'] ?? '' }}" class="form-control">
        </div>
        <div class="col-sm-6 col-lg-3">
            <label for="date_from" class="form-label">From date</label>
            <input id="date_from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
        </div>
        <div class="col-sm-6 col-lg-3">
            <label for="date_to" class="form-label">To date</label>
            <input id="date_to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
        </div>
        <div class="col-sm-6 col-lg-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1" aria-hidden="true"></i>Apply</button>
            <a href="{{ route('reports.consumables.performance') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="small text-muted">Boreholes in scope</div><div class="h3 mb-0">{{ number_format($totalBoreholes) }}</div></div></div>
        <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="small text-muted">Drilling bits recorded</div><div class="h3 mb-0">{{ number_format($bitSummary->sum('units_used')) }}</div></div></div>
        <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="small text-muted">Hammer rings recorded</div><div class="h3 mb-0">{{ number_format($hammerSummary->sum('units_used')) }}</div></div></div>
    </div>

    <section class="mb-4" aria-labelledby="bits-heading">
        <div class="d-flex align-items-baseline justify-content-between mb-2">
            <h2 id="bits-heading" class="h5 mb-0">Drilling bits by serial number</h2>
            <span class="small text-muted">{{ $bitSummary->count() }} serial/project groups</span>
        </div>
        <div class="table-responsive border rounded">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th scope="col">Bit serial number</th><th scope="col">Project</th><th scope="col" class="text-end">Boreholes</th><th scope="col" class="text-end">Bits used</th></tr></thead>
                <tbody>
                    @forelse($bitSummary as $row)
                        <tr><td class="fw-semibold">{{ $row->serial_no }}</td><td>{{ $row->project_name ?: 'Unassigned project' }}</td><td class="text-end">{{ number_format($row->borehole_count) }}</td><td class="text-end">{{ number_format($row->units_used) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No bit serial usage matches these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section aria-labelledby="hammers-heading">
        <div class="d-flex align-items-baseline justify-content-between mb-2">
            <h2 id="hammers-heading" class="h5 mb-0">Hammer rings by serial number</h2>
            <span class="small text-muted">{{ $hammerSummary->count() }} serial/project groups</span>
        </div>
        <div class="table-responsive border rounded">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th scope="col">Hammer serial number</th><th scope="col">Project</th><th scope="col" class="text-end">Boreholes</th><th scope="col" class="text-end">Rings used</th></tr></thead>
                <tbody>
                    @forelse($hammerSummary as $row)
                        <tr><td class="fw-semibold">{{ $row->serial_no }}</td><td>{{ $row->project_name ?: 'Unassigned project' }}</td><td class="text-end">{{ number_format($row->borehole_count) }}</td><td class="text-end">{{ number_format($row->units_used) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No hammer serial usage matches these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection