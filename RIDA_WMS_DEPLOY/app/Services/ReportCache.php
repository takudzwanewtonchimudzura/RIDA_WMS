<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportCache
{
    public static function remember(string $key, int $seconds, callable $callback): mixed
    {
        if (!config('reporting.cache.enabled', true)) {
            return $callback();
        }
        return Cache::remember('reports:' . Cache::get('reports.revision', 'uat-v1') . ':' . $key, $seconds, $callback);
    }

    public static function flush(): void
    {
        Cache::forever('reports.revision', bin2hex(random_bytes(12)));
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    public function saved(): void
    {
        self::flush();
        DB::afterCommit(fn () => self::flush());
    }

    public function deleted(): void
    {
        $this->saved();
    }

    public function restored(): void
    {
        $this->saved();
    }
}
