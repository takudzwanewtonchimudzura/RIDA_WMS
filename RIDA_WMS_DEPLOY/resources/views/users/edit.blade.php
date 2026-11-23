@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="container py-4" style="max-width:700px">
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left me-1"></i> Back to Users</a>
        <h2 class="fw-bold mt-2 mb-1">Edit User</h2>
        <p class="text-muted mb-0">Update account details for <strong>{{ $user->name }}</strong></p>
    </div>
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">-- Select Role --</option>
                            @foreach(\App\Models\User::ROLES as $role => $label)
                            <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Department</label>
                        <input type="text" name="department" class="form-control" value="{{ old('department', $user->department) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end pb-1">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Active Account</label>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Save Changes</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    @if($user->id === auth()->id())
        <a href="{{ route('account.password.edit') }}" class="btn btn-outline-primary mt-3">Change my password</a>
    @elseif($user->is_active)
    <div class="card card-body mt-4">
        <h3 class="h5">Account recovery</h3>
        <p>Verify this user's identity first. A temporary password expires in 24 hours and must be changed at the next sign-in. Issuance revokes existing sessions/reset links and is audited.</p>
        <form method="POST" action="{{ route('users.temporary-password', $user) }}" data-confirm="Issue a temporary password and sign this user out everywhere?">
            @csrf
            <label for="recovery_current_password" class="form-label">Your administrator password</label>
            <input id="recovery_current_password" type="password" name="current_password" required autocomplete="current-password" class="form-control mb-3">
            <button type="submit" class="btn btn-outline-warning">Issue temporary password</button>
        </form>
    </div>
    @endif
    @if($user->id !== auth()->id())
    <div class="card border-danger border-opacity-25 shadow-sm mt-4">
        <div class="card-header bg-danger bg-opacity-10 py-3">
            <h6 class="mb-0 fw-semibold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h6>
        </div>
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-semibold">Delete this user</div>
                <div class="text-muted small">The account will be archived; record ownership and audit history are preserved.</div>
            </div>
            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Archive this user account?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm px-3"><i class="fas fa-trash me-1"></i>Delete User</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
