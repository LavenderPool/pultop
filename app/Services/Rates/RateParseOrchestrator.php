<?php

namespace App\Services\Rates;

use App\Enums\ParseRunStatus;
use App\Models\Bank;
use App\Models\RateParseRun;
use App\Services\SettingService;
use Illuminate\Support\Facades\Log;
use Throwable;

class RateParseOrchestrator
{
    public function __construct(
        private readonly PultopWpRatesImporter $importer,
        private readonly BankRateWriter $writer,
        private readonly SettingService $settings,
    ) {}

    public function run(): RateParseRun
    {
        $run = RateParseRun::query()->create([
            'status' => ParseRunStatus::Running,
            'ok_count' => 0,
            'fail_count' => 0,
            'started_at' => now(),
            'details' => [
                'source' => 'wp_admin_ajax',
                'delay_ms' => $this->settings->ratesParseDelayMs(),
            ],
        ]);

        $details = [
            'source' => 'wp_admin_ajax',
            'delay_ms' => $this->settings->ratesParseDelayMs(),
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
            $details['request_total'] = count($import['requests']);

            /** @var array<int, Bank> $banks */
            $banks = $import['banks'];
            $writtenTotal = 0;
            $bankDetails = [];

            foreach ($import['by_bank'] as $bankId => $rates) {
                $bank = $banks[$bankId] ?? Bank::query()->find($bankId);

                if ($bank === null) {
                    continue;
                }

                $written = $this->writer->write($bank, $rates);
                $writtenTotal += $written;
                $bankDetails[$bank->slug] = [
                    'ok' => true,
                    'bank_name' => $bank->name,
                    'written' => $written,
                ];
            }

            $details['banks'] = $bankDetails;
            $details['written_total'] = $writtenTotal;
        } catch (Throwable $e) {
            $fail = max($fail, 1);
            $errors[] = $e->getMessage();
            $details['exception'] = $e->getMessage();
            Log::error('WP rates import failed', ['error' => $e->getMessage()]);
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

        return $run->fresh();
    }
}
