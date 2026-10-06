<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $closure, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        if (in_array($user->role, $roles)) {
            return $closure($request);
        }

        abort(403, 'Unauthorized action.');
    }
}