<?php

namespace App\Console\Commands;

use App\Services\Banks\PultopWpBanksImporter;
use Illuminate\Console\Command;

class ParseBanksCommand extends Command
{
    protected $signature = 'banks:parse
                            {--dry-run : Только разобрать страницы, без записи в БД}
                            {--limit= : Ограничить число банков (для отладки)}';

    protected $description = 'Единоразовый импорт банков и логотипов с pultop.uz/banks/';

    public function handle(PultopWpBanksImporter $importer): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limitOption = $this->option('limit');
        $limit = filled($limitOption) ? max(1, (int) $limitOption) : null;

        $this->info($dryRun
            ? 'Запуск парсинга банков (dry-run)…'
            : 'Запуск парсинга банков…');

        $result = $importer->import($dryRun, $limit);

        $this->line("Успешно: {$result['ok_count']}, ошибок: {$result['fail_count']}");

        foreach ($result['banks'] as $row) {
            $prefix = $row['ok'] ? '<info>OK</info>' : '<error>FAIL</error>';
            $this->line("{$prefix} {$row['slug']} — {$row['name']}: {$row['message']}");
        }

        if ($result['errors'] !== []) {
            $this->warn(implode('; ', $result['errors']));
        }

        return $result['fail_count'] > 0 && $result['ok_count'] === 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}
