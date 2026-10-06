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
                    <div class="col-12">
                        <div class="alert alert-info py-2 mb-0 small"><i class="fas fa-info-circle me-1"></i> Leave password fields blank to keep the current password.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">-- Select Role --</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Field Operator</option>
                            <option value="viewer" {{ old('role', $user->role) == 'viewer' ? 'selected' : '' }}>Viewer</option>
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
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
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
    @if($user->id !== auth()->id())
    <div class="card border-danger border-opacity-25 shadow-sm mt-4">
        <div class="card-header bg-danger bg-opacity-10 py-3">
            <h6 class="mb-0 fw-semibold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h6>
        </div>
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-semibold">Delete this user</div>
                <div class="text-muted small">This action cannot be undone.</div>
            </div>
            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Permanently delete this user?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm px-3"><i class="fas fa-trash me-1"></i>Delete User</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
