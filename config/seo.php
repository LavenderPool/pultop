<?php

$base = rtrim((string) env('SEO_WP_BASE_URL', env('RATES_WP_BASE_URL', 'https://pultop.uz')), '/');

$currencies = [
    'usd' => 'Доллар США',
    'eur' => 'Евро',
    'rub' => 'Российский рубль',
    'kzt' => 'Казахский тенге',
    'gbp' => 'Фунт стерлингов',
    'chf' => 'Швейцарский франк',
    'jpy' => 'Иена',
];

$creditTypes = [
    'potrebitelskie-krediti' => 'Потребительский',
    'avtokredity-v-uzbekistane' => 'Автокредит',
    'ipotechnye-kredity-v-uzbekistane' => 'Ипотека',
    'bankovskie-mikrozajmy-v-uzbekistane' => 'Банковские микрозаймы',
    'overdraft-v-uzbekistane' => 'Овердрафт',
    'biznesmenam-v-uzbekistane' => 'Предпринимателям',
    'obrazovatelnye-kredity-v-uzbekistane' => 'На образование',
    'dlya-biznesa' => 'Для бизнеса',
];

$articleCategories = [
    'stati' => 'Статьи',
    'novosti' => 'Новости',
    'novosti-bankov' => 'Новости банков',
    'sobytiya-i-akcii' => 'События и Акции',
];

$pages = [
    'home' => [
        'label' => 'Главная',
        'source_path' => '/',
    ],
    'banks.index' => [
        'label' => 'Банки',
        'source_path' => '/banks/',
    ],
    'banks.rating' => [
        'label' => 'Рейтинг банков',
        'source_path' => '/banks-of-uzbekistan/',
    ],
    'exchange-rates.index' => [
        'label' => 'Курс валют',
        'source_path' => '/kurs-obmena-valyut/',
    ],
    'credits.index' => [
        'label' => 'Кредиты',
        'source_path' => '/vse-kredity-uzbekistana/',
    ],
    'deposits.index' => [
        'label' => 'Вклады',
        'source_path' => '/vkladi/',
    ],
    'cards.index' => [
        'label' => 'Карты',
        'source_path' => '/sravnenie-bankovskih-kart/',
    ],
    'gold.show' => [
        'label' => 'Золото',
        'source_path' => '/gold-stat/',
    ],
    'calculators.credit' => [
        'label' => 'Кредитный калькулятор',
        'source_path' => '/kreditnyj-kalkulyator/',
    ],
    'calculators.deposit' => [
        'label' => 'Калькулятор вкладов',
        'source_path' => '/kalkulyator-vkladov/',
    ],
    'calculators.mortgage' => [
        'label' => 'Калькулятор ипотеки',
        'source_path' => '/kalkulyatr-ipoteki/',
    ],
    'calculators.autoloan' => [
        'label' => 'Калькулятор автокредита',
        'source_path' => '/kalkulyator-avtokredita/',
    ],
    'calculators.vat' => [
        'label' => 'Калькулятор НДС',
        'source_path' => '/kalkulyator-nds/',
    ],
    'calculators.monthly' => [
        'label' => 'Расчёт ежемесячного платежа',
        'source_path' => '/raschet-ezhemesyachnogo-platezha-po-kreditu/',
    ],
    'articles.index' => [
        'label' => 'Статьи и новости',
        'source_path' => '/articles/',
    ],
];

foreach ($currencies as $code => $label) {
    $pages['exchange-rates.show.'.$code] = [
        'label' => 'Курс: '.$label,
        'source_path' => '/kurs-obmena-valyut/'.$code.'/',
    ];
}

foreach ($creditTypes as $slug => $label) {
    $sourceSlug = $slug === 'biznesmenam-v-uzbekistane'
        ? 'kredity-nachinayushhim-biznesmenam-v-uzbekistane'
        : $slug;

    $pages['credits.type.'.$slug] = [
        'label' => 'Кредиты: '.$label,
        'source_path' => '/'.$sourceSlug.'/',
    ];
}

foreach ($articleCategories as $slug => $label) {
    $pages['articles.category.'.$slug] = [
        'label' => 'Категория: '.$label,
        'source_path' => '/category/'.$slug.'/',
    ];
}

return [

    'wp_base_url' => $base,

    'wp_timeout' => (int) env('SEO_WP_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Каталог типовых страниц (ключ → лейбл + путь на pultop.uz)
    |--------------------------------------------------------------------------
    */
    'pages' => $pages,

];
