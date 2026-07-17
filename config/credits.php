<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WordPress source (кредиты)
    |--------------------------------------------------------------------------
    */

    'wp_base_url' => rtrim((string) env('CREDITS_WP_BASE_URL', env('RATES_WP_BASE_URL', 'https://pultop.uz')), '/'),

    'wp_timeout' => (int) env('CREDITS_WP_TIMEOUT', env('RATES_WP_TIMEOUT', 30)),

    'wp_ajax_path' => (string) env('CREDITS_WP_AJAX_PATH', env('RATES_WP_AJAX_PATH', '/wp-admin/admin-ajax.php')),

    'parse_delay_ms' => (int) env('CREDITS_PARSE_DELAY_MS', 300),

    'parse_concurrency' => (int) env('CREDITS_PARSE_CONCURRENCY', 5),

];
