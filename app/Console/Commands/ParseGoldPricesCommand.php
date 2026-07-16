<?php

namespace App\Console\Commands;

use App\Services\Gold\GoldParseOrchestrator;
use Illuminate\Console\Command;

class ParseGoldPricesCommand extends Command
{
    protected $signature = 'gold:parse';

    protected $description = 'Импорт цен золота и мест продаж из WordPress';

    public function handle(GoldParseOrchestrator $orchestrator): int
    {
        $this->info('Запуск парсинга золота…');

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
