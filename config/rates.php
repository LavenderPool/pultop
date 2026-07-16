<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WordPress admin-ajax (источник курсов банков)
    |--------------------------------------------------------------------------
    */

    'wp_base_url' => rtrim((string) env('RATES_WP_BASE_URL', 'https://pultop.uz'), '/'),

    'wp_ajax_path' => (string) env('RATES_WP_AJAX_PATH', '/wp-admin/admin-ajax.php'),

    'wp_timeout' => (int) env('RATES_WP_TIMEOUT', 30),

];
