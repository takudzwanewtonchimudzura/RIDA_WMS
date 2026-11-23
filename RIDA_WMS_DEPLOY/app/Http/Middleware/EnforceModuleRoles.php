<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceModuleRoles
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || $user->isAdmin()) {
            return $next($request);
        }
        $route = $request->route();
        if (!$route) {
            return $next($request);
        }
        if ($route->named('clients.*', 'invoices.*')) {
            abort_unless($user->canManageFinance(), 403);
        }
        $writing = !$request->isMethodSafe() || $route->named('*.create', '*.edit');
        if ($writing && !$route->named('session.heartbeat', 'logout', 'tickets.*', 'account.password.*')) {
            if ($user->role === 'finance') {
                abort_unless($route->named('clients.*', 'invoices.*'), 403);
            } elseif ($user->role === 'stores') {
                abort_unless($route->named('consumables.*'), 403);
            } elseif ($user->isViewer()) {
                abort(403);
            }
        }
        return $next($request);
    }
}
