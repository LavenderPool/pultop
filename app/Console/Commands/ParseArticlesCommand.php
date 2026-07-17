<?php

namespace App\Console\Commands;

use App\Enums\ArticleCategory;
use App\Services\Articles\PultopWpArticlesImporter;
use Illuminate\Console\Command;
use ValueError;

class ParseArticlesCommand extends Command
{
    protected $signature = 'articles:parse
                            {--dry-run : Только разобрать ответы API, без записи в БД}
                            {--limit= : Ограничить число материалов (для отладки)}
                            {--category= : Категория: article, news, bank_news, events}';

    protected $description = 'Единоразовый импорт статей/новостей с pultop.uz (WP REST)';

    public function handle(PultopWpArticlesImporter $importer): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limitOption = $this->option('limit');
        $limit = filled($limitOption) ? max(1, (int) $limitOption) : null;

        $category = null;
        $categoryOption = $this->option('category');
        if (filled($categoryOption)) {
            try {
                $category = ArticleCategory::from((string) $categoryOption);
            } catch (ValueError) {
                $this->error('Неизвестная категория. Допустимо: article, news, bank_news, events');

                return self::FAILURE;
            }
        }

        $this->info($dryRun
            ? 'Запуск парсинга материалов (dry-run)…'
            : 'Запуск парсинга материалов…');

        if ($category !== null) {
            $this->line('Категория: '.$category->label());
        }

        $result = $importer->import($dryRun, $limit, $category);

        $this->line("Успешно: {$result['ok_count']}, ошибок: {$result['fail_count']}");

        foreach ($result['articles'] as $row) {
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
