<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\CacheController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\GeneralSettingsController;
use App\Http\Controllers\Admin\GoldSettingsController;
use App\Http\Controllers\Admin\RateSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('cache/clear', [CacheController::class, 'clear'])->name('cache.clear');

    Route::resource('banks', BankController::class)->except(['show']);
    Route::resource('articles', ArticleController::class)->except(['show']);
    Route::resource('credits', CreditController::class)->except(['show']);
    Route::resource('deposits', DepositController::class)->except(['show']);
    Route::resource('cards', CardController::class)->except(['show']);

    Route::get('settings/general', [GeneralSettingsController::class, 'edit'])->name('settings.general.edit');
    Route::put('settings/general', [GeneralSettingsController::class, 'update'])->name('settings.general.update');

    Route::get('settings/rates', [RateSettingsController::class, 'edit'])->name('settings.rates.edit');
    Route::put('settings/rates', [RateSettingsController::class, 'update'])->name('settings.rates.update');
    Route::post('settings/rates/proxies', [RateSettingsController::class, 'uploadProxies'])->name('settings.rates.proxies');
    Route::post('settings/rates/run', [RateSettingsController::class, 'runNow'])->name('settings.rates.run');

    Route::get('settings/gold', [GoldSettingsController::class, 'edit'])->name('settings.gold.edit');
    Route::put('settings/gold', [GoldSettingsController::class, 'update'])->name('settings.gold.update');
    Route::post('settings/gold/run', [GoldSettingsController::class, 'runNow'])->name('settings.gold.run');
});
