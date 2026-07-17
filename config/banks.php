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

    /*
    |--------------------------------------------------------------------------
    | Алиасы названий из таблицы рейтинга ЦБ → slug банка
    |--------------------------------------------------------------------------
    */
    'rating_aliases' => [
        'узнацбанк' => 'natsionalnyj-bank',
        'агробанк' => 'agrobank',
        'узпромстройбанк' => 'uzpromstroj-bank',
        'асака банк' => 'asaka-bank',
        'народный банк' => 'ak-narodnyj-bank-ruz',
        'банк развития бизнеса' => 'kishlok-kurilish-bank',
        'алока банк' => 'alokabank',
        'микрокредит банк' => 'mikrokreditbank',
        'турон банк' => 'turonbank',
        'капитал банк' => 'kapitalbank',
        'ипотека банк' => 'ipoteka-bank',
        'хамкор банк' => 'hamkorbank',
        'ипак йули банк' => 'ipak-juli-bank',
        'ориент финанс банк' => 'chakb-orient-finans',
        'тибиси банк' => 'tbc-bank',
        'анор банк' => 'anorbank',
        'инвест финанс банк' => 'invest-finance-bank',
        'траст банк' => 'trastbank',
        'узкдб банк' => 'kdb-bank-uzbekistan',
        'давр банк' => 'davr-bank',
        'азия альянс банк' => 'asia-alliance-bank',
        'тенге банк' => 'tenge-bank',
        'октобанк' => 'octobank',
        'хаёт банк' => 'hayotbank',
        'универсал банк' => 'universal-bank',
        'гарант банк' => 'garant-bank',
        'зираат банк' => 'ziraat-bank-uzbekistan',
        'апекс банк' => 'apex-bank',
        'опен банк' => 'openbank',
        'узум банк' => 'uzum-bank',
        'аво банк' => 'avo',
        'мадад инвест банк' => 'mybank',
        'садерат банк' => 'saderat-bank',
        'янги банк' => 'yangi-bank',
        'пойтахт банк' => 'pojtaht-bank',
    ],

];
