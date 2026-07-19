<?php

namespace App\Console\Commands;

use App\Services\Seo\PultopWpSeoImporter;
use Illuminate\Console\Command;

class ImportSeoFromPultopCommand extends Command
{
    protected $signature = 'seo:import-from-pultop
                            {--dry-run : Только разобрать страницы, без записи в БД}';

    protected $description = 'Импорт title / description / keywords / h1 типовых страниц с pultop.uz';

    public function handle(PultopWpSeoImporter $importer): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun
            ? 'Импорт SEO (dry-run)…'
            : 'Импорт SEO с pultop.uz…');

        $result = $importer->import($dryRun);

        $this->line("Успешно: {$result['ok_count']}, ошибок: {$result['fail_count']}");

        foreach ($result['pages'] as $row) {
            $prefix = $row['ok'] ? '<info>OK</info>' : '<error>FAIL</error>';
            $this->line("{$prefix} {$row['key']}: {$row['message']}");
            if ($row['ok']) {
                $this->line('  title: '.($row['title'] ?? '—'));
                $this->line('  h1: '.($row['h1'] ?? '—'));
                $this->line('  description: '.($row['description'] ?? '—'));
                $this->line('  keywords: '.($row['keywords'] ?? '—'));
            }
        }

        return $result['fail_count'] > 0 && $result['ok_count'] === 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}
