<?php

use App\Http\Controllers\Api\ExchangeRateApiController;
use App\Http\Controllers\Api\GoldChartApiController;
use App\Http\Controllers\Front\BankController;
use App\Http\Controllers\Front\BankRatingController;
use App\Http\Controllers\Front\CalculatorController;
use App\Http\Controllers\Front\ExchangeRateController;
use App\Http\Controllers\Front\GoldController;
use App\Http\Controllers\Front\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/banks', [BankController::class, 'index'])->name('banks.index');
Route::get('/banks-of-uzbekistan', [BankRatingController::class, 'show'])->name('banks.rating');
Route::get('/banks/{bank}', [BankController::class, 'show'])->name('banks.show');

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
