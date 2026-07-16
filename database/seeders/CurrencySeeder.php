<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name_ru' => 'Доллар США', 'flag' => '🇺🇸', 'sort_order' => 1],
            ['code' => 'EUR', 'name_ru' => 'Евро', 'flag' => '🇪🇺', 'sort_order' => 2],
            ['code' => 'RUB', 'name_ru' => 'Российский рубль', 'flag' => '🇷🇺', 'sort_order' => 3],
            ['code' => 'KZT', 'name_ru' => 'Казахский тенге', 'flag' => '🇰🇿', 'sort_order' => 4],
            ['code' => 'GBP', 'name_ru' => 'Фунт стерлингов', 'flag' => '🇬🇧', 'sort_order' => 5],
            ['code' => 'CHF', 'name_ru' => 'Швейцарский франк', 'flag' => '🇨🇭', 'sort_order' => 6],
            ['code' => 'JPY', 'name_ru' => 'Иена', 'flag' => '🇯🇵', 'sort_order' => 7],
        ];

        foreach ($currencies as $currency) {
            Currency::query()->updateOrCreate(
                ['code' => $currency['code']],
                [
                    'name_ru' => $currency['name_ru'],
                    'flag' => $currency['flag'],
                    'is_active' => true,
                    'sort_order' => $currency['sort_order'],
                ],
            );
        }
    }
}
