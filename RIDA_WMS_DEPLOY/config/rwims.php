<?php

return [
    'organization_name' => 'RIDA RWIMS',
    'support_email' => null,
    'idle_timeout' => (int) env('SESSION_IDLE_TIMEOUT', 20),
    'default_page_size' => 20,
    'capture_cost_limit' => 100000,
    'ph_min' => 6.5,
    'ph_max' => 8.5,
    'tds_max' => 600,
    'turbidity_max' => 5,
];
