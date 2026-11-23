<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function location(bool $borehole = false, int $index = 5): array
    {
        $location = app(\App\Services\ZimbabweLocations::class)->sample($index);
        if ($borehole) {
            $location['admin_district'] = $location['district'];
            unset($location['district']);
        }
        return $location;
    }
}
