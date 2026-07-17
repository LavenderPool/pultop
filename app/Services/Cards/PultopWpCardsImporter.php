<?php

namespace App\Services\Cards;

use App\Enums\CardType;
use App\Models\Bank;
use App\Models\Card;
use App\Services\PublicCacheService;
use App\Services\Wp\PultopWpClient;
use App\Services\Wp\PultopWpConditionParser;
use App\Services\Wp\PultopWpDom;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PultopWpCardsImporter
{
    /** @var list<string> */
    private const CURRENCIES = ['sum', 'usd', 'eur', 'rub', 'kzt', 'gbp', 'chf', 'jpy'];

    private readonly PultopWpClient $client;

    public function __construct(
        private readonly PublicCacheService $cache,
    ) {
        $this->client = PultopWpClient::fromConfigKey('cards');
    }

    /**
     * @param  null|callable(int $total): void  $onStart
     * @param  null|callable(array{slug: string, title: string, ok: bool, message: string}): void  $onItem
     * @return array{
     *     ok_count: int,
     *     fail_count: int,
     *     errors: list<string>,
     *     cards: list<array{slug: string, title: string, ok: bool, message: string}>
     * }
     */
    public function import(
        bool $dryRun = false,
        ?int $limit = null,
        ?callable $onStart = null,
        ?callable $onItem = null,
        ?int $concurrency = null,
    ): array {
        $listUrl = $this->client->baseUrl().'/cards/';
        $archiveHtml = $this->client->getHtml($listUrl, $listUrl);
        $nonce = $this->client->extractNonce($archiveHtml, '/cards/');
        /** @var array<string, array<string, mixed>> $cardsBySlug */
        $cardsBySlug = [];

        // Первая страница каталога отдаётся в HTML /cards/, ajax — последующие.
        foreach ($this->parseListCards($archiveHtml, 'sum') as $card) {
            $cardsBySlug[$card['slug']] = $card;
        }

        foreach (self::CURRENCIES as $currency) {
            $pageCards = $this->client->fetchAllPages(
                'load_more_cards',
                $listUrl,
                [
                    'banks_list' => '',
                    'currency' => $currency,
                    'payment_system' => '',
                    'type_card' => '',
                ],
                fn (string $html): array => $this->parseListCards($html, $currency),
                startPage: 1,
                nonce: $nonce,
            );

            foreach ($pageCards as $card) {
                $slug = $card['slug'];
                if (! isset($cardsBySlug[$slug])) {
                    $cardsBySlug[$slug] = $card;
                }
            }
        }

        /** @var list<array<string, mixed>> $queue */
        $queue = array_values($cardsBySlug);
        /** @var array<string, true> $seen */
        $seen = array_fill_keys(array_column($queue, 'slug'), true);

        if ($limit !== null && $limit > 0) {
            $queue = array_slice($queue, 0, $limit);
            $seen = array_fill_keys(array_column($queue, 'slug'), true);
        }

        if ($onStart !== null) {
            $onStart(max(1, count($queue)));
        }

        $ok = 0;
        $fail = 0;
        $errors = [];
        $results = [];
        $concurrency = max(1, min(20, $concurrency ?? (int) config('cards.parse_concurrency', 5)));
        $sortOrder = 0;

        while ($queue !== []) {
            $batch = array_splice($queue, 0, $concurrency);
            $urls = [];
            foreach ($batch as $card) {
                $urls[$card['slug']] = $this->detailUrl($card['slug']);
            }

            $bodies = $this->client->getHtmlMany($urls, $listUrl);

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
                        $this->upsertCard($merged, $sortOrder);
                    }

                    // WP ajax-пагинация каталога часто обрывается — добираем карточки
                    // по таблице «Другие карты банка» на detail-страницах.
                    if ($limit === null) {
                        foreach ($this->extractRelatedCardSlugs($body) as $relatedSlug) {
                            if (isset($seen[$relatedSlug])) {
                                continue;
                            }
                            $seen[$relatedSlug] = true;
                            $queue[] = [
                                'slug' => $relatedSlug,
                                'title' => $relatedSlug,
                                'bank_slug' => $merged['bank_slug'] ?? null,
                                'bank_name' => $merged['bank_name'] ?? null,
                                'currency' => $merged['currency'] ?? 'sum',
                                'payment_system' => null,
                                'card_type' => null,
                                'category' => null,
                                'issue_cost_display' => null,
                                'validity_display' => null,
                                'image_url' => null,
                            ];
                        }
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
                    Log::warning('WP card import failed', [
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
            $this->cache->forgetGroup(PublicCacheService::GROUP_CARDS);
        }

        return [
            'ok_count' => $ok,
            'fail_count' => $fail,
            'errors' => $errors,
            'cards' => $results,
        ];
    }

    /**
     * @return list<array{
     *     slug: string,
     *     title: string,
     *     bank_slug: ?string,
     *     bank_name: ?string,
     *     currency: string,
     *     payment_system: ?string,
     *     card_type: ?string,
     *     category: ?string,
     *     issue_cost_display: ?string,
     *     validity_display: ?string,
     *     image_url: ?string
     * }>
     */
    private function parseListCards(string $html, string $currency): array
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

            $link = $xpath->query('.//a[contains(@class,"item-name") and contains(@href,"/cards/")]', $node)->item(0);
            if ($link === null) {
                $link = $xpath->query('.//div[contains(@class,"item-data")]//a[contains(@href,"/cards/")]', $node)->item(0);
            }
            $href = $link instanceof DOMElement ? trim((string) $link->getAttribute('href')) : '';
            $slug = PultopWpDom::slugFromSectionHref($href, 'cards');

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

            $params = $this->parseParamLabels($xpath, $node);

            $imageUrl = null;
            $img = $xpath->query('.//*[contains(@class,"card-image")]//img', $node)->item(0);
            if ($img instanceof DOMElement) {
                $src = trim((string) $img->getAttribute('src'));
                if ($src !== '') {
                    $imageUrl = PultopWpDom::absoluteUrl($this->client->baseUrl(), $src);
                }
            }

            $cards[] = [
                'slug' => $slug,
                'title' => html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'bank_slug' => $bankSlug,
                'bank_name' => $bankName,
                'currency' => $currency,
                'payment_system' => $params['payment_system'],
                'card_type' => $params['card_type'],
                'category' => $params['category'],
                'issue_cost_display' => $params['issue_cost_display'],
                'validity_display' => $params['validity_display'],
                'image_url' => $imageUrl,
            ];
        }

        return $cards;
    }

    /**
     * @return array{
     *     payment_system: ?string,
     *     card_type: ?string,
     *     category: ?string,
     *     issue_cost_display: ?string,
     *     validity_display: ?string
     * }
     */
    private function parseParamLabels(DOMXPath $xpath, DOMElement $node): array
    {
        $result = [
            'payment_system' => null,
            'card_type' => null,
            'category' => null,
            'issue_cost_display' => null,
            'validity_display' => null,
        ];

        $divs = $xpath->query('.//div[span[contains(@style,"797979") or contains(@style,"color")]]', $node);
        if ($divs === false || $divs->length === 0) {
            $divs = $xpath->query('.//div[count(span)=1 and count(*)=1]', $node);
        }
        if ($divs === false) {
            return $result;
        }

        foreach ($divs as $div) {
            if (! $div instanceof DOMElement) {
                continue;
            }

            $text = PultopWpDom::normalizeText($div->textContent);
            $labelNode = $xpath->query('./span', $div)->item(0);
            $label = $labelNode !== null ? PultopWpDom::normalizeText($labelNode->textContent) : '';
            $label = rtrim($label, ':');
            $labelLower = mb_strtolower($label);
            $value = trim(preg_replace('/^'.preg_quote($label, '/').'\s*:?\s*/u', '', $text) ?? $text);
            $value = trim($value);
            if ($value === '' || $label === '') {
                continue;
            }

            if (str_contains($labelLower, 'платежн') || str_contains($labelLower, 'платёжн')) {
                $result['payment_system'] = $value;
            } elseif (str_contains($labelLower, 'тип карты')) {
                $result['card_type'] = $value;
            } elseif (str_contains($labelLower, 'категория')) {
                $result['category'] = $value;
            } elseif (str_contains($labelLower, 'стоимость')) {
                $result['issue_cost_display'] = $value;
            } elseif (str_contains($labelLower, 'срок действия')) {
                $result['validity_display'] = $value;
            }
        }

        return $result;
    }

    /**
     * @return array{
     *     title: ?string,
     *     conditions: list<array{label: string, value: ?string}>,
     *     special_conditions: ?string,
     *     apply_url: ?string,
     *     bank_slug: ?string,
     *     currency: ?string,
     *     payment_system: ?string,
     *     card_type: ?string,
     *     category: ?string,
     *     issue_cost_display: ?string,
     *     validity_display: ?string
     * }
     */
    private function fetchDetail(string $slug): array
    {
        return $this->parseDetailHtml($this->client->getHtml($this->detailUrl($slug)));
    }

    private function parseDetailHtml(string $html): array
    {
        $xpath = PultopWpDom::xpath($html);

        $titleNode = $xpath->query('//h1[contains(@class,"entry-title")]')->item(0);
        $title = $titleNode !== null ? PultopWpDom::normalizeText($titleNode->textContent) : null;

        $conditions = [];
        $currency = null;
        $paymentSystem = null;
        $cardType = null;
        $category = null;
        $issueCost = null;
        $validity = null;

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

                $labelLower = mb_strtolower($parsed['label']);
                $value = $parsed['value'];
                if (str_contains($labelLower, 'валюта')) {
                    $currency = $this->normalizeCurrencyCode($value);
                } elseif (str_contains($labelLower, 'платежн') || str_contains($labelLower, 'платёжн')) {
                    $paymentSystem = $value;
                } elseif (str_contains($labelLower, 'тип карты')) {
                    $cardType = $value;
                } elseif (str_contains($labelLower, 'категория')) {
                    $category = $value;
                } elseif (str_contains($labelLower, 'стоимость выпуска') || (str_contains($labelLower, 'стоимость') && $issueCost === null)) {
                    $issueCost = $value;
                } elseif (str_contains($labelLower, 'срок')) {
                    $validity = $value;
                }
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
                if (str_contains($text, 'карточка банка') || str_contains($text, 'калькулятор')) {
                    continue;
                }
                if (str_contains($text, 'сайт банка')
                    || str_contains($text, 'открыть карту')
                    || str_contains($text, 'открыть')
                    || str_contains($text, 'оформить')) {
                    $href = trim((string) $link->getAttribute('href'));
                    if ($href === '') {
                        continue;
                    }
                    $absolute = PultopWpDom::absoluteUrl($this->client->baseUrl(), $href);
                    $host = parse_url($absolute, PHP_URL_HOST);
                    if (is_string($host) && ! str_contains($host, 'pultop.uz')) {
                        $applyUrl = $absolute;
                        break;
                    }
                }
            }

            if ($applyUrl === null) {
                foreach ($links as $link) {
                    if (! $link instanceof DOMElement) {
                        continue;
                    }
                    $text = mb_strtolower(PultopWpDom::normalizeText($link->textContent));
                    if (str_contains($text, 'карточка банка') || str_contains($text, 'калькулятор')) {
                        continue;
                    }
                    $href = trim((string) $link->getAttribute('href'));
                    if ($href === '') {
                        continue;
                    }
                    $absolute = PultopWpDom::absoluteUrl($this->client->baseUrl(), $href);
                    $host = parse_url($absolute, PHP_URL_HOST);
                    if (is_string($host) && ! str_contains($host, 'pultop.uz')) {
                        $applyUrl = $absolute;
                        break;
                    }
                }
            }
        }

        $bankSlug = null;
        $bankLink = $xpath->query('//a[contains(@class,"item-name-bank")]')->item(0);
        if ($bankLink instanceof DOMElement) {
            $bankSlug = PultopWpDom::slugFromBankHref(trim((string) $bankLink->getAttribute('href')));
        }
        if ($bankSlug === null) {
            $cardLink = $xpath->query('//*[contains(@class,"info-footer")]//a[contains(@class,"info-link-btn")]')->item(0);
            if ($cardLink instanceof DOMElement) {
                $href = trim((string) $cardLink->getAttribute('href'));
                $bankSlug = PultopWpDom::slugFromBankHref($href);
            }
        }

        return [
            'title' => $title !== '' ? $title : null,
            'conditions' => $conditions,
            'special_conditions' => $specialHtml,
            'apply_url' => $applyUrl,
            'bank_slug' => $bankSlug,
            'currency' => $currency,
            'payment_system' => $paymentSystem,
            'card_type' => $cardType,
            'category' => $category,
            'issue_cost_display' => $issueCost,
            'validity_display' => $validity,
        ];
    }

    /**
     * @return list<string>
     */
    private function extractRelatedCardSlugs(string $html): array
    {
        $xpath = PultopWpDom::xpath($html);
        $links = $xpath->query('//*[contains(@class,"info-more")]//a[contains(@href,"/cards/")]');
        if ($links === false || $links->length === 0) {
            $links = $xpath->query('//a[contains(@href,"/cards/")]');
        }
        if ($links === false) {
            return [];
        }

        $slugs = [];
        foreach ($links as $link) {
            if (! $link instanceof DOMElement) {
                continue;
            }
            $href = trim((string) $link->getAttribute('href'));
            if ($href === '' || ! preg_match('#/cards/([^/]+)/?#', $href, $m)) {
                continue;
            }
            $slug = rawurldecode($m[1]);
            if ($slug === '' || $slug === 'cards') {
                continue;
            }
            $slugs[$slug] = $slug;
        }

        return array_values($slugs);
    }

    /**
     * @param  array<string, mixed>  $card
     * @param  array<string, mixed>  $detail
     * @return array<string, mixed>
     */
    private function mergeCardAndDetail(array $card, array $detail): array
    {
        return [
            'slug' => $card['slug'],
            'title' => $detail['title'] ?? $card['title'],
            'bank_slug' => $detail['bank_slug'] ?? $card['bank_slug'],
            'bank_name' => $card['bank_name'],
            'currency' => $detail['currency'] ?? $card['currency'] ?? 'sum',
            'payment_system' => $detail['payment_system'] ?? $card['payment_system'],
            'card_type' => $detail['card_type'] ?? $card['card_type'],
            'category' => $detail['category'] ?? $card['category'],
            'issue_cost_display' => $detail['issue_cost_display'] ?? $card['issue_cost_display'],
            'validity_display' => $detail['validity_display'] ?? $card['validity_display'],
            'image_url' => $card['image_url'],
            'conditions' => $detail['conditions'],
            'special_conditions' => $detail['special_conditions'],
            'apply_url' => $detail['apply_url'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function upsertCard(array $data, int $sortOrder): void
    {
        DB::transaction(function () use ($data, $sortOrder): void {
            $bankId = $this->resolveBankId($data);

            $cardType = CardType::tryFromLabel(
                is_string($data['card_type'] ?? null) ? $data['card_type'] : null
            );

            $existing = Card::query()->where('slug', $data['slug'])->first();

            $card = Card::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'bank_id' => $bankId,
                    'title' => $data['title'],
                    'currency' => filled($data['currency'] ?? null) ? (string) $data['currency'] : 'sum',
                    'payment_system' => $data['payment_system'],
                    'card_type' => $cardType?->value,
                    'category' => $data['category'],
                    'issue_cost_display' => $data['issue_cost_display'],
                    'validity_display' => $data['validity_display'],
                    'special_conditions' => $data['special_conditions'],
                    'apply_url' => $data['apply_url'],
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ],
            );

            if (filled($data['image_url'] ?? null)) {
                $path = $this->downloadImage((string) $data['image_url'], $data['slug'], $existing?->image_path ?? $card->image_path);
                if ($path !== null) {
                    $card->image_path = $path;
                    $card->save();
                }
            }

            $card->conditions()->delete();
            foreach ($data['conditions'] as $index => $condition) {
                $card->conditions()->create([
                    'label' => $condition['label'],
                    'value' => $condition['value'],
                    'note' => $condition['note'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveBankId(array $data): ?int
    {
        $bank = null;

        if (filled($data['bank_slug'] ?? null)) {
            $bank = Bank::query()->where('slug', $data['bank_slug'])->first();
        }

        if ($bank === null && filled($data['bank_name'] ?? null)) {
            $bank = Bank::query()
                ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $data['bank_name'])])
                ->first();
        }

        if ($bank === null) {
            Log::warning('WP card import: bank not found', [
                'card_slug' => $data['slug'],
                'bank_slug' => $data['bank_slug'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
            ]);

            return null;
        }

        return $bank->id;
    }

    private function downloadImage(string $url, string $slug, ?string $existingPath): ?string
    {
        try {
            $response = Http::timeout($this->client->timeout())
                ->withHeaders(['User-Agent' => PultopWpClient::USER_AGENT])
                ->get($url);

            if (! $response->successful()) {
                Log::warning('Card image download failed', ['url' => $url, 'status' => $response->status()]);

                return null;
            }

            $body = $response->body();
            if ($body === '') {
                return null;
            }

            $extension = $this->guessExtension($url, (string) $response->header('Content-Type'));
            $path = 'cards/'.$slug.'.'.$extension;

            if ($existingPath && $existingPath !== $path) {
                Storage::disk('public')->delete($existingPath);
            }

            Storage::disk('public')->put($path, $body);

            return $path;
        } catch (Throwable $e) {
            Log::warning('Card image download exception', ['url' => $url, 'error' => $e->getMessage()]);

            return null;
        }
    }

    private function guessExtension(string $url, string $contentType): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $fromUrl = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($fromUrl, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
            return $fromUrl === 'jpeg' ? 'jpg' : $fromUrl;
        }

        $contentType = strtolower($contentType);
        if (str_contains($contentType, 'png')) {
            return 'png';
        }
        if (str_contains($contentType, 'webp')) {
            return 'webp';
        }
        if (str_contains($contentType, 'gif')) {
            return 'gif';
        }

        return 'jpg';
    }

    private function normalizeCurrencyCode(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $code = mb_strtolower(trim($value));

        return match ($code) {
            'uzs', 'сум', 'sum' => 'sum',
            'usd', '$' => 'usd',
            'eur', '€' => 'eur',
            'rub', '₽' => 'rub',
            'kzt' => 'kzt',
            'gbp' => 'gbp',
            'chf' => 'chf',
            'jpy' => 'jpy',
            default => $code,
        };
    }

    private function detailUrl(string $slug): string
    {
        return $this->client->baseUrl().'/cards/'.$slug.'/';
    }
}
