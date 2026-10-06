@extends('layouts.app')

@section('title', 'Boreholes List')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Filter Boreholes</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('boreholes.index') }}">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Serial no, location..." class="form-control">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Province</label>
                    <select name="province" class="form-select">
                        <option value="">All Provinces</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" {{ request('province') == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                
                <div>
                    <a href="{{ route('boreholes.export.excel', request()->query()) }}" 
                       class="btn btn-success me-2">Export Excel</a>
                    <a href="{{ route('boreholes.create') }}" class="btn btn-primary">New Borehole</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Boreholes List</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Serial No</th>
                        <th>Location</th>
                        <th>Depth (m)</th>
                        <th>Blowing Yield (l/s)</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boreholes as $borehole)
                    <tr>
                        <td>{{ $borehole->borehole_serial_no }}</td>
                        <td>{{ $borehole->locality }}, {{ $borehole->admin_district }}</td>
                        <td>
                            @if($borehole->final_depth_m)
                                {{ number_format($borehole->final_depth_m, 2) }} m
                            @elseif($borehole->final_cased_depth)
                                {{ number_format($borehole->final_cased_depth, 2) }} m
                            @elseif($borehole->rdd_depth)
                                {{ number_format($borehole->rdd_depth, 2) }} m
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($borehole->blowing_yield)
                                {{ number_format($borehole->blowing_yield, 2) }} l/s
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $borehole->date->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('boreholes.show', $borehole) }}" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('boreholes.edit', $borehole) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('boreholes.pdf.download', $borehole) }}" class="btn btn-outline-info" title="Download PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <form action="{{ route('boreholes.destroy', $borehole->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this borehole?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Delete"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $boreholes->links() }}
    </div>
</div>

@if($boreholes->isEmpty())
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle me-2"></i>No boreholes found matching your criteria.
</div>
@endif

<style>
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
.text-muted {
    color: #6c757d !important;
    font-style: italic;
}
</style>
@endsection