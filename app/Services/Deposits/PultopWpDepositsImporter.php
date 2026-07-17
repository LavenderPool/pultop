<?php

namespace App\Services\Deposits;

use App\Models\Bank;
use App\Models\Deposit;
use App\Services\PublicCacheService;
use App\Services\Wp\PultopWpClient;
use App\Services\Wp\PultopWpConditionParser;
use App\Services\Wp\PultopWpDom;
use DOMElement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PultopWpDepositsImporter
{
    /** @var list<string> */
    private const CURRENCIES = ['', 'sum', 'usd', 'eur', 'rub', 'kzt', 'gbp', 'chf', 'jpy'];

    private readonly PultopWpClient $client;

    public function __construct(
        private readonly PublicCacheService $cache,
    ) {
        $this->client = PultopWpClient::fromConfigKey('deposits');
    }

    /**
     * @param  null|callable(int $total): void  $onStart
     * @param  null|callable(array{slug: string, title: string, ok: bool, message: string}): void  $onItem
     * @return array{
     *     ok_count: int,
     *     fail_count: int,
     *     errors: list<string>,
     *     deposits: list<array{slug: string, title: string, ok: bool, message: string}>
     * }
     */
    public function import(
        bool $dryRun = false,
        ?int $limit = null,
        ?callable $onStart = null,
        ?callable $onItem = null,
        ?int $concurrency = null,
    ): array {
        $cards = $this->fetchListCards();

        if ($limit !== null && $limit > 0) {
            $cards = array_slice($cards, 0, $limit);
        }

        if ($onStart !== null) {
            $onStart(count($cards));
        }

        $ok = 0;
        $fail = 0;
        $errors = [];
        $results = [];
        $concurrency = max(1, min(20, $concurrency ?? (int) config('deposits.parse_concurrency', 5)));
        $sortOrder = 0;

        foreach (array_chunk($cards, $concurrency) as $batch) {
            $urls = [];
            foreach ($batch as $card) {
                $urls[$card['slug']] = $this->detailUrl($card['slug']);
            }

            $bodies = $this->client->getHtmlMany($urls);

            foreach ($batch as $card) {
                $sortOrder++;
                try {
                    $body = $bodies[$card['slug']] ?? null;
                    if ($body instanceof Throwable) {
                        throw $body;
                    }
                    if (! is_string($body)) {
                        throw new \RuntimeException('Пустой ответ detail');
                    }

                    $detail = $this->parseDetailHtml($body);
                    $merged = $this->mergeCardAndDetail($card, $detail);

                    if (! $dryRun) {
                        $this->upsertDeposit($merged, $sortOrder);
                    }

                    $ok++;
                    $results[] = [
                        'slug' => $merged['slug'],
                        'title' => $merged['title'],
                        'ok' => true,
                        'message' => $dryRun ? 'dry-run OK' : 'OK',
                    ];
                } catch (Throwable $e) {
                    $fail++;
                    $message = $e->getMessage();
                    $errors[] = "{$card['slug']}: {$message}";
                    $results[] = [
                        'slug' => $card['slug'],
                        'title' => $card['title'],
                        'ok' => false,
                        'message' => $message,
                    ];
                    Log::warning('WP deposit import failed', [
                        'slug' => $card['slug'],
                        'error' => $message,
                    ]);
                }

                if ($onItem !== null) {
                    $onItem($results[array_key_last($results)]);
                }
            }
        }

        if (! $dryRun && $ok > 0) {
            $this->cache->forgetGroup(PublicCacheService::GROUP_DEPOSITS);
        }

        return [
            'ok_count' => $ok,
            'fail_count' => $fail,
            'errors' => $errors,
            'deposits' => $results,
        ];
    }

    /**
     * @return list<array{
     *     slug: string,
     *     title: string,
     *     bank_slug: ?string,
     *     bank_name: ?string,
     *     rate_display: ?string,
     *     term_display: ?string,
     *     amount_display: ?string
     * }>
     */
    private function fetchListCards(): array
    {
        /** @var array<string, array{slug: string, title: string, bank_slug: ?string, bank_name: ?string, rate_display: ?string, term_display: ?string, amount_display: ?string}> $bySlug */
        $bySlug = [];

        foreach (self::CURRENCIES as $currency) {
            $cards = $this->client->fetchAllPages(
                'load_more_deposits',
                $this->listUrl(),
                [
                    'banks_list' => '',
                    'currency' => $currency,
                    'sum' => '',
                    'srok' => 'all',
                    'summa' => '',
                ],
                fn (string $html): array => $this->parseListCards($html),
                startPage: ($currency === '' || $currency === 'sum') ? 1 : 1,
            );

            foreach ($cards as $card) {
                $bySlug[$card['slug']] = $card;
            }
        }

        return array_values($bySlug);
    }

    /**
     * @return list<array{
     *     slug: string,
     *     title: string,
     *     bank_slug: ?string,
     *     bank_name: ?string,
     *     rate_display: ?string,
     *     term_display: ?string,
     *     amount_display: ?string
     * }>
     */
    private function parseListCards(string $html): array
    {
        if ($html === '' || ! str_contains($html, 'item-content')) {
            return [];
        }

        $xpath = PultopWpDom::xpath($html);
        $nodes = $xpath->query('//div[contains(@class,"item-content")]');
        if ($nodes === false || $nodes->length === 0) {
            return [];
        }

        $cards = [];

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $link = PultopWpDom::firstElement($xpath, './/div[contains(@class,"item-data")]//a[contains(@href,"/deposits/")]', $node)
                ?? PultopWpDom::firstElement($xpath, './/a[contains(@href,"/deposits/")]', $node);
            $href = $link instanceof DOMElement ? trim((string) $link->getAttribute('href')) : '';
            $slug = PultopWpDom::slugFromSectionHref($href, 'deposits');

            if ($slug === null) {
                continue;
            }

            $title = $link !== null ? PultopWpDom::normalizeText($link->textContent) : '';
            if ($title === '') {
                continue;
            }

            $bankLink = $xpath->query('.//a[contains(@class,"item-name-bank")]', $node)->item(0);
            $bankHref = $bankLink instanceof DOMElement ? trim((string) $bankLink->getAttribute('href')) : '';
            $bankSlug = PultopWpDom::slugFromBankHref($bankHref);
            $bankName = $bankLink !== null ? PultopWpDom::normalizeText($bankLink->textContent) : null;
            if ($bankName === '') {
                $bankName = null;
            }

            $rateNode = $xpath->query('.//*[contains(@class,"item-rate")]', $node)->item(0);
            $rateDisplay = $rateNode !== null ? PultopWpDom::normalizeText($rateNode->textContent) : null;
            if ($rateDisplay === '') {
                $rateDisplay = null;
            }

            $termDisplay = null;
            $amountDisplay = null;

            $paramBlocks = $xpath->query('.//*[contains(@class,"item-params")]/div', $node);
            if ($paramBlocks !== false) {
                foreach ($paramBlocks as $block) {
                    if (! $block instanceof DOMElement) {
                        continue;
                    }

                    $text = PultopWpDom::normalizeText($block->textContent);
                    $strong = $xpath->query('.//strong', $block)->item(0);
                    $strongText = $strong !== null ? PultopWpDom::normalizeText($strong->textContent) : '';

                    if (str_contains(mb_strtolower($text), 'срок')) {
                        $termDisplay = $strongText !== '' ? $strongText : null;
                    } elseif (str_contains(mb_strtolower($text), 'сумма')) {
                        $amountDisplay = $strongText !== '' ? $strongText : null;
                    }
                }
            }

            $cards[] = [
                'slug' => $slug,
                'title' => html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'bank_slug' => $bankSlug,
                'bank_name' => $bankName,
                'rate_display' => $rateDisplay,
                'term_display' => $termDisplay,
                'amount_display' => $amountDisplay,
            ];
        }

        return $cards;
    }

    /**
     * @return array{
     *     title: ?string,
     *     currency: ?string,
     *     rate_rows: list<array{rate: string, term: ?string, note: ?string}>,
     *     conditions: list<array{label: string, value: ?string}>,
     *     special_conditions: ?string,
     *     apply_url: ?string,
     *     bank_slug: ?string,
     *     early_termination: bool,
     *     partial_withdrawal: bool,
     *     capitalization: bool,
     *     is_online: bool
     * }
     */
    private function fetchDetail(string $slug): array
    {
        return $this->parseDetailHtml($this->client->getHtml($this->detailUrl($slug)));
    }

    /**
     * @return array{
     *     title: ?string,
     *     currency: ?string,
     *     rate_rows: list<array{rate: string, term: ?string, note: ?string}>,
     *     conditions: list<array{label: string, value: ?string, note?: ?string}>,
     *     special_conditions: ?string,
     *     apply_url: ?string,
     *     bank_slug: ?string,
     *     early_termination: bool,
     *     partial_withdrawal: bool,
     *     capitalization: bool,
     *     is_online: bool
     * }
     */
    private function parseDetailHtml(string $html): array
    {
        $xpath = PultopWpDom::xpath($html);

        $titleNode = $xpath->query('//h1[contains(@class,"entry-title")]')->item(0);
        $title = $titleNode !== null ? PultopWpDom::normalizeText($titleNode->textContent) : null;

        $rateRows = [];
        $rateNodes = $xpath->query('//*[contains(@class,"info-rate")]//tr[contains(@class,"rate-data")]');
        if ($rateNodes !== false) {
            foreach ($rateNodes as $row) {
                if (! $row instanceof DOMElement) {
                    continue;
                }

                $rateCell = $xpath->query('.//th', $row)->item(0);
                $cells = $xpath->query('.//td', $row);
                $rate = $rateCell !== null ? PultopWpDom::normalizeText($rateCell->textContent) : '';
                $term = $cells !== false && $cells->item(0) !== null
                    ? PultopWpDom::normalizeText($cells->item(0)->textContent)
                    : null;
                $note = $cells !== false && $cells->item(1) !== null
                    ? PultopWpDom::normalizeText($cells->item(1)->textContent)
                    : null;

                if ($rate === '') {
                    continue;
                }

                $rateRows[] = [
                    'rate' => $rate,
                    'term' => $term !== '' ? $term : null,
                    'note' => $note !== '' ? $note : null,
                ];
            }
        }

        $conditions = [];
        /** @var list<array{label: string, value: ?string, note: ?string, enabled: ?bool}> $rawConditions */
        $rawConditions = [];
        $conditionNodes = $xpath->query('//*[contains(@class,"info-conditions")]//li[contains(@class,"item-detail-item")]');
        if ($conditionNodes !== false) {
            foreach ($conditionNodes as $item) {
                if (! $item instanceof DOMElement) {
                    continue;
                }

                $parsed = PultopWpConditionParser::parseItem($item, $xpath);
                if ($parsed === null) {
                    continue;
                }

                $conditions[] = [
                    'label' => $parsed['label'],
                    'value' => $parsed['value'],
                    'note' => $parsed['note'],
                ];

                $rawConditions[] = $parsed;
            }
        }

        $specialHtml = null;
        $specialNode = $xpath->query('//*[contains(@class,"info-conditions")]//*[contains(@class,"special")]')->item(0);
        if ($specialNode instanceof DOMElement) {
            $inner = '';
            foreach ($specialNode->childNodes as $child) {
                if ($child instanceof DOMElement && strtolower($child->tagName) === 'h2') {
                    continue;
                }
                $inner .= $specialNode->ownerDocument?->saveHTML($child) ?? '';
            }
            $specialHtml = PultopWpDom::normalizeHtml($inner);
            if ($specialHtml === '') {
                $specialHtml = null;
            }
        }

        $applyUrl = null;
        $links = $xpath->query('//*[contains(@class,"info-footer")]//a[contains(@class,"info-link-btn")]');
        if ($links !== false) {
            foreach ($links as $link) {
                if (! $link instanceof DOMElement) {
                    continue;
                }
                $text = mb_strtolower(PultopWpDom::normalizeText($link->textContent));
                if (str_contains($text, 'сайт банка') || str_contains($text, 'открыть вклад')) {
                    $href = trim((string) $link->getAttribute('href'));
                    if ($href === '') {
                        continue;
                    }
                    if (! str_starts_with($href, '/') && ! str_contains($href, 'pultop.uz')) {
                        $applyUrl = PultopWpDom::absoluteUrl($this->client->baseUrl(), $href);
                        break;
                    }
                    if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
                        $host = parse_url($href, PHP_URL_HOST);
                        if (is_string($host) && ! str_contains($host, 'pultop.uz')) {
                            $applyUrl = $href;
                            break;
                        }
                    }
                }
            }
        }

        $bankSlug = null;
        $bankLink = $xpath->query('//a[contains(@class,"item-name-bank") or contains(@href,"/banks/")]')->item(0);
        if ($bankLink instanceof DOMElement) {
            $bankSlug = PultopWpDom::slugFromBankHref(trim((string) $bankLink->getAttribute('href')));
        }

        if ($bankSlug === null) {
            $cardLink = $xpath->query('//*[contains(@class,"info-footer")]//a[contains(@class,"info-link-btn")]')->item(0);
            if ($cardLink instanceof DOMElement) {
                $href = trim((string) $cardLink->getAttribute('href'));
                $bankSlug = PultopWpDom::slugFromBankHref($href) ?? PultopWpDom::slugFromLooseBankHref($href);
            }
        }

        $flags = $this->deriveFlags($rawConditions, $specialHtml);

        return [
            'title' => $title !== '' ? $title : null,
            'currency' => $this->detectCurrency($conditions),
            'rate_rows' => $rateRows,
            'conditions' => $conditions,
            'special_conditions' => $specialHtml,
            'apply_url' => $applyUrl,
            'bank_slug' => $bankSlug,
            'early_termination' => $flags['early_termination'],
            'partial_withdrawal' => $flags['partial_withdrawal'],
            'capitalization' => $flags['capitalization'],
            'is_online' => $flags['is_online'],
        ];
    }

    /**
     * @param  list<array{label: string, value: ?string, note: ?string, enabled: ?bool}>  $conditions
     * @return array{early_termination: bool, partial_withdrawal: bool, capitalization: bool, is_online: bool}
     */
    private function deriveFlags(array $conditions, ?string $specialHtml): array
    {
        $flags = [
            'early_termination' => false,
            'partial_withdrawal' => false,
            'capitalization' => false,
            'is_online' => false,
        ];

        foreach ($conditions as $condition) {
            $label = mb_strtolower($condition['label']);
            $enabled = $condition['enabled'] ?? PultopWpConditionParser::looksEnabled($condition['value']);

            if (str_contains($label, 'досрочн')) {
                $flags['early_termination'] = $enabled;
            }
            if (str_contains($label, 'частичн')) {
                $flags['partial_withdrawal'] = $enabled;
            }
            if (str_contains($label, 'капитализац')) {
                $flags['capitalization'] = $enabled;
            }
            if (str_contains($label, 'on-line')
                || str_contains($label, 'онлайн')
                || (str_contains($label, 'открытие') && str_contains($label, 'приложени'))) {
                $flags['is_online'] = $flags['is_online'] || $enabled;
            }
        }

        $special = mb_strtolower(strip_tags((string) $specialHtml));
        if ($special !== '') {
            if (str_contains($special, 'частичное снятие') && str_contains($special, 'не предусмотр')) {
                $flags['partial_withdrawal'] = false;
            }
            if (str_contains($special, 'капитализац') && (str_contains($special, 'без капитализац') || str_contains($special, 'не предусмотр'))) {
                $flags['capitalization'] = false;
            }
            if (str_contains($special, 'досрочн') && ! str_contains($special, 'не предусмотр')) {
                $flags['early_termination'] = true;
            }
        }

        return $flags;
    }

    /**
     * @param  list<array{label: string, value: ?string}>  $conditions
     */
    private function detectCurrency(array $conditions): ?string
    {
        foreach ($conditions as $condition) {
            if (! str_contains(mb_strtolower($condition['label']), 'валют')) {
                continue;
            }

            $mapped = $this->mapCurrencyLabel((string) ($condition['value'] ?? ''));
            if ($mapped !== null) {
                return $mapped;
            }
        }

        return null;
    }

    private function mapCurrencyLabel(string $value): ?string
    {
        $value = mb_strtolower(trim($value));
        if ($value === '') {
            return null;
        }

        return match (true) {
            str_contains($value, 'сум') || str_contains($value, 'uzs') => 'UZS',
            str_contains($value, 'доллар') || str_contains($value, 'usd') => 'USD',
            str_contains($value, 'евро') || str_contains($value, 'eur') => 'EUR',
            str_contains($value, 'руб') || str_contains($value, 'rub') => 'RUB',
            str_contains($value, 'тенге') || str_contains($value, 'kzt') => 'KZT',
            str_contains($value, 'фунт') || str_contains($value, 'gbp') => 'GBP',
            str_contains($value, 'франк') || str_contains($value, 'chf') => 'CHF',
            str_contains($value, 'иен') || str_contains($value, 'йен') || str_contains($value, 'jpy') => 'JPY',
            default => null,
        };
    }

    /**
     * @param  array{
     *     slug: string,
     *     title: string,
     *     bank_slug: ?string,
     *     bank_name: ?string,
     *     rate_display: ?string,
     *     term_display: ?string,
     *     amount_display: ?string
     * }  $card
     * @param  array{
     *     title: ?string,
     *     currency: ?string,
     *     rate_rows: list<array{rate: string, term: ?string, note: ?string}>,
     *     conditions: list<array{label: string, value: ?string}>,
     *     special_conditions: ?string,
     *     apply_url: ?string,
     *     bank_slug: ?string,
     *     early_termination: bool,
     *     partial_withdrawal: bool,
     *     capitalization: bool,
     *     is_online: bool
     * }  $detail
     * @return array{
     *     slug: string,
     *     title: string,
     *     bank_slug: ?string,
     *     bank_name: ?string,
     *     currency: string,
     *     rate_display: ?string,
     *     term_display: ?string,
     *     amount_display: ?string,
     *     term_min_months: ?int,
     *     term_max_months: ?int,
     *     amount_min: ?int,
     *     amount_max: ?int,
     *     early_termination: bool,
     *     partial_withdrawal: bool,
     *     capitalization: bool,
     *     is_online: bool,
     *     rate_rows: list<array{rate: string, term: ?string, note: ?string}>,
     *     conditions: list<array{label: string, value: ?string}>,
     *     special_conditions: ?string,
     *     apply_url: ?string
     * }
     */
    private function mergeCardAndDetail(array $card, array $detail): array
    {
        $termDisplay = $card['term_display'];
        $amountDisplay = $card['amount_display'];
        $currency = $detail['currency']
            ?? $this->currencyFromAmountDisplay($amountDisplay)
            ?? 'UZS';

        [$termMin, $termMax] = $this->parseTermMonths($termDisplay);
        [$amountMin, $amountMax] = $this->parseAmountBounds($amountDisplay);

        return [
            'slug' => $card['slug'],
            'title' => $detail['title'] ?? $card['title'],
            'bank_slug' => $detail['bank_slug'] ?? $card['bank_slug'],
            'bank_name' => $card['bank_name'],
            'currency' => $currency,
            'rate_display' => $card['rate_display'],
            'term_display' => $termDisplay,
            'amount_display' => $amountDisplay,
            'term_min_months' => $termMin,
            'term_max_months' => $termMax,
            'amount_min' => $amountMin,
            'amount_max' => $amountMax,
            'early_termination' => $detail['early_termination'],
            'partial_withdrawal' => $detail['partial_withdrawal'],
            'capitalization' => $detail['capitalization'],
            'is_online' => $detail['is_online'],
            'rate_rows' => $detail['rate_rows'],
            'conditions' => $detail['conditions'],
            'special_conditions' => $detail['special_conditions'],
            'apply_url' => $detail['apply_url'],
        ];
    }

    /**
     * @param  array{
     *     slug: string,
     *     title: string,
     *     bank_slug: ?string,
     *     bank_name: ?string,
     *     currency: string,
     *     rate_display: ?string,
     *     term_display: ?string,
     *     amount_display: ?string,
     *     term_min_months: ?int,
     *     term_max_months: ?int,
     *     amount_min: ?int,
     *     amount_max: ?int,
     *     early_termination: bool,
     *     partial_withdrawal: bool,
     *     capitalization: bool,
     *     is_online: bool,
     *     rate_rows: list<array{rate: string, term: ?string, note: ?string}>,
     *     conditions: list<array{label: string, value: ?string}>,
     *     special_conditions: ?string,
     *     apply_url: ?string
     * }  $data
     */
    private function upsertDeposit(array $data, int $sortOrder): void
    {
        DB::transaction(function () use ($data, $sortOrder): void {
            $bankId = null;
            if (filled($data['bank_slug'])) {
                $bank = Bank::query()->where('slug', $data['bank_slug'])->first();
                if ($bank === null && filled($data['bank_name'])) {
                    $bank = Bank::query()
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $data['bank_name'])])
                        ->first();
                }
                if ($bank === null) {
                    Log::warning('WP deposit import: bank not found', [
                        'deposit_slug' => $data['slug'],
                        'bank_slug' => $data['bank_slug'],
                        'bank_name' => $data['bank_name'],
                    ]);
                } else {
                    $bankId = $bank->id;
                }
            }

            $deposit = Deposit::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'bank_id' => $bankId,
                    'title' => $data['title'],
                    'currency' => $data['currency'],
                    'rate_display' => $data['rate_display'],
                    'term_display' => $data['term_display'],
                    'amount_display' => $data['amount_display'],
                    'term_min_months' => $data['term_min_months'],
                    'term_max_months' => $data['term_max_months'],
                    'amount_min' => $data['amount_min'],
                    'amount_max' => $data['amount_max'],
                    'early_termination' => $data['early_termination'],
                    'partial_withdrawal' => $data['partial_withdrawal'],
                    'capitalization' => $data['capitalization'],
                    'is_online' => $data['is_online'],
                    'special_conditions' => $data['special_conditions'],
                    'apply_url' => $data['apply_url'],
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ],
            );

            $deposit->rates()->delete();
            foreach ($data['rate_rows'] as $index => $row) {
                $deposit->rates()->create([
                    'rate' => $row['rate'],
                    'term' => $row['term'],
                    'note' => $row['note'],
                    'sort_order' => $index + 1,
                ]);
            }

            $deposit->conditions()->delete();
            foreach ($data['conditions'] as $index => $condition) {
                $deposit->conditions()->create([
                    'label' => $condition['label'],
                    'value' => $condition['value'],
                    'note' => $condition['note'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }
        });
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function parseTermMonths(?string $display): array
    {
        if ($display === null || $display === '') {
            return [null, null];
        }

        $text = mb_strtolower($display);
        $multiplier = 1;
        if (str_contains($text, 'год') || str_contains($text, 'лет')) {
            $multiplier = 12;
        }

        if (preg_match_all('/(\d+(?:[.,]\d+)?)/u', $display, $m) && $m[1] !== []) {
            $values = array_map(function (string $raw) use ($multiplier): int {
                $num = (float) str_replace(',', '.', $raw);

                return (int) round($num * $multiplier);
            }, $m[1]);

            $min = min($values);
            $max = max($values);

            return [$min > 0 ? $min : null, $max > 0 ? $max : null];
        }

        return [null, null];
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function parseAmountBounds(?string $display): array
    {
        if ($display === null || $display === '') {
            return [null, null];
        }

        $text = mb_strtolower($display);
        if (str_contains($text, 'без огранич') || str_contains($text, 'не огранич')) {
            return [null, null];
        }

        if (str_contains($text, 'от') && str_contains($text, 'до')) {
            if (preg_match_all('/([\d\s]+)/u', $display, $m) && count($m[1]) >= 2) {
                $first = (int) (preg_replace('/\D+/', '', $m[1][0]) ?? '');
                $second = (int) (preg_replace('/\D+/', '', $m[1][1]) ?? '');

                return [
                    $first > 0 ? $first : null,
                    $second > 0 ? $second : null,
                ];
            }
        }

        $digits = preg_replace('/\D+/', '', $display) ?? '';
        if ($digits === '') {
            return [null, null];
        }

        $amount = (int) $digits;
        if ($amount <= 0) {
            return [null, null];
        }

        if (str_contains($text, 'до') && ! str_contains($text, 'от')) {
            return [null, $amount];
        }

        // Typical deposit wording: «от N» or bare minimum amount.
        return [$amount, null];
    }

    private function currencyFromAmountDisplay(?string $display): ?string
    {
        if ($display === null || $display === '') {
            return null;
        }

        if (preg_match('/\b(UZS|USD|EUR|RUB|KZT|GBP|CHF|JPY)\b/i', $display, $m)) {
            return strtoupper($m[1]);
        }

        return $this->mapCurrencyLabel($display);
    }

    private function listUrl(): string
    {
        return $this->client->baseUrl().rtrim((string) config('deposits.wp_list_path', '/deposits/'), '/').'/';
    }

    private function detailUrl(string $slug): string
    {
        return $this->client->baseUrl().'/deposits/'.$slug.'/';
    }
}
