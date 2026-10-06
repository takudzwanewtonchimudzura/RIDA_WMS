{{-- resources/views/piped-water-schemes/networks.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Scheme Networks</h1>
            <p class="lead">Network infrastructure overview</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('piped-water-schemes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Schemes
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                        <h3 class="text-primary">{{ number_format($schemes->sum('network_length')) }} m</h3>
                        <p class="mb-0">Total Network Length</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="p-3 bg-success bg-opacity-10 rounded">
                        <h3 class="text-success">{{ $schemes->count() }}</h3>
                        <p class="mb-0">Total Schemes</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="p-3 bg-info bg-opacity-10 rounded">
                        <h3 class="text-info">{{ $schemes->sum('stand_pipes_count') }}</h3>
                        <p class="mb-0">Total Stand Pipes</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    @php
                        $functional = $schemes->where('functionality', 'Functional')->count();
                        $percentage = $schemes->count() > 0 ? round(($functional / $schemes->count()) * 100) : 0;
                    @endphp
                    <div class="p-3 {{ $percentage >= 80 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }} bg-opacity-10 rounded">
                        <h3 class="{{ $percentage >= 80 ? 'text-success' : ($percentage >= 50 ? 'text-warning' : 'text-danger') }}">{{ $percentage }}%</h3>
                        <p class="mb-0">Functional Schemes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <form method="GET" action="{{ route('piped-water-schemes.networks') }}">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Scheme Name or Village" value="{{ request()->get('search') }}">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Scheme Name</th>
                            <th>Village</th>
                            <th>Ward</th>
                            <th>Network Length (m)</th>
                            <th>Stand Pipes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schemes as $scheme)
                        <tr>
                            <td>{{ $scheme->name }}</td>
                            <td>{{ $scheme->village }}</td>
                            <td>{{ $scheme->ward }}</td>
                            <td class="text-end">{{ number_format($scheme->network_length) }}</td>
                            <td class="text-end">{{ $scheme->stand_pipes_count }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $scheme->functionality == 'Functional' ? 'success' : 
                                    ($scheme->functionality == 'Partially Functional' ? 'warning' : 'danger') 
                                }}">
                                    {{ $scheme->functionality }}
                                </span>
                            </td>
                            <td>
                                <!-- <a href="{{ route('piped-water-schemes.edit', $scheme) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a> -->
                                <a href="{{ route('piped-water-schemes.edit', $scheme->id) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection