<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WordPress source (wp-json posts)
    |--------------------------------------------------------------------------
    */

    'wp_base_url' => rtrim((string) env('ARTICLES_WP_BASE_URL', env('RATES_WP_BASE_URL', 'https://pultop.uz')), '/'),

    'wp_timeout' => (int) env('ARTICLES_WP_TIMEOUT', env('RATES_WP_TIMEOUT', 30)),

    'parse_delay_ms' => (int) env('ARTICLES_PARSE_DELAY_MS', 200),

    'per_page' => (int) env('ARTICLES_WP_PER_PAGE', 100),

    'homepage_limit' => (int) env('ARTICLES_HOMEPAGE_LIMIT', 6),

    'sidebar_limit' => (int) env('ARTICLES_SIDEBAR_LIMIT', 5),

];
