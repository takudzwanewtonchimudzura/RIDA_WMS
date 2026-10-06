@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Dashboard</h4>
            </div>
            <div class="card-body">
                <h5>Welcome, {{ auth()->user()->name }}!</h5>
                <p class="mb-0">You are logged in as <strong>{{ ucfirst(auth()->user()->role) }}</strong>.</p>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>Boreholes</h5>
                                <p class="mb-0">Manage water borehole data</p>
                                <a href="{{ route('boreholes.index') }}" class="btn btn-light btn-sm mt-2">View Boreholes</a>
                            </div>
                        </div>
                    </div>
                    
                    @if(auth()->user()->isAdmin())
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>User Management</h5>
                                <p class="mb-0">Manage system users</p>
                                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm mt-2">Manage Users</a>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5>Map View</h5>
                                <p class="mb-0">View boreholes on map</p>
                                <a href="#" class="btn btn-light btn-sm mt-2">View Map</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection