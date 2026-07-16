<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WordPress source (архив банков /banks/)
    |--------------------------------------------------------------------------
    */

    'wp_base_url' => rtrim((string) env('BANKS_WP_BASE_URL', env('RATES_WP_BASE_URL', 'https://pultop.uz')), '/'),

    'wp_timeout' => (int) env('BANKS_WP_TIMEOUT', env('RATES_WP_TIMEOUT', 30)),

    'wp_list_path' => (string) env('BANKS_WP_LIST_PATH', '/banks/'),

    'wp_rating_path' => (string) env('BANKS_WP_RATING_PATH', '/banks-of-uzbekistan/'),

    'parse_delay_ms' => (int) env('BANKS_PARSE_DELAY_MS', 300),

];
