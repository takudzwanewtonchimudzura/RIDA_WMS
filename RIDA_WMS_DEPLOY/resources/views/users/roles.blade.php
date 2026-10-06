@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="text-decoration-none text-muted small">
            <i class="fas fa-arrow-left me-1"></i> Back to Users
        </a>
        <h2 class="fw-bold mt-2 mb-1">Roles & Permissions</h2>
        <p class="text-muted mb-0">Overview of system roles and what each role can do</p>
    </div>

    <div class="row g-4">
        @foreach($roles as $key => $role)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-{{ $role['color'] }} bg-opacity-10 border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="badge bg-{{ $role['color'] }} mb-1">{{ strtoupper($key) }}</span>
                            <h5 class="fw-bold mb-0">{{ $role['label'] }}</h5>
                        </div>
                        <div class="text-center">
                            <div class="fs-2 fw-bold text-{{ $role['color'] }}">{{ $role['count'] }}</div>
                            <div class="text-muted small">users</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ $role['description'] }}</p>
                    <h6 class="fw-semibold mb-2">Permissions:</h6>
                    <ul class="list-unstyled mb-0">
                        @foreach($role['permissions'] as $permission)
                        <li class="d-flex align-items-center gap-2 mb-1 small">
                            <i class="fas fa-check-circle text-{{ $role['color'] }}"></i>
                            {{ $permission }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('users.index') }}?role={{ $key }}" class="btn btn-sm btn-outline-{{ $role['color'] }} w-100">
                        View {{ $role['label'] }} Users
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Summary table --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-table me-2 text-primary"></i>Permission Summary</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Permission</th>
                            <th class="text-center">Admin</th>
                            <th class="text-center">Field Officer</th>
                            <th class="text-center">Viewer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $permissions = [
                            'Manage users'         => [true,  false, false],
                            'Create boreholes'     => [true,  true,  false],
                            'Edit boreholes'       => [true,  true,  false],
                            'Delete boreholes'     => [true,  false, false],
                            'Approve boreholes'    => [true,  false, false],
                            'Create dams'          => [true,  true,  false],
                            'Edit dams'            => [true,  true,  false],
                            'Delete dams'          => [true,  false, false],
                            'View reports'         => [true,  true,  true],
                            'Export data'          => [true,  true,  true],
                            'System settings'      => [true,  false, false],
                        ];
                        @endphp
                        @foreach($permissions as $perm => $access)
                        <tr>
                            <td class="ps-4">{{ $perm }}</td>
                            @foreach($access as $allowed)
                            <td class="text-center">
                                @if($allowed)
                                    <i class="fas fa-check-circle text-success fs-5"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger fs-5 opacity-25"></i>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection