{{-- resources/views/piped-water-schemes/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Piped Water Schemes</h1>
            <p class="lead">List of all piped water schemes</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('piped-water-schemes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Scheme
            </a>
            <a href="{{ route('piped-water-schemes.networks') }}" class="btn btn-info">
                <i class="bi bi-diagram-3"></i> View Networks
            </a>
            <a href="{{ route('piped-water-schemes.quality') }}" class="btn btn-success">
                <i class="bi bi-droplet"></i> Water Quality
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('piped-water-schemes.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, village, ward..." value="{{ request('search') }}">
                </div>
                
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="Functional" {{ request('status') == 'Functional' ? 'selected' : '' }}>Functional</option>
                        <option value="Partially Functional" {{ request('status') == 'Partially Functional' ? 'selected' : '' }}>Partially Functional</option>
                        <option value="Non Functional" {{ request('status') == 'Non Functional' ? 'selected' : '' }}>Non Functional</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Scheme Name</th>
                            <th>Village</th>
                            <th>Ward</th>
                            <th>Water Source</th>
                            <th>Network (m)</th>
                            <th>Status</th>
                            <th>HH Served</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schemes as $scheme)
                        <tr>
                            <td>{{ $scheme->name }}</td>
                            <td>{{ $scheme->village }}</td>
                            <td>{{ $scheme->ward }}</td>
                            <td>
                                {{ $scheme->water_source }}
                                @if($scheme->water_source_other)
                                    ({{ $scheme->water_source_other }})
                                @endif
                            </td>
                            <td>{{ number_format($scheme->network_length) }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $scheme->functionality == 'Functional' ? 'success' : 
                                    ($scheme->functionality == 'Partially Functional' ? 'warning' : 'danger') 
                                }}">
                                    {{ $scheme->functionality }}
                                </span>
                            </td>
                            <td>{{ number_format($scheme->households_served) }}</td>

              <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('piped-water-schemes.show', $scheme->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('piped-water-schemes.edit', $scheme) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('piped-water-schemes.destroy', $scheme->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this scheme?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Delete"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <!-- <td>
                                <a href="{{ route('piped-water-schemes.edit', $scheme) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('piped-water-schemes.destroy', $scheme) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td> -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $schemes->links() }}
        </div>
    </div>
</div>
@endsection