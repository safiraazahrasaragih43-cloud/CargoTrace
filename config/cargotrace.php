<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CargoTrace Global Configuration (Individual All-Routes Mode)
    |--------------------------------------------------------------------------
    | Combines all 3 global logistics corridors with deterministic ETA delay
    | thresholds, severity tiers, and primary exception focus.
    */

    'default_delay_threshold_hours' => (int) env('CARGOTRACE_DELAY_THRESHOLD', 12),

    'corridors' => [
        'SEA_EUROPE' => [
            'name' => 'Southeast Asia → Europe',
            'origin_countries' => ['Indonesia', 'Singapore', 'Malaysia', 'Thailand', 'Vietnam', 'Philippines'],
            'destination_countries' => ['Netherlands', 'Germany', 'United Kingdom', 'France', 'Belgium', 'Spain', 'Italy'],
            'delay_threshold_hours' => 12,
            'primary_exception' => 'CUSTOMS_HOLD',
            'kpi_focus' => 'on_time_delivery_rate',
        ],
        'ASIA_NORTH_AMERICA' => [
            'name' => 'Asia → North America',
            'origin_countries' => ['Indonesia', 'Singapore', 'China', 'Japan', 'South Korea', 'Taiwan', 'Vietnam'],
            'destination_countries' => ['United States', 'Canada', 'Mexico'],
            'delay_threshold_hours' => 18,
            'primary_exception' => 'WEATHER_DELAY',
            'kpi_focus' => 'average_delay_duration',
        ],
        'SEA_AUSTRALIA' => [
            'name' => 'Southeast Asia → Australia',
            'origin_countries' => ['Indonesia', 'Singapore', 'Malaysia', 'Thailand', 'Vietnam', 'Philippines'],
            'destination_countries' => ['Australia', 'New Zealand'],
            'delay_threshold_hours' => 24,
            'primary_exception' => 'DOCUMENTATION_ISSUE',
            'kpi_focus' => 'exception_resolution_time',
        ],
        'GLOBAL_DEFAULT' => [
            'name' => 'Global Standard Corridor',
            'origin_countries' => [],
            'destination_countries' => [],
            'delay_threshold_hours' => (int) env('CARGOTRACE_DELAY_THRESHOLD', 12),
            'primary_exception' => 'PORT_CONGESTION',
            'kpi_focus' => 'on_time_delivery_rate',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Deterministic Delay Severity Rules (Module Slide 08)
    |--------------------------------------------------------------------------
    | 0 hours: ON_TIME
    | 1-6 hours: LOW
    | >6-24 hours: MEDIUM
    | >24 hours: HIGH
    */
    'severity_tiers' => [
        'LOW' => ['min' => 1, 'max' => 6],
        'MEDIUM' => ['min' => 7, 'max' => 24],
        'HIGH' => ['min' => 25, 'max' => null],
    ],
];
