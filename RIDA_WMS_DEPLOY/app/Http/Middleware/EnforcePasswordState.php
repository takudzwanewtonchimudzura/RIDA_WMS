<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforcePasswordState
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }
        if ((int) $request->session()->get('password_version', 0) !== (int) $user->password_version
            || ($user->temporary_password_expires_at && $user->temporary_password_expires_at->isPast())) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return $request->expectsJson()
                ? response()->json(['message' => 'Your password changed or expired. Sign in again.'], 401)
                : redirect()->route('login')->withErrors(['email' => 'Your password changed or expired. Sign in again.']);
        }
        if ($user->must_change_password && !$request->routeIs('account.password.*', 'logout')) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Change your temporary password before continuing.'], 403)
                : redirect()->route('account.password.edit');
        }
        return $next($request);
    }
}
