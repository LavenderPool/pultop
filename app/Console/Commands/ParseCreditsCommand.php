<?php

namespace App\Console\Commands;

use App\Enums\CreditTypeSlug;
use App\Services\Credits\PultopWpCreditsImporter;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class ParseCreditsCommand extends Command
{
    protected $signature = 'credits:parse
                            {--dry-run : Только разобрать страницы, без записи в БД}
                            {--limit= : Ограничить число кредитов (для отладки)}
                            {--type= : Импортировать только один тип (slug)}
                            {--concurrency= : Число параллельных загрузок detail (по умолчанию 5)}';

    protected $description = 'Единоразовый импорт кредитов с pultop.uz (admin-ajax + детальные страницы)';

    public function handle(PultopWpCreditsImporter $importer): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limitOption = $this->option('limit');
        $limit = filled($limitOption) ? max(1, (int) $limitOption) : null;
        $concurrencyOption = $this->option('concurrency');
        $concurrency = filled($concurrencyOption) ? max(1, (int) $concurrencyOption) : null;

        $typeOption = $this->option('type');
        $type = null;
        if (filled($typeOption)) {
            $type = CreditTypeSlug::tryFrom((string) $typeOption);
            if ($type === null) {
                $this->error('Неизвестный тип кредита: '.$typeOption);
                $this->line('Допустимые: '.implode(', ', array_column(CreditTypeSlug::cases(), 'value')));

                return self::FAILURE;
            }
        }

        $this->info($dryRun
            ? 'Запуск парсинга кредитов (dry-run)…'
            : 'Запуск парсинга кредитов…');
        $this->info('Сбор списка…');

        /** @var ProgressBar|null $bar */
        $bar = null;

        $result = $importer->import(
            $dryRun,
            $limit,
            $type,
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

        foreach ($result['credits'] as $row) {
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
