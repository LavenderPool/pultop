<?php

namespace Database\Seeders;

use App\Models\SeoPage;
use Illuminate\Database\Seeder;

class SeoPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'home' => [
                'title' => 'Кредиты, вклады, курсы валют в Узбекистане | PulTop.Uz',
                'description' => 'Сравнение банковских вкладов, кредитов, карт. Актуальные обменные курсы валют. Быстрые расчеты выплат и доходности. Финансовый инструмент для бизнеса',
                'keywords' => null,
                'h1' => null,
            ],
            'banks.index' => [
                'title' => 'Банки Узбекистана - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Банки Узбекистана',
            ],
            'banks.rating' => [
                'title' => 'Рейтинг банков Узбекистана - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Рейтинг банков Узбекистана',
            ],
            'exchange-rates.index' => [
                'title' => 'Курс валют в банках Узбекистана - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Курс валют в банках Узбекистана',
            ],
            'exchange-rates.show.usd' => [
                'title' => 'Доллар США - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Доллар США',
            ],
            'exchange-rates.show.eur' => [
                'title' => 'Евро - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Евро',
            ],
            'exchange-rates.show.rub' => [
                'title' => 'Российский рубль - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Российский рубль',
            ],
            'exchange-rates.show.kzt' => [
                'title' => 'Казахский тенге - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Казахский тенге',
            ],
            'exchange-rates.show.gbp' => [
                'title' => 'Фунт стерлингов - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Фунт стерлингов',
            ],
            'exchange-rates.show.chf' => [
                'title' => 'Швейцарский франк - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Швейцарский франк',
            ],
            'exchange-rates.show.jpy' => [
                'title' => 'Иена - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Иена',
            ],
            'credits.index' => [
                'title' => 'Кредиты - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Кредиты',
            ],
            'credits.type.potrebitelskie-krediti' => [
                'title' => 'Потребительский - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Потребительский',
            ],
            'credits.type.avtokredity-v-uzbekistane' => [
                'title' => 'Автокредит - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Автокредит',
            ],
            'credits.type.ipotechnye-kredity-v-uzbekistane' => [
                'title' => 'Ипотека - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Ипотека',
            ],
            'credits.type.bankovskie-mikrozajmy-v-uzbekistane' => [
                'title' => 'Банковские микрозаймы - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Банковские микрозаймы',
            ],
            'credits.type.overdraft-v-uzbekistane' => [
                'title' => 'Овердрафт - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Овердрафт',
            ],
            'credits.type.biznesmenam-v-uzbekistane' => [
                'title' => 'Предпринимателям - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Предпринимателям',
            ],
            'credits.type.obrazovatelnye-kredity-v-uzbekistane' => [
                'title' => 'На образование - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'На образование',
            ],
            'credits.type.dlya-biznesa' => [
                'title' => 'Для бизнеса - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Для бизнеса',
            ],
            'deposits.index' => [
                'title' => 'Сравнение вкладов в банках Узбекистана - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Сравнение вкладов в банках Узбекистана',
            ],
            'cards.index' => [
                'title' => 'Сравнение банковских карт - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Карты',
            ],
            'gold.show' => [
                'title' => 'Стоимость золотых слитков - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Стоимость золотых слитков',
            ],
            'calculators.credit' => [
                'title' => 'Кредитный калькулятор - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Кредитный калькулятор',
            ],
            'calculators.deposit' => [
                'title' => 'Калькулятор Вкладов - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Калькулятор Вкладов',
            ],
            'calculators.mortgage' => [
                'title' => 'Калькулятор Ипотеки - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Калькулятор Ипотеки',
            ],
            'calculators.autoloan' => [
                'title' => 'Калькулятор Автокредита - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Калькулятор Автокредита',
            ],
            'calculators.vat' => [
                'title' => 'Калькулятор НДС - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Калькулятор НДС',
            ],
            'calculators.monthly' => [
                'title' => 'Расчет ежемесячного платежа по кредиту - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Расчет ежемесячного платежа по кредиту',
            ],
            'articles.index' => [
                'title' => 'Статьи и новости - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Статьи и новости',
            ],
            'articles.category.stati' => [
                'title' => 'Статьи - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Статьи',
            ],
            'articles.category.novosti' => [
                'title' => 'Новости - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Новости',
            ],
            'articles.category.novosti-bankov' => [
                'title' => 'Новости банков - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'Новости банков',
            ],
            'articles.category.sobytiya-i-akcii' => [
                'title' => 'События и Акции - Выгодные вклады, кредиты, ипотека в Узбекистане',
                'description' => null,
                'keywords' => null,
                'h1' => 'События и Акции',
            ],
        ];

        foreach ($pages as $key => $data) {
            SeoPage::query()->updateOrCreate(
                ['key' => $key],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'keywords' => $data['keywords'],
                    'h1' => $data['h1'],
                ],
            );
        }
    }
}
