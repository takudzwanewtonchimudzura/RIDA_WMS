<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#103d39">
    <title>@yield('title', 'RIDA RWIMS')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/rida-logo-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/rida-logo-180.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            color-scheme: light;
            --ink: #152e2b;
            --muted: #637773;
            --line: #dbe5e1;
            --green: #14665b;
            --green-dark: #0d423d;
            --lime: #c4df7b;
            --surface: #fbfdfb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-width: 320px;
            min-height: 100vh;
            min-height: 100svh;
            background: var(--surface);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
        }

        a { color: var(--green); }

        .auth-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(390px, 0.92fr);
            min-height: 100vh;
            min-height: 100svh;
        }

        .auth-visual {
            position: relative;
            display: flex;
            min-height: 100%;
            align-items: flex-end;
            overflow: hidden;
            padding: clamp(32px, 5vw, 76px);
            color: #fff;
            background-color: #164f4d;
            background-image:
                linear-gradient(180deg, rgba(11, 42, 39, 0.18) 10%, rgba(7, 37, 35, 0.88) 100%),
                url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?auto=format&fit=crop&w=1800&q=85');
            background-position: center;
            background-size: cover;
        }

        .auth-visual::after {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(13, 66, 61, 0.1), transparent 62%);
            content: '';
            pointer-events: none;
        }

        .visual-brand {
            position: absolute;
            z-index: 1;
            top: clamp(28px, 4vw, 56px);
            left: clamp(32px, 5vw, 76px);
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: white;
            font-family: 'Manrope', sans-serif;
            font-size: 16px;
            font-weight: 800;
            text-decoration: none;
        }

        .brand-mark {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.48);
            border-radius: 12px;
            background: #fff;
            color: var(--lime);
            font-size: 19px;
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 3px;
        }

        .visual-copy {
            position: relative;
            z-index: 1;
            max-width: 560px;
        }

        .visual-kicker {
            margin: 0 0 16px;
            color: var(--lime);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .visual-copy h1 {
            max-width: 560px;
            margin: 0;
            font-family: 'Manrope', sans-serif;
            font-size: clamp(32px, 4.1vw, 58px);
            font-weight: 700;
            line-height: 1.08;
        }

        .visual-copy p {
            max-width: 480px;
            margin: 18px 0 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 16px;
            line-height: 1.65;
        }

        .auth-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(28px, 6vw, 88px);
            background:
                radial-gradient(ellipse at 95% 6%, rgba(196, 223, 123, 0.13), transparent 28%),
                var(--surface);
        }

        .auth-content { width: min(100%, 430px); }

        .mobile-brand {
            display: none;
            align-items: center;
            gap: 10px;
            margin-bottom: 34px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
        }

        .mobile-brand .brand-mark {
            border-color: #cfe0d7;
            background: #fff;
            color: var(--green);
        }

        .auth-eyebrow {
            margin: 0 0 10px;
            color: var(--green);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .auth-content h2 {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
        }

        .auth-intro {
            margin: 11px 0 30px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.55;
        }

        .form-field { margin-bottom: 20px; }

        .form-field label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
        }

        .input-wrap { position: relative; }

        .input-wrap > i {
            position: absolute;
            top: 50%;
            left: 16px;
            color: #6f8982;
            transform: translateY(-50%);
        }

        .form-field input[type="email"],
        .form-field input[type="password"],
        .form-field input[type="text"] {
            width: 100%;
            min-height: 52px;
            padding: 12px 48px 12px 44px;
            border: 1px solid var(--line);
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: var(--ink);
            font: inherit;
            transition: border-color 160ms ease, box-shadow 160ms ease;
        }

        .form-field input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(20, 102, 91, 0.14);
        }

        .form-field input.is-invalid { border-color: #bd3d38; }

        .field-error {
            display: block;
            margin-top: 7px;
            color: #a52d2a;
            font-size: 13px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #59736d;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .password-toggle:hover { background: #eef4f0; color: var(--green-dark); }
        .password-toggle:focus-visible { outline: 2px solid var(--green); outline-offset: 2px; }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin: 2px 0 26px;
            font-size: 13px;
        }

        .remember-option { display: inline-flex; align-items: center; gap: 9px; color: #405a54; }
        .remember-option input { width: 16px; height: 16px; accent-color: var(--green); }
        .form-options a, .register-link a { font-weight: 700; text-decoration-thickness: 1px; text-underline-offset: 3px; }

        .submit-button {
            display: flex;
            width: 100%;
            min-height: 52px;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 0;
            border-radius: 8px;
            background: var(--green);
            color: white;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
            transition: background 160ms ease, transform 160ms ease;
        }

        .submit-button:hover { background: var(--green-dark); transform: translateY(-1px); }
        .submit-button:focus-visible { outline: 3px solid rgba(20, 102, 91, 0.35); outline-offset: 3px; }

        .form-alert {
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #efc4c0;
            border-radius: 8px;
            background: #fff5f3;
            color: #852a25;
            font-size: 14px;
        }

        .form-alert-info {
            border-color: #bfe0da;
            background: #eefaf7;
            color: #14665b;
        }

        .register-link {
            margin: 24px 0 0;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }

        .auth-footer {
            margin-top: 54px;
            color: #758681;
            font-size: 11px;
            line-height: 1.6;
            text-align: center;
        }

        @media (max-width: 860px) {
            .auth-shell { grid-template-columns: minmax(0, 0.85fr) minmax(370px, 1.15fr); }
            .auth-panel { padding: 42px; }
            .visual-copy h1 { font-size: 40px; }
        }

        @media (max-width: 680px) {
            .auth-shell { display: flex; flex-direction: column; }
            .auth-visual { min-height: 220px; padding: 28px 24px; }
            .visual-brand { display: none; }
            .visual-copy { display: none; }
            .auth-panel { flex: 1; align-items: flex-start; padding: 34px 24px 28px; }
            .auth-content { margin: 0 auto; }
            .mobile-brand { display: flex; }
            .auth-content h2 { font-size: 28px; }
            .auth-intro { margin-bottom: 25px; }
            .auth-footer { margin-top: 40px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>