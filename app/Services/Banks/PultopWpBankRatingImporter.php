<?php

namespace App\Services\Banks;

use App\Models\BankRatingRow;
use App\Models\BankRatingSnapshot;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PultopWpBankRatingImporter
{
    private const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * @return array{
     *     ok: bool,
     *     dry_run: bool,
     *     as_of_date: ?string,
     *     unit: string,
     *     rows_count: int,
     *     message: string
     * }
     */
    public function import(bool $dryRun = false): array
    {
        $url = $this->ratingUrl();

        try {
            $html = $this->getHtml($url);
            $parsed = $this->parseHtml($html, $url);
        } catch (Throwable $e) {
            Log::warning('WP bank rating import failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        if ($dryRun) {
            return [
                'ok' => true,
                'dry_run' => true,
                'as_of_date' => $parsed['as_of_date']?->toDateString(),
                'unit' => $parsed['unit'],
                'rows_count' => count($parsed['rows']),
                'message' => 'dry-run OK',
            ];
        }

        DB::transaction(function () use ($parsed, $url): void {
            BankRatingSnapshot::query()->where('is_current', true)->update(['is_current' => false]);

            $snapshot = BankRatingSnapshot::query()->create([
                'as_of_date' => $parsed['as_of_date'],
                'unit' => $parsed['unit'],
                'source_url' => $url,
                'parsed_at' => now(),
                'is_current' => true,
            ]);

            foreach ($parsed['rows'] as $row) {
                $snapshot->rows()->create($row);
            }
        });

        return [
            'ok' => true,
            'dry_run' => false,
            'as_of_date' => $parsed['as_of_date']?->toDateString(),
            'unit' => $parsed['unit'],
            'rows_count' => count($parsed['rows']),
            'message' => 'OK',
        ];
    }

    /**
     * @return array{
     *     as_of_date: ?Carbon,
     *     unit: string,
     *     rows: list<array{
     *         sort_order: int,
     *         row_type: string,
     *         position: ?int,
     *         name: string,
     *         assets: ?int,
     *         loans: ?int,
     *         capital: ?int,
     *         deposits: ?int,
     *         group_key: ?string
     *     }>
     * }
     */
    private function parseHtml(string $html, string $url): array
    {
        $xpath = $this->xpath($html);
        $table = $xpath->query('//div[@id="content"]//table')->item(0);

        if (! $table instanceof DOMElement) {
            throw new RuntimeException("Таблица рейтинга не найдена на {$url}");
        }

        $asOfDate = null;
        $unit = 'млрд. сум';
        $rows = [];
        $sortOrder = 0;
        $currentGroupKey = null;
        $headerDone = false;

        $trNodes = $xpath->query('.//tr', $table);
        if ($trNodes === false || $trNodes->length === 0) {
            throw new RuntimeException('В таблице рейтинга нет строк');
        }

        foreach ($trNodes as $tr) {
            if (! $tr instanceof DOMElement) {
                continue;
            }

            $cells = $this->rowCells($tr);
            if ($cells === []) {
                continue;
            }

            $firstText = $this->normalizeText($cells[0]['text']);
            $joined = $this->normalizeText(implode(' ', array_column($cells, 'text')));

            if ($asOfDate === null && str_contains(mb_strtolower($joined), 'по состоянию на')) {
                $asOfDate = $this->parseAsOfDate($joined);

                continue;
            }

            if (str_contains(mb_strtolower($joined), 'единица измерения')) {
                if (preg_match('/единица измерения:\s*(.+)$/ui', $joined, $m)) {
                    $unit = trim($m[1]);
                }

                continue;
            }

            if (! $headerDone) {
                if ($this->isHeaderRow($cells, $firstText)) {
                    if ($this->isHeaderComplete($cells, $firstText)) {
                        $headerDone = true;
                    }

                    continue;
                }
            }

            if (! $headerDone) {
                continue;
            }

            if ($this->isSubHeaderSumRow($cells)) {
                continue;
            }

            $metrics = $this->extractMetrics($cells);

            if ($this->isTotalOrGroupRow($cells, $firstText)) {
                $name = $this->stripStrongMarkers($firstText);
                $rowType = $this->looksLikeTotal($name)
                    ? BankRatingRow::TYPE_TOTAL
                    : BankRatingRow::TYPE_GROUP;

                if ($rowType === BankRatingRow::TYPE_GROUP) {
                    $currentGroupKey = $this->resolveGroupKey($name);
                }

                $rows[] = [
                    'sort_order' => $sortOrder++,
                    'row_type' => $rowType,
                    'position' => null,
                    'name' => $name,
                    'assets' => $metrics['assets'],
                    'loans' => $metrics['loans'],
                    'capital' => $metrics['capital'],
                    'deposits' => $metrics['deposits'],
                    'group_key' => $rowType === BankRatingRow::TYPE_GROUP ? $currentGroupKey : null,
                ];

                continue;
            }

            if (preg_match('/^\d+$/u', $firstText) && count($cells) >= 6) {
                $name = $this->normalizeText($cells[1]['text']);
                if ($name === '') {
                    continue;
                }

                $rows[] = [
                    'sort_order' => $sortOrder++,
                    'row_type' => BankRatingRow::TYPE_BANK,
                    'position' => (int) $firstText,
                    'name' => $name,
                    'assets' => $metrics['assets'],
                    'loans' => $metrics['loans'],
                    'capital' => $metrics['capital'],
                    'deposits' => $metrics['deposits'],
                    'group_key' => $currentGroupKey,
                ];
            }
        }

        if ($rows === []) {
            throw new RuntimeException('Не удалось разобрать строки рейтинга банков');
        }

        return [
            'as_of_date' => $asOfDate,
            'unit' => $unit,
            'rows' => $rows,
        ];
    }

    /**
     * @return list<array{text: string, colspan: int, sheets: ?int}>
     */
    private function rowCells(DOMElement $tr): array
    {
        $cells = [];

        foreach ($tr->childNodes as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);
            if ($tag !== 'td' && $tag !== 'th') {
                continue;
            }

            $colspan = max(1, (int) $child->getAttribute('colspan'));
            $text = $this->cellText($child);
            $sheets = $this->sheetsNumber($child);

            $cells[] = [
                'text' => $text,
                'colspan' => $colspan,
                'sheets' => $sheets,
            ];
        }

        return $cells;
    }

    private function cellText(DOMElement $cell): string
    {
        return $this->normalizeText($cell->textContent);
    }

    private function sheetsNumber(DOMElement $cell): ?int
    {
        $attr = $cell->getAttribute('data-sheets-value');
        if ($attr === '') {
            return null;
        }

        $decoded = json_decode($attr, true);
        if (! is_array($decoded)) {
            return null;
        }

        if (($decoded['1'] ?? null) === 3 && isset($decoded['3']) && is_numeric($decoded['3'])) {
            return (int) round((float) $decoded['3']);
        }

        return null;
    }

    /**
     * @param  list<array{text: string, colspan: int, sheets: ?int}>  $cells
     */
    private function isHeaderRow(array $cells, string $firstText): bool
    {
        $lower = mb_strtolower($firstText);

        return $lower === '№'
            || $lower === 'no'
            || str_contains($lower, 'наименование')
            || str_contains(mb_strtolower(implode(' ', array_column($cells, 'text'))), 'актив');
    }

    /**
     * @param  list<array{text: string, colspan: int, sheets: ?int}>  $cells
     */
    private function isHeaderComplete(array $cells, string $firstText): bool
    {
        $joined = mb_strtolower(implode(' ', array_column($cells, 'text')));

        return mb_strtolower($firstText) === '№'
            || (str_contains($joined, 'актив') && str_contains($joined, 'депозит'));
    }

    /**
     * @param  list<array{text: string, colspan: int, sheets: ?int}>  $cells
     */
    private function isSubHeaderSumRow(array $cells): bool
    {
        if ($cells === []) {
            return false;
        }

        $texts = array_map(
            fn (array $cell): string => mb_strtolower($cell['text']),
            $cells
        );

        $nonEmpty = array_values(array_filter($texts, fn (string $t): bool => $t !== ''));

        return $nonEmpty !== [] && count(array_unique($nonEmpty)) === 1 && $nonEmpty[0] === 'сумма';
    }

    /**
     * @param  list<array{text: string, colspan: int, sheets: ?int}>  $cells
     */
    private function isTotalOrGroupRow(array $cells, string $firstText): bool
    {
        if ($firstText === '') {
            return false;
        }

        if (preg_match('/^\d+$/u', $firstText)) {
            return false;
        }

        $totalColspan = array_sum(array_column($cells, 'colspan'));
        $firstColspan = $cells[0]['colspan'];

        return $firstColspan >= 2 || ($totalColspan >= 5 && count($cells) <= 5);
    }

    private function looksLikeTotal(string $name): bool
    {
        $lower = mb_strtolower($name);

        return $lower === 'всего' || str_starts_with($lower, 'всего ');
    }

    private function resolveGroupKey(string $name): string
    {
        $lower = mb_strtolower($name);

        if (str_contains($lower, 'государствен')) {
            return BankRatingRow::GROUP_STATE;
        }

        return BankRatingRow::GROUP_OTHER;
    }

    /**
     * @param  list<array{text: string, colspan: int, sheets: ?int}>  $cells
     * @return array{assets: ?int, loans: ?int, capital: ?int, deposits: ?int}
     */
    private function extractMetrics(array $cells): array
    {
        $values = [];

        foreach ($cells as $index => $cell) {
            if ($index === 0 && preg_match('/^\d+$/u', $cell['text'])) {
                continue;
            }

            if ($index === 0 && $cell['colspan'] >= 2) {
                // name cell for total/group
                continue;
            }

            if ($index === 1 && preg_match('/^\d+$/u', $cells[0]['text'] ?? '')) {
                // bank name
                continue;
            }

            // Prefer visible cell text (matches WP table); sheets value can differ by 1
            $number = $this->parseNumber($cell['text']) ?? $cell['sheets'];
            if ($number !== null) {
                $values[] = $number;
            }
        }

        // Prefer last 4 numeric cells (assets, loans, capital, deposits)
        if (count($values) > 4) {
            $values = array_slice($values, -4);
        }

        return [
            'assets' => $values[0] ?? null,
            'loans' => $values[1] ?? null,
            'capital' => $values[2] ?? null,
            'deposits' => $values[3] ?? null,
        ];
    }

    private function parseNumber(string $text): ?int
    {
        $text = $this->normalizeText($text);
        $text = str_replace(["\u{00A0}", ' ', ','], '', $text);

        if ($text === '' || ! preg_match('/^-?\d+$/u', $text)) {
            return null;
        }

        return (int) $text;
    }

    private function parseAsOfDate(string $text): ?Carbon
    {
        $months = [
            'января' => 1,
            'февраля' => 2,
            'марта' => 3,
            'апреля' => 4,
            'мая' => 5,
            'июня' => 6,
            'июля' => 7,
            'августа' => 8,
            'сентября' => 9,
            'октября' => 10,
            'ноября' => 11,
            'декабря' => 12,
        ];

        if (! preg_match('/(\d{1,2})\s+([а-яё]+)\s+(\d{4})/ui', $text, $m)) {
            return null;
        }

        $monthName = mb_strtolower($m[2]);
        $month = $months[$monthName] ?? null;
        if ($month === null) {
            return null;
        }

        return Carbon::createFromDate((int) $m[3], $month, (int) $m[1])->startOfDay();
    }

    private function stripStrongMarkers(string $name): string
    {
        return $this->normalizeText($name);
    }

    private function ratingUrl(): string
    {
        return rtrim((string) config('banks.wp_base_url'), '/')
            .'/'.ltrim((string) config('banks.wp_rating_path', '/banks-of-uzbekistan/'), '/');
    }

    private function getHtml(string $url): string
    {
        $response = Http::timeout((int) config('banks.wp_timeout', 30))
            ->withHeaders(['User-Agent' => self::USER_AGENT])
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException("HTTP {$response->status()} для {$url}");
        }

        $html = $response->body();
        if ($html === '') {
            throw new RuntimeException("Пустой ответ для {$url}");
        }

        return $html;
    }

    private function xpath(string $html): DOMXPath
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        return new DOMXPath($dom);
    }

    private function normalizeText(?string $text): string
    {
        $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
