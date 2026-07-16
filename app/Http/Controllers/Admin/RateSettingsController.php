<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRateSettingsRequest;
use App\Http\Requests\Admin\UploadProxiesRequest;
use App\Models\RateParseRun;
use App\Services\ProxyService;
use App\Services\Rates\RateParseOrchestrator;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

class RateSettingsController extends Controller
{
    public function __construct(
        private readonly SettingService $settings,
        private readonly ProxyService $proxies,
    ) {}

    public function edit(): Response
    {
        $runs = RateParseRun::query()
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn (RateParseRun $run) => [
                'id' => $run->id,
                'status' => $run->status->value,
                'status_label' => $run->status->label(),
                'ok_count' => $run->ok_count,
                'fail_count' => $run->fail_count,
                'error_summary' => $run->error_summary,
                'started_at' => $run->started_at?->format('d.m.Y H:i:s'),
                'finished_at' => $run->finished_at?->format('d.m.Y H:i:s'),
            ]);

        $proxyList = $this->proxies->listForAdmin();

        return Inertia::render('admin/settings/rates', [
            'settings' => [
                'rates_parse_enabled' => $this->settings->isRatesParseEnabled(),
                'rates_parse_cron' => $this->settings->ratesParseCron(),
                'rates_parse_concurrency' => $this->settings->ratesParseConcurrency(),
                'rates_parse_delay_ms' => $this->settings->ratesParseDelayMs(),
            ],
            'cronPresets' => [
                ['value' => '0 * * * *', 'label' => 'Каждый час'],
                ['value' => '*/30 * * * *', 'label' => 'Каждые 30 минут'],
                ['value' => '0 */2 * * *', 'label' => 'Каждые 2 часа'],
                ['value' => '0 9-18 * * *', 'label' => 'Каждый час с 9:00 до 18:00'],
            ],
            'proxies' => $proxyList,
            'proxiesCount' => count($proxyList),
            'runs' => $runs,
        ]);
    }

    public function update(UpdateRateSettingsRequest $request): RedirectResponse
    {
        $this->settings->setMany([
            'rates_parse_enabled' => $request->boolean('rates_parse_enabled'),
            'rates_parse_cron' => $request->string('rates_parse_cron')->toString(),
            'rates_parse_concurrency' => (string) $request->integer('rates_parse_concurrency'),
            'rates_parse_delay_ms' => (string) $request->integer('rates_parse_delay_ms'),
        ]);

        return redirect()
            ->route('admin.settings.rates.edit')
            ->with('success', 'Настройки парсера сохранены.');
    }

    public function uploadProxies(UploadProxiesRequest $request): RedirectResponse
    {
        try {
            $result = $this->proxies->replaceFromText(
                (string) $request->input('proxies_text', ''),
            );
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('admin.settings.rates.edit')
                ->withErrors(['proxies_text' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.settings.rates.edit')
            ->with('success', "Загружено прокси: {$result['count']}.");
    }

    public function runNow(RateParseOrchestrator $orchestrator): RedirectResponse
    {
        $run = $orchestrator->run();

        return redirect()
            ->route('admin.settings.rates.edit')
            ->with('success', "Парсинг завершён: {$run->status->label()} (успешно: {$run->ok_count}, ошибок: {$run->fail_count}).");
    }
}
