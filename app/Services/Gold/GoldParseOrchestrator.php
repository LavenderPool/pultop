<?php

namespace App\Services\Gold;

use App\Enums\ParseRunStatus;
use App\Models\GoldParseRun;
use App\Services\PublicCacheService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoldParseOrchestrator
{
    public function __construct(
        private readonly PultopWpGoldImporter $importer,
        private readonly GoldPriceWriter $writer,
        private readonly SettingService $settings,
        private readonly PublicCacheService $cache,
    ) {}

    public function run(): GoldParseRun
    {
        $run = GoldParseRun::query()->create([
            'status' => ParseRunStatus::Running,
            'ok_count' => 0,
            'fail_count' => 0,
            'started_at' => now(),
            'details' => [
                'source' => 'wp_gold',
                'delay_ms' => $this->settings->goldParseDelayMs(),
            ],
        ]);

        $details = [
            'source' => 'wp_gold',
            'delay_ms' => $this->settings->goldParseDelayMs(),
        ];
        $ok = 0;
        $fail = 0;
        $errors = [];

        try {
            $import = $this->importer->import();

            $ok = $import['ok_count'];
            $fail = $import['fail_count'];
            $errors = $import['errors'];
            $details['requests'] = $import['requests'];
            $details['period'] = $import['period'];
            $details['request_total'] = count($import['requests']);

            $written = $this->writer->write(
                $import['history'],
                $import['current'],
                $import['sale_points'],
            );

            $details['written'] = $written;
        } catch (Throwable $e) {
            $fail = max($fail, 1);
            $errors[] = $e->getMessage();
            $details['exception'] = $e->getMessage();
            Log::error('WP gold import failed', ['error' => $e->getMessage()]);
        }

        $status = match (true) {
            $fail === 0 && $ok > 0 => ParseRunStatus::Success,
            $ok === 0 => ParseRunStatus::Failed,
            default => ParseRunStatus::Partial,
        };

        $run->update([
            'status' => $status,
            'ok_count' => $ok,
            'fail_count' => $fail,
            'error_summary' => $errors === [] ? null : implode("\n", array_slice($errors, 0, 30)),
            'details' => $details,
            'finished_at' => now(),
        ]);

        if ($ok > 0) {
            $this->cache->forgetGroup(PublicCacheService::GROUP_GOLD);
        }

        return $run->fresh();
    }
}
