@extends('layouts.app')
@section('title', 'Temporary Password Issued')
@section('content')
<div class="container py-3" style="max-width:700px">
    <h2>Temporary password issued</h2>
    <div class="alert alert-warning" role="alert">Shown once. Share privately with the verified account holder. Do not email or post this password in a group.</div>
    <dl class="card card-body">
        <dt>Account</dt><dd>{{ $user->email }}</dd>
        <dt>Temporary password</dt><dd><code class="fs-5 text-break">{{ $temporaryPassword }}</code></dd>
        <dt>Expires</dt><dd>{{ $user->temporary_password_expires_at->format('d M Y H:i T') }}</dd>
    </dl>
    <p>The user must change this password at their next sign-in. Existing sessions and reset links have been revoked. The audit log records issuance, not the password.</p>
    <a class="btn btn-primary" href="{{ route('users.edit', $user) }}">Return to user</a>
</div>
@endsection
