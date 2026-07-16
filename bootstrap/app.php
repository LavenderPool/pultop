<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Services\SettingService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withSchedule(function (Schedule $schedule): void {
        $cron = '0 * * * *';

        try {
            $cron = app(SettingService::class)->ratesParseCron();
        } catch (Throwable) {
            // settings table may be unavailable during early migrate
        }

        $schedule->command('rates:parse')
            ->cron($cron)
            ->when(function () {
                try {
                    return app(SettingService::class)->isRatesParseEnabled();
                } catch (Throwable) {
                    return true;
                }
            })
            ->withoutOverlapping()
            ->name('rates-parse');

        $goldCron = '0 8 * * *';

        try {
            $goldCron = app(SettingService::class)->goldParseCron();
        } catch (Throwable) {
            // settings table may be unavailable during early migrate
        }

        $schedule->command('gold:parse')
            ->cron($goldCron)
            ->when(function () {
                try {
                    return app(SettingService::class)->isGoldParseEnabled();
                } catch (Throwable) {
                    return true;
                }
            })
            ->withoutOverlapping()
            ->name('gold-parse');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => route('admin.login'));
        $middleware->redirectUsersTo(fn (Request $request) => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
