<?php

namespace App\Services\Rates;

use App\Enums\RateOperation;
use App\Enums\RatePlace;
use App\Models\Bank;
use App\Models\Currency;
use App\Services\Rates\Dto\ParsedBankRate;
use App\Services\SettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PultopWpRatesImporter
{
    private const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    private ?string $nonce = null;

    public function __construct(
        private readonly SettingService $settings,
    ) {}

    /**
     * @return array{
     *     ok_count: int,
     *     fail_count: int,
     *     errors: list<string>,
     *     requests: list<array{currency: string, operation: string, place: string, ok: bool, message: string, banks?: int}>,
     *     by_bank: array<int, list<ParsedBankRate>>,
     *     banks: array<int, Bank>
     * }
     */
    public function import(): array
    {
        $currencies = Currency::query()->active()->ordered()->pluck('code')->all();

        if ($currencies === []) {
            throw new RuntimeException('Нет активных валют для импорта');
        }

        $this->refreshNonce(strtolower((string) $currencies[0]));

        /** @var array<string, array{buy: ?string, sell: ?string}> $cells */
        $cells = [];
        /** @var array<string, Bank> $banksByKey */
        $banksByKey = [];

        $ok = 0;
        $fail = 0;
        $errors = [];
        $requests = [];
        $delayMs = $this->settings->ratesParseDelayMs();
        $requestIndex = 0;

        foreach ($currencies as $currencyCode) {
            $currency = strtolower((string) $currencyCode);

            foreach (RatePlace::cases() as $place) {
                foreach (RateOperation::cases() as $operation) {
                    if ($requestIndex > 0 && $delayMs > 0) {
                        usleep($delayMs * 1000);
                    }
                    $requestIndex++;

                    $result = $this->fetchRates($currency, $operation, $place);

                    $requests[] = [
                        'currency' => strtoupper($currency),
                        'operation' => $operation->value,
                        'place' => $place->value,
                        'ok' => $result['ok'],
                        'message' => $result['message'],
                        'banks' => count($result['items']),
                    ];

                    if (! $result['ok']) {
                        $fail++;
                        $errors[] = sprintf(
                            '%s/%s/%s: %s',
                            strtoupper($currency),
                            $operation->value,
                            $place->value,
                            $result['message'],
                        );

                        continue;
                    }

                    $ok++;

                    foreach ($result['items'] as $item) {
                        $bank = $this->resolveBank($item['name']);
                        $bankId = (int) $bank->id;
                        $banksByKey[$bankId] = $bank;

                        $cellKey = $bankId.'|'.strtoupper($currency).'|'.$place->value;

                        if (! isset($cells[$cellKey])) {
                            $cells[$cellKey] = ['buy' => null, 'sell' => null];
                        }

                        // WP operation=buy («Я покупаю») → sell банка; sell → buy.
                        if ($operation === RateOperation::Buy) {
                            $cells[$cellKey]['sell'] = $item['rate'];
                        } else {
                            $cells[$cellKey]['buy'] = $item['rate'];
                        }
                    }
                }
            }
        }

        $byBank = [];

        foreach ($cells as $cellKey => $pair) {
            [$bankId, $code, $placeValue] = explode('|', $cellKey, 3);
            $place = RatePlace::from($placeValue);

            if ($pair['buy'] === null && $pair['sell'] === null) {
                continue;
            }

            $byBank[(int) $bankId][] = new ParsedBankRate(
                $code,
                $place,
                $pair['buy'],
                $pair['sell'],
            );
        }

        return [
            'ok_count' => $ok,
            'fail_count' => $fail,
            'errors' => $errors,
            'requests' => $requests,
            'by_bank' => $byBank,
            'banks' => $banksByKey,
        ];
    }

    /**
     * @return array{ok: bool, message: string, items: list<array{name: string, rate: string}>}
     */
    private function fetchRates(string $currency, RateOperation $operation, RatePlace $place): array
    {
        try {
            $html = $this->postFetchRates($currency, $operation, $place, retryNonce: true);
        } catch (Throwable $e) {
            Log::warning('WP fetch_rates failed', [
                'currency' => $currency,
                'operation' => $operation->value,
                'place' => $place->value,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'message' => $e->getMessage(),
                'items' => [],
            ];
        }

        $items = $this->parseRatesHtml($html);

        if ($items === []) {
            return [
                'ok' => false,
                'message' => 'Пустой ответ или не удалось разобрать HTML',
                'items' => [],
            ];
        }

        return [
            'ok' => true,
            'message' => 'OK',
            'items' => $items,
        ];
    }

    private function postFetchRates(
        string $currency,
        RateOperation $operation,
        RatePlace $place,
        bool $retryNonce,
    ): string {
        if ($this->nonce === null || $this->nonce === '') {
            $this->refreshNonce($currency);
        }

        $baseUrl = (string) config('rates.wp_base_url');
        $ajaxUrl = $baseUrl.(string) config('rates.wp_ajax_path');
        $referer = $baseUrl.'/kurs-obmena-valyut/'.$currency.'/';
        $timeout = max(5, (int) config('rates.wp_timeout', 30));

        $response = Http::timeout($timeout)
            ->withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html, */*;q=0.8',
                'X-Requested-With' => 'XMLHttpRequest',
                'Origin' => $baseUrl,
                'Referer' => $referer,
            ])
            ->asForm()
            ->post($ajaxUrl, [
                'action' => 'fetch_rates',
                'nonce' => $this->nonce,
                'currency' => $currency,
                'operation' => $operation->value,
                'place' => $place->value,
                'amount' => '',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('HTTP '.$response->status());
        }

        $body = $response->body();

        if ($this->isInvalidAuthResponse($body)) {
            if (! $retryNonce) {
                throw new RuntimeException(trim($body) !== '' ? trim($body) : 'Invalid request');
            }

            $this->refreshNonce($currency);

            return $this->postFetchRates($currency, $operation, $place, retryNonce: false);
        }

        return $body;
    }

    private function isInvalidAuthResponse(string $body): bool
    {
        $trimmed = trim($body);

        return $trimmed === 'Invalid referer'
            || $trimmed === 'Invalid request'
            || str_starts_with($trimmed, 'Invalid request');
    }

    private function refreshNonce(string $currency): void
    {
        $baseUrl = (string) config('rates.wp_base_url');
        $url = $baseUrl.'/kurs-obmena-valyut/'.$currency.'/';
        $timeout = max(5, (int) config('rates.wp_timeout', 30));

        $response = Http::timeout($timeout)
            ->withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html',
            ])
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Не удалось загрузить страницу для nonce (HTTP '.$response->status().')');
        }

        if (! preg_match('/name=["\']nonce["\']\s+value=["\']([^"\']+)["\']/', $response->body(), $m)
            && ! preg_match('/value=["\']([^"\']+)["\']\s+name=["\']nonce["\']/', $response->body(), $m)) {
            throw new RuntimeException('Nonce не найден на странице курсов');
        }

        $this->nonce = $m[1];
    }

    /**
     * @return list<array{name: string, rate: string}>
     */
    private function parseRatesHtml(string $html): array
    {
        if ($html === '' || ! str_contains($html, 'FinanceItem-HeaderTitle')) {
            return [];
        }

        if (! preg_match_all(
            '/FinanceItem-HeaderTitle">\s*(.*?)\s*<\/h3>.*?FinanceItem-ProductDetailValue">\s*([0-9\s.,]+)/su',
            $html,
            $matches,
            PREG_SET_ORDER,
        )) {
            return [];
        }

        $items = [];

        foreach ($matches as $match) {
            $name = html_entity_decode(strip_tags($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $name = trim(preg_replace('/\s+/u', ' ', $name) ?? $name);
            $rate = $this->decimal($match[2]);

            if ($name === '' || $rate === null) {
                continue;
            }

            $items[] = [
                'name' => $name,
                'rate' => $rate,
            ];
        }

        return $items;
    }

    private function resolveBank(string $name): Bank
    {
        $slug = Str::slug($name);

        if ($slug === '') {
            $slug = 'bank-'.substr(md5($name), 0, 8);
        }

        $existing = Bank::query()
            ->where(function ($query) use ($slug, $name): void {
                $query->where('slug', $slug)
                    ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($name)]);
            })
            ->first();

        if ($existing !== null) {
            if ($existing->name !== $name) {
                $existing->name = $name;
                $existing->save();
            }

            return $existing;
        }

        return Bank::query()->create([
            'name' => $name,
            'slug' => $slug,
            'parser_code' => null,
            'rates_url' => null,
            'is_active' => true,
            'sort_order' => 100,
        ]);
    }

    private function decimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = str_replace([' ', ',', "\xc2\xa0"], ['', '.', ''], (string) $value);

        if (! is_numeric($normalized)) {
            return null;
        }

        return number_format((float) $normalized, 4, '.', '');
    }
}
