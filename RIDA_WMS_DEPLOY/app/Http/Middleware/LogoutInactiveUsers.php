<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutInactiveUsers
{
    /**
     * Force logout an authenticated user once the idle timeout has elapsed.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (!Auth::user()->is_active || Auth::user()->trashed()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return $request->expectsJson()
                    ? response()->json(['message' => 'This account is inactive.'], 401)
                    : redirect()->route('login')->withErrors(['email' => 'This account is inactive.']);
            }
            $timeoutSeconds = max(1, (int) config('session.idle_timeout', 20)) * 60;
            $lastActivity = $request->session()->get('last_activity_at');

            if ($lastActivity !== null && (time() - (int) $lastActivity) > $timeoutSeconds) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired due to inactivity.'], 401);
                }

                return redirect()->route('login')->with('status', 'You have been logged out due to inactivity.');
            }

            $request->session()->put('last_activity_at', time());
            if (!Auth::user()->last_seen_at || Auth::user()->last_seen_at->lt(now()->subMinute())) {
                Auth::user()->forceFill(['last_seen_at' => now()])->save();
            }
        }

        return $next($request);
    }
}
