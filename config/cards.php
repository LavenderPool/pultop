<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WordPress source (карты)
    |--------------------------------------------------------------------------
    */

    'wp_base_url' => rtrim((string) env('CARDS_WP_BASE_URL', env('RATES_WP_BASE_URL', 'https://pultop.uz')), '/'),

    'wp_timeout' => (int) env('CARDS_WP_TIMEOUT', env('RATES_WP_TIMEOUT', 30)),

    'wp_ajax_path' => (string) env('CARDS_WP_AJAX_PATH', env('RATES_WP_AJAX_PATH', '/wp-admin/admin-ajax.php')),

    'parse_delay_ms' => (int) env('CARDS_PARSE_DELAY_MS', 300),

    'parse_concurrency' => (int) env('CARDS_PARSE_CONCURRENCY', 5),

];
