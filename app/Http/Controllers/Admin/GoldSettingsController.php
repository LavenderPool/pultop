<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateGoldSettingsRequest;
use App\Models\GoldParseRun;
use App\Services\Gold\GoldParseOrchestrator;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GoldSettingsController extends Controller
{
    public function __construct(
        private readonly SettingService $settings,
    ) {}

    public function edit(): Response
    {
        $runs = GoldParseRun::query()
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn (GoldParseRun $run) => [
                'id' => $run->id,
                'status' => $run->status->value,
                'status_label' => $run->status->label(),
                'ok_count' => $run->ok_count,
                'fail_count' => $run->fail_count,
                'error_summary' => $run->error_summary,
                'started_at' => $run->started_at?->format('d.m.Y H:i:s'),
                'finished_at' => $run->finished_at?->format('d.m.Y H:i:s'),
            ]);

        return Inertia::render('admin/settings/gold', [
            'settings' => [
                'gold_parse_enabled' => $this->settings->isGoldParseEnabled(),
                'gold_parse_cron' => $this->settings->goldParseCron(),
                'gold_parse_delay_ms' => $this->settings->goldParseDelayMs(),
            ],
            'cronPresets' => [
                ['value' => '0 8 * * *', 'label' => 'Ежедневно в 8:00'],
                ['value' => '0 9 * * *', 'label' => 'Ежедневно в 9:00'],
                ['value' => '0 */6 * * *', 'label' => 'Каждые 6 часов'],
                ['value' => '0 * * * *', 'label' => 'Каждый час'],
            ],
            'runs' => $runs,
        ]);
    }

    public function update(UpdateGoldSettingsRequest $request): RedirectResponse
    {
        $this->settings->setMany([
            'gold_parse_enabled' => $request->boolean('gold_parse_enabled'),
            'gold_parse_cron' => $request->string('gold_parse_cron')->toString(),
            'gold_parse_delay_ms' => (string) $request->integer('gold_parse_delay_ms'),
        ]);

        return redirect()
            ->route('admin.settings.gold.edit')
            ->with('success', 'Настройки парсера золота сохранены.');
    }

    public function runNow(GoldParseOrchestrator $orchestrator): RedirectResponse
    {
        $run = $orchestrator->run();

        return redirect()
            ->route('admin.settings.gold.edit')
            ->with('success', "Парсинг золота завершён: {$run->status->label()} (успешно: {$run->ok_count}, ошибок: {$run->fail_count}).");
    }
}
