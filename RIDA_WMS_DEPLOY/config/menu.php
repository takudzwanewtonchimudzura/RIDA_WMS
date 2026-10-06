<?php

return [
    'main_navigation' => [
        'title' => 'MAIN NAVIGATION',
        'items' => [
            'dashboard' => [
                'title' => 'Dashboard',
                'icon' => 'fas fa-home',
                'route' => 'dashboard',
                'badge' => null,
            ],
            'prospecting' => [
                'title' => 'Prospecting',
                'icon' => 'fas fa-compass',
                'submenu' => [
                    'prospecting_sites' => ['title' => 'All Sites', 'route' => 'prospecting.index'],
                    'new_prospecting_site' => ['title' => 'New Site Survey', 'route' => 'prospecting.create'],
                ],
            ],
            'boreholes' => [
                'title' => 'Boreholes',
                'icon' => 'fas fa-water',
                'color' => '#3b82f6',
                'submenu' => [
                    'all_boreholes' => [
                        'title' => 'All Boreholes',
                        'route' => 'boreholes.index',
                    ],
                    'new_borehole' => [
                        'title' => 'New Borehole',
                        'route' => 'boreholes.create',
                    ],
                    'borehole_maps' => [
                        'title' => 'Borehole Maps',
                        'route' => 'maps.geographic',
                    ],
                    'borehole_reports' => [
                        'title' => 'Borehole Reports',
                        'route' => 'reports.technical.analysis',
                    ],
                    'consumables_performance' => [
                        'title' => 'Consumables Performance',
                        'route' => 'reports.consumables.performance',
                    ],
                ],
            ],
            'dams' => [
                'title' => 'Dams',
                'icon' => 'fas fa-water',
                'color' => '#10b981',
                'submenu' => [
                    'all_dams' => [
                        'title' => 'All Dams',
                        'route' => 'dams.index',
                    ],
                    'new_dam' => [
                        'title' => 'New Dam',
                        'route' => 'dams.create',
                    ],
                    'dam_levels' => [
                        'title' => 'Dam Levels',
                        'route' => 'dams.levels',
                    ],
                    'dam_inspections' => [
                        'title' => 'Dam Inspections',
                        'route' => 'dams.inspections',
                    ],
                ],
            ],
            'water_schemes' => [
                'title' => 'Water Schemes',
                'icon' => 'fas fa-faucet',
                'color' => '#8b5cf6',
                'submenu' => [
                    'all_schemes' => [
                        'title' => 'All Schemes',
                        'route' => 'piped-water-schemes.index',
                    ],
                    'new_scheme' => [
                        'title' => 'New Scheme',
                        'route' => 'piped-water-schemes.create',
                    ],
                    'scheme_networks' => [
                        'title' => 'Scheme Networks',
                        'route' => 'piped-water-schemes.networks',
                    ],
                    'water_quality' => [
                        'title' => 'Water Quality',
                        'route' => 'piped-water-schemes.quality',
                    ],
                ],
            ],
            'irrigation_schemes' => [
                'title' => 'Irrigation Schemes',
                'icon' => 'fas fa-seedling',
                'color' => '#16a34a',
                'submenu' => [
                    'all_irrigation_schemes' => [
                        'title' => 'All Schemes',
                        'route' => 'irrigation-schemes.index',
                    ],
                    'new_irrigation_scheme' => [
                        'title' => 'New Scheme',
                        'route' => 'irrigation-schemes.create',
                    ],
                ],
            ],
            'tickets' => [
                'title' => 'Ticketing',
                'icon' => 'fas fa-ticket-alt',
                'submenu' => [
                    'my_tickets' => ['title' => 'My Tickets', 'route' => 'tickets.index'],
                    'new_ticket' => ['title' => 'New Ticket', 'route' => 'tickets.create'],
                ],
            ],
        ],
    ],

    'management' => [
        'title' => 'MANAGEMENT',
        'items' => [
            'users' => [
                'title' => 'Users',
                'icon' => 'fas fa-users',
                'color' => '#ec4899',
                'submenu' => [
                    'all_users' => [
                        'title' => 'All Users',
                        'route' => 'users.index',
                    ],
                    'add_user' => [
                        'title' => 'Add User',
                        'route' => 'users.create',
                    ],
                    'roles_permissions' => [
                        'title' => 'Roles & Permissions',
                        'route' => 'users.roles',
                    ],
                    'user_activity' => [
                        'title' => 'User Activity',
                        'route' => 'users.activity',
                    ],
                ],
            ],
        ],
    ],

    'reports_analytics' => [
        'title' => 'REPORTS & ANALYTICS',
        'items' => [
            'executive-dashboard' => [
                'title' => 'Executive Dashboard',
                'icon' => 'fas fa-chart-line',
                'route' => 'reports.executive.dashboard',
                'color' => '#3b82f6'
            ],
            'reports' => [
                'title' => 'Advanced Reports',
                'icon' => 'fas fa-chart-bar',
                'color' => '#10b981',
                'submenu' => [
                    'operational_reports' => [
                        'title' => 'Operational Performance',
                        'route' => 'reports.operational.performance',
                    ],
                    'technical_reports' => [
                        'title' => 'Technical Analysis', 
                        'route' => 'reports.technical.analysis',
                    ],
                    'geographic_reports' => [
                        'title' => 'Geographic Distribution',
                        'route' => 'reports.geographic.distribution',
                    ],
                    'financial_reports' => [
                        'title' => 'Financial Reports',
                        'route' => 'reports.financial',
                    ],
                ],
            ],
            'analytics' => [
                'title' => 'Analytics Dashboard',
                'icon' => 'fas fa-chart-pie',
                'color' => '#8b5cf6',
                'route' => 'analytics.dashboard',
            ],
            'maps' => [
                'title' => 'Geographic Maps',
                'icon' => 'fas fa-map-marked-alt',
                'color' => '#ef4444',
                'route' => 'maps.geographic',
            ],
        ],
    ],

];