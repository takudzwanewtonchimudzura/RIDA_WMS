@extends('layouts.guest')
@section('title', 'Reset Password')
@section('content')
<main class="auth-panel" style="min-height:100svh">
    <div class="auth-content">
        <p class="auth-eyebrow">{{ config('rwims.organization_name') }}</p>
        <h2>Reset password</h2>
        <p class="auth-intro">Use at least 12 characters with uppercase and lowercase letters and a number.</p>
        @if($errors->any())<div class="form-alert" role="alert">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-field"><label for="email">Account email</label><div class="input-wrap"><i class="fa-regular fa-envelope" aria-hidden="true"></i><input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email"></div></div>
            <div class="form-field"><label for="password">New password</label><div class="input-wrap"><i class="fa-solid fa-lock" aria-hidden="true"></i><input id="password" type="password" name="password" minlength="12" required autocomplete="new-password"></div></div>
            <div class="form-field"><label for="password_confirmation">Confirm new password</label><div class="input-wrap"><i class="fa-solid fa-lock" aria-hidden="true"></i><input id="password_confirmation" type="password" name="password_confirmation" minlength="12" required autocomplete="new-password"></div></div>
            <button type="submit" class="submit-button">Save new password</button>
        </form>
        <p class="register-link"><a href="{{ route('password.request') }}">Request another link</a> &middot; <a href="{{ route('login') }}">Back to sign in</a></p>
    </div>
</main>
@endsection
