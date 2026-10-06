{{-- resources/views/piped-water-schemes/water-quality.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Water Quality Monitoring</h1>
            <p class="lead">Water source quality information</p>
        </div>
        <div class="col-md-4 text-end">
            <span class="text-muted">Choose a scheme below to record a test</span>
            <a href="{{ route('piped-water-schemes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Schemes
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('piped-water-schemes.quality') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by Scheme Name or Village" value="{{ request()->get('search') }}">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Scheme Name</th>
                            <th>Village</th>
                            <th>Water Source</th>
                            <th>Status</th>
                            <th>Last Test Date</th>
                            <th>pH Level</th>
                            <th>Turbidity (NTU)</th>
                            <th>E.coli</th>
                            <th>Overall Quality</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schemes as $scheme)
                        <tr>
                            <td>{{ $scheme->name }}</td>
                            <td>{{ $scheme->village }}</td>
                            <td>{{ $scheme->water_source }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $scheme->functionality == 'Functional' ? 'success' : 
                                    ($scheme->functionality == 'Partially Functional' ? 'warning' : 'danger') 
                                }}">
                                    {{ $scheme->functionality }}
                                </span>
                            </td>
                            <td>{{ $scheme->latestQualityTest?->tested_at?->format('d M Y') ?? 'Not tested' }}</td>
                            <td>{{ $scheme->latestQualityTest?->ph ?? 'N/A' }}</td>
                            <td>{{ $scheme->latestQualityTest?->turbidity_ntu ?? 'N/A' }}</td>
                            <td>{{ $scheme->latestQualityTest?->ecoli_cfu_100ml ?? 'N/A' }}</td>
                            <td><span class="badge bg-{{ ($scheme->latestQualityTest?->overall_quality ?? 'Not Tested') === 'Meets Standards' ? 'success' : (($scheme->latestQualityTest?->overall_quality ?? 'Not Tested') === 'Needs Attention' ? 'warning' : 'secondary') }}">{{ $scheme->latestQualityTest?->overall_quality ?? 'Not Tested' }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#testModal{{ $scheme->id }}">
                                    <i class="bi bi-droplet"></i> Test
                                </button>
                                <a class="btn btn-sm btn-outline-info" href="{{ route('piped-water-schemes.quality-tests.history', $scheme) }}">
                                    <i class="bi bi-clock-history"></i> History
                                </a>
                            </td>
                        </tr>
                        <div class="modal fade" id="testModal{{ $scheme->id }}" tabindex="-1">
                            <div class="modal-dialog"><div class="modal-content">
                                <form method="POST" action="{{ route('piped-water-schemes.quality-tests.store', $scheme) }}">
                                    @csrf
                                    <div class="modal-header"><h5 class="modal-title">Add test: {{ $scheme->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6"><label class="form-label">Test date</label><input type="date" name="tested_at" class="form-control" value="{{ now()->toDateString() }}" required></div>
                                            <div class="col-md-6"><label class="form-label">pH</label><input type="number" name="ph" class="form-control" min="0" max="14" step="0.01"></div>
                                            <div class="col-md-6"><label class="form-label">Turbidity (NTU)</label><input type="number" name="turbidity_ntu" class="form-control" min="0" step="0.01"></div>
                                            <div class="col-md-6"><label class="form-label">E.coli (CFU/100ml)</label><input type="number" name="ecoli_cfu_100ml" class="form-control" min="0"></div>
                                            <div class="col-md-6"><label class="form-label">TDS (mg/L)</label><input type="number" name="tds_mg_l" class="form-control" min="0" step="0.01"></div>
                                            <div class="col-md-6"><label class="form-label">Electrical conductivity</label><input type="number" name="electrical_conductivity" class="form-control" min="0" step="0.01"></div>
                                            <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save test</button></div>
                                </form>
                            </div></div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection