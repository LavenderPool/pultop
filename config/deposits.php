<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WordPress source (вклады)
    |--------------------------------------------------------------------------
    */

    'wp_base_url' => rtrim((string) env('DEPOSITS_WP_BASE_URL', env('RATES_WP_BASE_URL', 'https://pultop.uz')), '/'),

    'wp_timeout' => (int) env('DEPOSITS_WP_TIMEOUT', env('RATES_WP_TIMEOUT', 30)),

    'wp_ajax_path' => (string) env('DEPOSITS_WP_AJAX_PATH', env('RATES_WP_AJAX_PATH', '/wp-admin/admin-ajax.php')),

    'wp_list_path' => (string) env('DEPOSITS_WP_LIST_PATH', '/deposits/'),

    'parse_delay_ms' => (int) env('DEPOSITS_PARSE_DELAY_MS', 300),

    'parse_concurrency' => (int) env('DEPOSITS_PARSE_CONCURRENCY', 5),

    'per_page' => (int) env('DEPOSITS_PER_PAGE', 10),

];
