<?php

namespace App\Services;

use App\Models\SystemSetting;

class SystemSettings
{
    private ?array $values = null;

    public function all(bool $refresh = false): array
    {
        if ($refresh || $this->values === null) {
            $defaults = require config_path('rwims.php');
            $this->values = array_replace($defaults, SystemSetting::where('key', 'general')->value('values') ?? []);
        }
        return $this->values;
    }
}
