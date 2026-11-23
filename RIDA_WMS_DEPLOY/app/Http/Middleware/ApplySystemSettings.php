<?php

namespace App\Http\Middleware;

use App\Services\SystemSettings;
use Closure;
use Illuminate\Http\Request;

class ApplySystemSettings
{
    public function handle(Request $request, Closure $next)
    {
        config(['rwims' => app(SystemSettings::class)->all(true)]);
        config(['session.idle_timeout' => config('rwims.idle_timeout')]);

        return $next($request);
    }
}
