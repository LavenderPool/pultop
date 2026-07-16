<?php

namespace App\Console\Commands;

use App\Services\Rates\RateParseOrchestrator;
use Illuminate\Console\Command;

class ParseExchangeRatesCommand extends Command
{
    protected $signature = 'rates:parse';

    protected $description = 'Импорт курсов банков из WordPress admin-ajax';

    public function handle(RateParseOrchestrator $orchestrator): int
    {
        $this->info('Запуск парсинга курсов…');

        $run = $orchestrator->run();

        $this->line("Статус: {$run->status->label()}");
        $this->line("Успешно: {$run->ok_count}, ошибок: {$run->fail_count}");

        if ($run->error_summary) {
            $this->warn($run->error_summary);
        }

        return $run->fail_count > 0 && $run->ok_count === 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}
