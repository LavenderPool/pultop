<?php

namespace App\Console\Commands;

use App\Services\Banks\PultopWpBankRatingImporter;
use Illuminate\Console\Command;
use Throwable;

class ParseBankRatingCommand extends Command
{
    protected $signature = 'banks:parse-rating
                            {--dry-run : Только разобрать страницу, без записи в БД}';

    protected $description = 'Импорт рейтинга банков с pultop.uz/banks-of-uzbekistan/';

    public function handle(PultopWpBankRatingImporter $importer): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun
            ? 'Запуск парсинга рейтинга банков (dry-run)…'
            : 'Запуск парсинга рейтинга банков…');

        try {
            $result = $importer->import($dryRun);
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $asOf = $result['as_of_date'] ?? '—';
        $this->line("Дата отчёта: {$asOf}");
        $this->line("Единица: {$result['unit']}");
        $this->line("Строк: {$result['rows_count']}");
        $this->info($result['message']);

        return self::SUCCESS;
    }
}
