<?php

return [
    'branding' => [
        'name' => 'RIDA RWIMS',
        'logo' => public_path('images/rida-logo.png'),
        'colors' => [
            'primary' => '#2c7fb8',
            'secondary' => '#7fcdbb',
            'success' => '#28a745',
            'warning' => '#ffc107',
            'danger' => '#dc3545',
            'info' => '#17a2b8',
        ],
        'contact' => [
            'organization' => 'Rural Infrastructure Development Agency',
            'address' => 'Government Complex, Capital City',
            'phone' => '+263-XXX-XXXXXX',
            'email' => 'reports@rida.gov.zw'
        ]
    ],
    
    'charts' => [
        'default' => 'chartjs',
        'colors' => [
            '#2c7fb8', '#7fcdbb', '#edf8b1', '#2ca25f', 
            '#99d8c9', '#66c2a4', '#41ae76', '#238b45'
        ]
    ],
    
    'exports' => [
        'pdf_orientation' => 'landscape',
        'excel_auto_size' => true,
        'include_charts' => true
    ],

    'cache' => [
        'enabled' => true,
        'duration' => 3600, // 1 hour in seconds
    ]
];