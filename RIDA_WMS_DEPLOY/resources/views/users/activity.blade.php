@extends('layouts.app')

@section('title', 'User Activity')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="text-decoration-none text-muted small">
            <i class="fas fa-arrow-left me-1"></i> Back to Users
        </a>
        <h2 class="fw-bold mt-2 mb-1">User Activity</h2>
        <p class="text-muted mb-0">Track when users last accessed the system</p>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary">{{ $stats['total'] }}</div>
                <div class="text-muted small">Total Users</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success">{{ $stats['active_today'] }}</div>
                <div class="text-muted small">Active Today</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-info">{{ $stats['active_week'] }}</div>
                <div class="text-muted small">Active This Week</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-warning">{{ $stats['never_logged_in'] }}</div>
                <div class="text-muted small">Never Logged In</div>
            </div>
        </div>
    </div>

    {{-- Activity Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-history me-2 text-primary"></i>Login Activity</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Member Since</th>
                            <th class="text-end pe-4">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        @php
                            $roleColors = ['admin' => 'danger', 'operator' => 'primary', 'viewer' => 'success'];
                            $roleLabels = ['admin' => 'Admin', 'operator' => 'Field Operator', 'viewer' => 'Viewer'];
                            $color = $roleColors[$user->role] ?? 'secondary';
                            $label = $roleLabels[$user->role] ?? ucfirst($user->role);

                            $activityLevel = 'Never logged in';
                            $activityColor = 'secondary';
                            if ($user->last_login_at) {
                                $diff = now()->diffInDays($user->last_login_at);
                                if ($diff === 0) { $activityLevel = 'Today'; $activityColor = 'success'; }
                                elseif ($diff <= 7) { $activityLevel = 'This week'; $activityColor = 'info'; }
                                elseif ($diff <= 30) { $activityLevel = 'This month'; $activityColor = 'primary'; }
                                else { $activityLevel = 'Inactive'; $activityColor = 'warning'; }
                            }
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-{{ $color }} bg-opacity-10 text-{{ $color }} d-flex align-items-center justify-content-center fw-bold"
                                         style="width:38px;height:38px;min-width:38px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-{{ $color }}">{{ $label }}</span></td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Inactive</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('d M Y, H:i') }}<br>
                                    <span class="text-muted">{{ $user->last_login_at->diffForHumans() }}</span>
                                @else
                                    <span class="text-muted fst-italic">Never</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <span class="badge bg-{{ $activityColor }}-subtle text-{{ $activityColor }} border border-{{ $activityColor }}">
                                    {{ $activityLevel }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection