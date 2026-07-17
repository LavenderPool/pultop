<?php

namespace App\Console\Commands;

use App\Services\Deposits\PultopWpDepositsImporter;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class ParseDepositsCommand extends Command
{
    protected $signature = 'deposits:parse
                            {--dry-run : Только разобрать страницы, без записи в БД}
                            {--limit= : Ограничить число вкладов (для отладки)}
                            {--concurrency= : Число параллельных загрузок detail (по умолчанию 5)}';

    protected $description = 'Единоразовый импорт вкладов с pultop.uz (admin-ajax + детальные страницы)';

    public function handle(PultopWpDepositsImporter $importer): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limitOption = $this->option('limit');
        $limit = filled($limitOption) ? max(1, (int) $limitOption) : null;
        $concurrencyOption = $this->option('concurrency');
        $concurrency = filled($concurrencyOption) ? max(1, (int) $concurrencyOption) : null;

        $this->info($dryRun
            ? 'Запуск парсинга вкладов (dry-run)…'
            : 'Запуск парсинга вкладов…');
        $this->info('Сбор списка…');

        /** @var ProgressBar|null $bar */
        $bar = null;

        $result = $importer->import(
            $dryRun,
            $limit,
            onStart: function (int $total) use (&$bar): void {
                $bar = $this->output->createProgressBar($total);
                $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
                $bar->setMessage('');
                $bar->setOverwrite(false);
                $bar->start();
            },
            onItem: function (array $row) use (&$bar): void {
                $bar?->setMessage($row['slug']);
                $bar?->advance();
            },
            concurrency: $concurrency,
        );

        $bar?->finish();
        $this->newLine(2);

        $this->line("Успешно: {$result['ok_count']}, ошибок: {$result['fail_count']}");

        foreach ($result['deposits'] as $row) {
            $prefix = $row['ok'] ? '<info>OK</info>' : '<error>FAIL</error>';
            $this->line("{$prefix} {$row['slug']} — {$row['title']}: {$row['message']}");
        }

        if ($result['errors'] !== []) {
            $this->warn(implode('; ', $result['errors']));
        }

        return $result['fail_count'] > 0 && $result['ok_count'] === 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}
