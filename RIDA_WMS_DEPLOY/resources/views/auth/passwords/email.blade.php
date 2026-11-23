@extends('layouts.guest')
@section('title', 'Forgot Password')
@section('content')
<main class="auth-panel" style="min-height:100svh">
    <div class="auth-content">
        <p class="auth-eyebrow">{{ config('rwims.organization_name') }}</p>
        <h2>Forgot password?</h2>
        <p class="auth-intro">Enter your account email to request a reset link. Links expire after {{ config('auth.passwords.users.expire', 60) }} minutes.</p>
        @if(session('status'))<div class="form-alert form-alert-info" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="form-alert" role="alert">{{ $errors->first() }}</div>@endif
        @if(in_array(config('mail.default'), ['log', 'array'], true))
            <div class="form-alert" role="alert">Email delivery is not configured on this installation. Ask an administrator for a temporary password.</div>
        @endif
        @if(config('rwims.support_email'))<p>Support: <a href="mailto:{{ config('rwims.support_email') }}">{{ config('rwims.support_email') }}</a></p>@endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-field">
                <label for="email">Account email</label>
                <div class="input-wrap"><i class="fa-regular fa-envelope" aria-hidden="true"></i><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus></div>
            </div>
            <button type="submit" class="submit-button">Request reset link</button>
        </form>
        <p class="register-link"><a href="{{ route('login') }}">Back to sign in</a></p>
    </div>
</main>
@endsection
