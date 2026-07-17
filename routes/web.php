<?php

use App\Http\Controllers\Api\DepositApiController;
use App\Http\Controllers\Api\ExchangeRateApiController;
use App\Http\Controllers\Api\GoldChartApiController;
use App\Http\Controllers\Front\ArticleController;
use App\Http\Controllers\Front\BankController;
use App\Http\Controllers\Front\BankRatingController;
use App\Http\Controllers\Front\CalculatorController;
use App\Http\Controllers\Front\CardController;
use App\Http\Controllers\Front\CreditController;
use App\Http\Controllers\Front\DepositController;
use App\Http\Controllers\Front\ExchangeRateController;
use App\Http\Controllers\Front\GoldController;
use App\Http\Controllers\Front\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/category/{category}', [ArticleController::class, 'category'])
    ->where('category', 'stati|novosti|novosti-bankov|sobytiya-i-akcii')
    ->name('articles.category');

Route::get('/banks', [BankController::class, 'index'])->name('banks.index');
Route::get('/banks-of-uzbekistan', [BankRatingController::class, 'show'])->name('banks.rating');
Route::get('/banks/{bank}', [BankController::class, 'show'])->name('banks.show');

Route::get('/credits', [CreditController::class, 'index'])->name('credits.index');
Route::get('/vse-kredity-uzbekistana', [CreditController::class, 'index'])->name('credits.all');
Route::get('/credit-type/{type}', [CreditController::class, 'byType'])
    ->where('type', 'avtokredity-v-uzbekistane|dlya-biznesa|ipotechnye-kredity-v-uzbekistane|bankovskie-mikrozajmy-v-uzbekistane|obrazovatelnye-kredity-v-uzbekistane|overdraft-v-uzbekistane|potrebitelskie-krediti|biznesmenam-v-uzbekistane')
    ->name('credits.type');
Route::get('/credits/{credit}', [CreditController::class, 'show'])->name('credits.show');

Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
Route::get('/sravnenie-bankovskih-kart', [CardController::class, 'index'])->name('cards.alias');
Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');

Route::redirect('/vkladi', '/deposits', 301);
Route::redirect('/vkladi/', '/deposits', 301);
Route::get('/deposits', [DepositController::class, 'index'])->name('deposits.index');
Route::get('/deposits/{deposit}', [DepositController::class, 'show'])->name('deposits.show');

$creditTypeAliases = [
    'potrebitelskie-krediti',
    'avtokredity-v-uzbekistane',
    'ipotechnye-kredity-v-uzbekistane',
    'bankovskie-mikrozajmy-v-uzbekistane',
    'overdraft-v-uzbekistane',
    'kredity-nachinayushhim-biznesmenam-v-uzbekistane',
    'obrazovatelnye-kredity-v-uzbekistane',
    'dlya-biznesa',
    'biznesmenam-v-uzbekistane',
];
foreach ($creditTypeAliases as $alias) {
    Route::get('/'.$alias, [CreditController::class, 'byType'])
        ->defaults('type', $alias)
        ->name('credits.alias.'.$alias);
}

Route::get('/kurs-obmena-valyut', [ExchangeRateController::class, 'index'])
    ->name('exchange-rates.index');

Route::get('/kurs-obmena-valyut/{currency}', [ExchangeRateController::class, 'show'])
    ->where('currency', '[A-Za-z]{3}')
    ->name('exchange-rates.show');

Route::get('/gold-stat', [GoldController::class, 'show'])->name('gold.show');

Route::get('/kreditnyj-kalkulyator', [CalculatorController::class, 'credit'])
    ->name('calculators.credit');
Route::get('/kalkulyator-vkladov', [CalculatorController::class, 'deposit'])
    ->name('calculators.deposit');
Route::get('/kalkulyatr-ipoteki', [CalculatorController::class, 'mortgage'])
    ->name('calculators.mortgage');
Route::get('/kalkulyator-avtokredita', [CalculatorController::class, 'autoloan'])
    ->name('calculators.autoloan');
Route::get('/kalkulyator-nds', [CalculatorController::class, 'vat'])
    ->name('calculators.vat');
Route::get('/raschet-ezhemesyachnogo-platezha-po-kreditu', [CalculatorController::class, 'monthly'])
    ->name('calculators.monthly');

Route::get('/api/rates', ExchangeRateApiController::class)
    ->name('api.rates');

Route::get('/api/gold-chart', GoldChartApiController::class)
    ->name('api.gold-chart');

Route::get('/api/deposits', DepositApiController::class)
    ->name('api.deposits');
