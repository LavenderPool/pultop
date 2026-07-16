<?php

namespace App\Services\Gold;

use App\Enums\GoldWeight;
use App\Models\GoldPriceHistory;
use App\Services\Gold\Dto\ParsedGoldCurrentPrice;
use App\Services\Gold\Dto\ParsedGoldHistoryPoint;
use App\Services\Gold\Dto\ParsedGoldSalePoint;
use App\Services\SettingService;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PultopWpGoldImporter
{
    private const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    private ?string $nonce = null;

    private ?string $goldStatHtml = null;

    public function __construct(
        private readonly SettingService $settings,
    ) {}

    /**
     * @return array{
     *     ok_count: int,
     *     fail_count: int,
     *     errors: list<string>,
     *     requests: list<array{step: string, ok: bool, message: string}>,
     *     history: list<ParsedGoldHistoryPoint>,
     *     current: list<ParsedGoldCurrentPrice>,
     *     sale_points: list<ParsedGoldSalePoint>,
     *     period: int
     * }
     */
    public function import(): array
    {
        $ok = 0;
        $fail = 0;
        $errors = [];
        $requests = [];
        $history = [];
        $current = [];
        $salePoints = [];

        $period = GoldPriceHistory::query()->exists() ? 7 : 90;
        $delayMs = $this->settings->goldParseDelayMs();
        $requestIndex = 0;

        try {
            $this->loadGoldStatPage();
            $this->extractNonce($this->goldStatHtml ?? '');
            $salePoints = $this->parseSalePoints($this->goldStatHtml ?? '');
            $ok++;
            $requests[] = [
                'step' => 'gold_stat_page',
                'ok' => true,
                'message' => 'OK, точек продаж: '.count($salePoints),
            ];
        } catch (Throwable $e) {
            $fail++;
            $errors[] = 'gold-stat: '.$e->getMessage();
            $requests[] = [
                'step' => 'gold_stat_page',
                'ok' => false,
                'message' => $e->getMessage(),
            ];
            Log::warning('WP gold-stat page failed', ['error' => $e->getMessage()]);
        }

        foreach (GoldWeight::ordered() as $weight) {
            if ($requestIndex > 0 && $delayMs > 0) {
                usleep($delayMs * 1000);
            }
            $requestIndex++;

            try {
                $points = $this->fetchChartHistory($weight, $period, retryNonce: true);
                $history = array_merge($history, $points);
                $ok++;
                $requests[] = [
                    'step' => 'chart_'.$weight->value.'g',
                    'ok' => true,
                    'message' => 'OK, точек: '.count($points),
                ];
            } catch (Throwable $e) {
                $fail++;
                $errors[] = sprintf('chart %sg: %s', $weight->value, $e->getMessage());
                $requests[] = [
                    'step' => 'chart_'.$weight->value.'g',
                    'ok' => false,
                    'message' => $e->getMessage(),
                ];
                Log::warning('WP gold chart failed', [
                    'weight' => $weight->value,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($delayMs > 0) {
            usleep($delayMs * 1000);
        }

        try {
            $current = $this->fetchCurrentFromHomepage();
            $ok++;
            $requests[] = [
                'step' => 'homepage_table',
                'ok' => true,
                'message' => 'OK, номиналов: '.count($current),
            ];
        } catch (Throwable $e) {
            $fail++;
            $errors[] = 'homepage: '.$e->getMessage();
            $requests[] = [
                'step' => 'homepage_table',
                'ok' => false,
                'message' => $e->getMessage(),
            ];
            Log::warning('WP homepage gold table failed', ['error' => $e->getMessage()]);
        }

        if ($current === [] && $history !== []) {
            $current = $this->currentFromLatestHistory($history);
        }

        return [
            'ok_count' => $ok,
            'fail_count' => $fail,
            'errors' => $errors,
            'requests' => $requests,
            'history' => $history,
            'current' => $current,
            'sale_points' => $salePoints,
            'period' => $period,
        ];
    }

    private function loadGoldStatPage(): void
    {
        $baseUrl = (string) config('gold.wp_base_url');
        $path = (string) config('gold.wp_gold_stat_path', '/gold-stat/');
        $url = $baseUrl.'/'.ltrim($path, '/');
        $timeout = max(5, (int) config('gold.wp_timeout', 30));

        $response = Http::timeout($timeout)
            ->withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html',
            ])
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Не удалось загрузить /gold-stat/ (HTTP '.$response->status().')');
        }

        $this->goldStatHtml = $response->body();
    }

    private function extractNonce(string $html): void
    {
        if (preg_match('/pultop_chart_ajax\s*=\s*\{[^}]*"nonce"\s*:\s*"([^"]+)"/', $html, $m)) {
            $this->nonce = $m[1];

            return;
        }

        throw new RuntimeException('Nonce pultop_chart_ajax не найден на /gold-stat/');
    }

    /**
     * @return list<ParsedGoldHistoryPoint>
     */
    private function fetchChartHistory(GoldWeight $weight, int $period, bool $retryNonce): array
    {
        if ($this->nonce === null || $this->nonce === '') {
            $this->loadGoldStatPage();
            $this->extractNonce($this->goldStatHtml ?? '');
        }

        $baseUrl = (string) config('gold.wp_base_url');
        $ajaxUrl = $baseUrl.(string) config('gold.wp_ajax_path');
        $timeout = max(5, (int) config('gold.wp_timeout', 30));

        $response = Http::timeout($timeout)
            ->withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'application/json, */*;q=0.8',
                'X-Requested-With' => 'XMLHttpRequest',
                'Origin' => $baseUrl,
                'Referer' => $baseUrl.'/gold-stat/',
            ])
            ->asForm()
            ->post($ajaxUrl, [
                'action' => 'pul_get_gold_chart',
                'nonce' => $this->nonce,
                'gold' => (string) $weight->wpIndex(),
                'period' => (string) $period,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('HTTP '.$response->status());
        }

        $body = $response->body();

        if ($this->isInvalidAuthResponse($body)) {
            if (! $retryNonce) {
                throw new RuntimeException(trim($body) !== '' ? trim($body) : 'Invalid request');
            }

            $this->loadGoldStatPage();
            $this->extractNonce($this->goldStatHtml ?? '');

            return $this->fetchChartHistory($weight, $period, retryNonce: false);
        }

        $json = $response->json();

        if (! is_array($json) || ! isset($json['rows']) || ! is_array($json['rows'])) {
            throw new RuntimeException('Некорректный JSON ответа chart');
        }

        $points = [];

        foreach ($json['rows'] as $row) {
            if (! is_array($row)) {
                continue;
            }

            $dateRaw = (string) ($row['date'] ?? '');
            $price = $this->decimal($row['price'] ?? null);
            $diff = $this->decimal($row['diff'] ?? null);

            if ($dateRaw === '' || $price === null) {
                continue;
            }

            try {
                $date = Carbon::createFromFormat('d.m.Y', $dateRaw)->startOfDay();
            } catch (Throwable) {
                continue;
            }

            $points[] = new ParsedGoldHistoryPoint($weight, $date, $price, $diff);
        }

        if ($points === []) {
            throw new RuntimeException('Пустой список точек истории');
        }

        return $points;
    }

    /**
     * @return list<ParsedGoldCurrentPrice>
     */
    private function fetchCurrentFromHomepage(): array
    {
        $baseUrl = (string) config('gold.wp_base_url');
        $timeout = max(5, (int) config('gold.wp_timeout', 30));

        $response = Http::timeout($timeout)
            ->withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html',
            ])
            ->get($baseUrl.'/');

        if (! $response->successful()) {
            throw new RuntimeException('Не удалось загрузить главную (HTTP '.$response->status().')');
        }

        return $this->parseHomepageGoldTable($response->body());
    }

    /**
     * @return list<ParsedGoldCurrentPrice>
     */
    private function parseHomepageGoldTable(string $html): array
    {
        $pricedOn = null;

        if (preg_match(
            '/Дата обновления:\s*(\d{2}\.\d{2}\.\d{4}).{0,800}?class=["\'][^"\']*gold-table/su',
            $html,
            $m,
        ) || preg_match('/Дата обновления:\s*(\d{2}\.\d{2}\.\d{4})/u', $html, $m)) {
            try {
                $pricedOn = Carbon::createFromFormat('d.m.Y', $m[1])->startOfDay();
            } catch (Throwable) {
                $pricedOn = null;
            }
        }

        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new DOMXPath($dom);
        $rows = $xpath->query('//a[contains(@class,"gold-table-link")]//table[contains(@class,"gold-table")]//tbody/tr');

        if ($rows === false || $rows->length === 0) {
            $rows = $xpath->query('//table[contains(concat(" ", normalize-space(@class), " "), " gold-table ")]//tbody/tr');
        }

        if ($rows === false || $rows->length === 0) {
            throw new RuntimeException('Таблица .gold-table не найдена на главной');
        }

        $items = [];

        foreach ($rows as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }

            $weightText = trim(preg_replace('/\s+/u', ' ', $row->textContent) ?? '');
            $weight = null;

            if (preg_match('/(\d+)\s*грамм/u', $weightText, $wm)) {
                $weight = GoldWeight::tryFrom((int) $wm[1]);
            }

            if ($weight === null) {
                continue;
            }

            $sellNode = $xpath->query('.//td[contains(@class,"sale-price")]//div[contains(@class,"price")]', $row)->item(0);
            $buybackGoodNode = $xpath->query('.//td[contains(@class,"buyback-good")]//div[contains(@class,"price")]', $row)->item(0);
            $buybackDamagedNode = $xpath->query('.//td[contains(@class,"buyback-damaged")]//div[contains(@class,"price")]', $row)->item(0);
            $badgeNode = $xpath->query('.//td[contains(@class,"sale-price")]//span[contains(@class,"price-badge")]', $row)->item(0);

            $sell = $this->decimal($sellNode?->textContent);
            $buybackGood = $this->decimal($buybackGoodNode?->textContent);
            $buybackDamaged = $this->decimal($buybackDamagedNode?->textContent);
            $diff = $this->decimal($badgeNode?->textContent);

            if ($sell === null) {
                continue;
            }

            $items[] = new ParsedGoldCurrentPrice(
                $weight,
                $sell,
                $buybackGood,
                $buybackDamaged,
                $diff,
                $pricedOn,
            );
        }

        if ($items === []) {
            throw new RuntimeException('Не удалось разобрать строки таблицы золота');
        }

        return $items;
    }

    /**
     * @return list<ParsedGoldSalePoint>
     */
    private function parseSalePoints(string $html): array
    {
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new DOMXPath($dom);
        $rows = $xpath->query('//table[contains(@class,"gold-place-sale")]//tr[@region]');

        if ($rows === false) {
            return [];
        }

        $points = [];
        $sort = 0;

        foreach ($rows as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }

            $region = trim($row->getAttribute('region'));
            $cells = $xpath->query('./td', $row);

            if ($region === '' || $cells === false || $cells->length < 2) {
                continue;
            }

            $bankName = $this->normalizeCellHtml($cells->item(0));
            $address = $this->normalizeCellHtml($cells->item(1));
            $phone = $cells->length >= 3
                ? $this->normalizeCellHtml($cells->item(2))
                : '';

            if ($bankName === '' || $address === '') {
                continue;
            }

            $points[] = new ParsedGoldSalePoint(
                $region,
                $bankName,
                $address,
                $phone !== '' ? $phone : null,
                $sort++,
            );
        }

        return $points;
    }

    /**
     * @param  list<ParsedGoldHistoryPoint>  $history
     * @return list<ParsedGoldCurrentPrice>
     */
    private function currentFromLatestHistory(array $history): array
    {
        /** @var array<int, ParsedGoldHistoryPoint> $latest */
        $latest = [];

        foreach ($history as $point) {
            $key = $point->weight->value;
            $existing = $latest[$key] ?? null;

            if ($existing === null || $point->priceDate->gt($existing->priceDate)) {
                $latest[$key] = $point;
            }
        }

        $items = [];

        foreach ($latest as $point) {
            $items[] = new ParsedGoldCurrentPrice(
                $point->weight,
                $point->price,
                null,
                null,
                $point->diff,
                $point->priceDate,
            );
        }

        return $items;
    }

    private function isInvalidAuthResponse(string $body): bool
    {
        $trimmed = trim($body);

        return $trimmed === 'Invalid referer'
            || $trimmed === 'Invalid request'
            || $trimmed === '0'
            || str_starts_with($trimmed, 'Invalid request');
    }

    private function normalizeCellHtml(?\DOMNode $node): string
    {
        if ($node === null) {
            return '';
        }

        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument?->saveHTML($child) ?? '';
        }

        $html = preg_replace('/<br\s*\/?>/iu', ' ', $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function decimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = str_replace([' ', ',', "\xc2\xa0", 'сум', 'SUM'], ['', '.', '', '', ''], (string) $value);
        $normalized = preg_replace('/[^\d.\-]/', '', $normalized) ?? $normalized;

        if ($normalized === '' || ! is_numeric($normalized)) {
            return null;
        }

        return number_format((float) $normalized, 2, '.', '');
    }
}
