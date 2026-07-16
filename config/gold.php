<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WordPress source (gold prices / sale points)
    |--------------------------------------------------------------------------
    */

    'wp_base_url' => rtrim((string) env('GOLD_WP_BASE_URL', env('RATES_WP_BASE_URL', 'https://pultop.uz')), '/'),

    'wp_ajax_path' => (string) env('GOLD_WP_AJAX_PATH', env('RATES_WP_AJAX_PATH', '/wp-admin/admin-ajax.php')),

    'wp_timeout' => (int) env('GOLD_WP_TIMEOUT', env('RATES_WP_TIMEOUT', 30)),

    'wp_gold_stat_path' => (string) env('GOLD_WP_GOLD_STAT_PATH', '/gold-stat/'),

];
