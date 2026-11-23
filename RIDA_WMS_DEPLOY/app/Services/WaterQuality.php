<?php

namespace App\Services;

class WaterQuality
{
    public static function limits(): array
    {
        return array_intersect_key(app(SystemSettings::class)->all(), array_flip(['ph_min', 'ph_max', 'tds_max', 'turbidity_max']));
    }

    public static function compliant($query)
    {
        $limits = self::limits();
        return $query->whereBetween('ph', [$limits['ph_min'], $limits['ph_max']])
            ->whereBetween('tds_mg_l', [0, $limits['tds_max']])
            ->whereBetween('turbidity_ntu', [0, $limits['turbidity_max']]);
    }
}
