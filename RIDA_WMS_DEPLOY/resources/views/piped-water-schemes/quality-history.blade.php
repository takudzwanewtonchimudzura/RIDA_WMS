@extends('layouts.app')

@section('title', 'Water Quality History')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>{{ $pipedWaterScheme->name }}: Water Quality History</h1>
            <p class="text-muted mb-0">{{ $pipedWaterScheme->village }} / {{ $pipedWaterScheme->ward }}</p>
        </div>
        <a href="{{ route('piped-water-schemes.quality') }}" class="btn btn-outline-secondary">Back to quality monitoring</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Date</th><th>pH</th><th>Turbidity</th><th>E.coli</th><th>TDS</th><th>EC</th><th>Result</th><th>Tested by</th><th>Notes</th></tr></thead>
                <tbody>
                    @forelse($tests as $test)
                    <tr>
                        <td>{{ $test->tested_at->format('d M Y') }}</td>
                        <td>{{ $test->ph ?? 'N/A' }}</td>
                        <td>{{ $test->turbidity_ntu !== null ? $test->turbidity_ntu . ' NTU' : 'N/A' }}</td>
                        <td>{{ $test->ecoli_cfu_100ml ?? 'N/A' }}</td>
                        <td>{{ $test->tds_mg_l !== null ? $test->tds_mg_l . ' mg/L' : 'N/A' }}</td>
                        <td>{{ $test->electrical_conductivity ?? 'N/A' }}</td>
                        <td><span class="badge bg-{{ $test->overall_quality === 'Meets Standards' ? 'success' : 'warning' }}">{{ $test->overall_quality }}</span></td>
                        <td>{{ $test->tester->name ?? 'N/A' }}</td>
                        <td>{{ $test->notes ?: 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No tests recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $tests->links() }}</div>
    </div>
</div>
@endsection
