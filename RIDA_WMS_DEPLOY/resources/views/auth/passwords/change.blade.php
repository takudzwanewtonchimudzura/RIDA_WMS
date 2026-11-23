@extends('layouts.app')
@section('title', 'Change Password')
@section('content')
<div class="container py-3" style="max-width:700px">
    <h2>Change password</h2>
    @if(auth()->user()->must_change_password)<div class="alert alert-warning" role="alert">You are using a temporary password. Choose your own password before continuing.</div>@endif
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <p class="text-muted">Use at least 12 characters with uppercase and lowercase letters and a number. Changing your password signs out other sessions and revokes outstanding reset links.</p>
    <form method="POST" action="{{ route('account.password.update') }}" class="card card-body">
        @csrf @method('PUT')
        <div class="mb-3"><label for="current_password" class="form-label">Current password</label><input id="current_password" type="password" name="current_password" class="form-control" required autocomplete="current-password"></div>
        <div class="mb-3"><label for="password" class="form-label">New password</label><input id="password" type="password" name="password" class="form-control" required minlength="12" autocomplete="new-password"></div>
        <div class="mb-3"><label for="password_confirmation" class="form-label">Confirm new password</label><input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required minlength="12" autocomplete="new-password"></div>
        <button type="submit" class="btn btn-primary">Change password</button>
    </form>
    @unless(auth()->user()->must_change_password)<a class="btn btn-link mt-2" href="{{ route('dashboard') }}">Back to dashboard</a>@endunless
</div>
@endsection
