<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\ZimbabweLocations::class);
        $this->app->scoped(\App\Services\SystemSettings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (['password-email' => 5, 'password-reset' => 10, 'password-change' => 5, 'temporary-password' => 5, 'settings-update' => 10] as $name => $attempts) {
            \Illuminate\Support\Facades\RateLimiter::for($name, fn (\Illuminate\Http\Request $request) =>
                \Illuminate\Cache\RateLimiting\Limit::perMinute($attempts)->by($name . ':' . ($request->user()?->id ?? $request->ip()))
            );
        }
        foreach ([
            \App\Models\Borehole::class, \App\Models\GeologyLayer::class,
            \App\Models\CasingDetail::class, \App\Models\BoreholeConsumableUsage::class,
        ] as $model) {
            $model::observe(\App\Services\ReportCache::class);
        }
    }
}
