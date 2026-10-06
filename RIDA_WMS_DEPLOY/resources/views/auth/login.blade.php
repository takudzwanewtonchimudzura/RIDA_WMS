@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<main class="auth-shell">
    <section class="auth-visual" aria-label="Water resources">
        <a class="visual-brand" href="{{ url('/') }}" aria-label="RIDA RWIMS home">
            <span class="brand-mark"><img src="{{ asset('images/rida-logo-192.png') }}" alt="RIDA logo"></span>
            <span>RIDA RWIMS</span>
        </a>
        <div class="visual-copy">
            <p class="visual-kicker">Water resources information management</p>
            <h1>Reliable water data. Better decisions.</h1>
            <p>One secure workspace for infrastructure records, field operations, and resource planning.</p>
        </div>
    </section>

    <section class="auth-panel" aria-labelledby="login-title">
        <div class="auth-content">
            <div class="mobile-brand">
                <span class="brand-mark"><img src="{{ asset('images/rida-logo-192.png') }}" alt="RIDA logo"></span>
                <span>RIDA RWIMS</span>
            </div>

            <p class="auth-eyebrow">Secure workspace</p>
            <h2 id="login-title">Sign in</h2>
            <p class="auth-intro">Use your RIDA account to continue.</p>

            @if (session('status'))
                <div class="form-alert form-alert-info" role="status" aria-live="polite">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="form-alert" role="alert" aria-live="polite">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-field">
                    <label for="email">Email address</label>
                    <div class="input-wrap">
                        <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="@error('email') is-invalid @enderror" required autocomplete="username"
                               autofocus inputmode="email" aria-describedby="email-error">
                    </div>
                    @error('email')<span id="email-error" class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        <input id="password" type="password" name="password"
                               class="@error('password') is-invalid @enderror" required autocomplete="current-password"
                               aria-describedby="password-error">
                        <button class="password-toggle" type="button" id="password-toggle"
                                aria-label="Show password" aria-pressed="false" title="Show password">
                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    @error('password')<span id="password-error" class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-options">
                    <label class="remember-option" for="remember">
                        <input type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))>
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <button class="submit-button" type="submit">
                    <span>Sign in</span>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </button>
            </form>

            @if (Route::has('register'))
                <p class="register-link">Need an account? <a href="{{ route('register') }}">Register</a></p>
            @endif

            <footer class="auth-footer">RIDA RWIMS · Water infrastructure management</footer>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
    const passwordField = document.getElementById('password');
    const passwordToggle = document.getElementById('password-toggle');

    passwordToggle?.addEventListener('click', () => {
        const isVisible = passwordField.type === 'text';
        passwordField.type = isVisible ? 'password' : 'text';
        passwordToggle.setAttribute('aria-pressed', String(!isVisible));
        passwordToggle.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
        passwordToggle.title = isVisible ? 'Show password' : 'Hide password';
        passwordToggle.querySelector('i').className = isVisible ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
    });
</script>
@endpush