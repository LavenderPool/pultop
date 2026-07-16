<?php

namespace App\Services\Banks;

use App\Models\Bank;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PultopWpBanksImporter
{
    private const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * @return array{
     *     ok_count: int,
     *     fail_count: int,
     *     errors: list<string>,
     *     banks: list<array{slug: string, name: string, ok: bool, message: string}>
     * }
     */
    public function import(bool $dryRun = false, ?int $limit = null): array
    {
        $ok = 0;
        $fail = 0;
        $errors = [];
        $results = [];

        $cards = $this->fetchListCards();

        if ($limit !== null && $limit > 0) {
            $cards = array_slice($cards, 0, $limit);
        }

        $delayMs = max(0, (int) config('banks.parse_delay_ms', 300));

        foreach ($cards as $index => $card) {
            if ($index > 0 && $delayMs > 0) {
                usleep($delayMs * 1000);
            }

            try {
                $detail = $this->fetchDetail($card['slug']);
                $merged = $this->mergeCardAndDetail($card, $detail);

                if (! $dryRun) {
                    $this->upsertBank($merged, $index + 1);
                }

                $ok++;
                $results[] = [
                    'slug' => $merged['slug'],
                    'name' => $merged['name'],
                    'ok' => true,
                    'message' => $dryRun ? 'dry-run OK' : 'OK',
                ];
            } catch (Throwable $e) {
                $fail++;
                $message = $e->getMessage();
                $errors[] = "{$card['slug']}: {$message}";
                $results[] = [
                    'slug' => $card['slug'],
                    'name' => $card['name'],
                    'ok' => false,
                    'message' => $message,
                ];
                Log::warning('WP bank import failed', [
                    'slug' => $card['slug'],
                    'error' => $message,
                ]);
            }
        }

        return [
            'ok_count' => $ok,
            'fail_count' => $fail,
            'errors' => $errors,
            'banks' => $results,
        ];
    }

    /**
     * @return list<array{slug: string, name: string, address: ?string, logo_url: ?string}>
     */
    private function fetchListCards(): array
    {
        $html = $this->getHtml($this->listUrl());
        $xpath = $this->xpath($html);

        $nodes = $xpath->query('//section[contains(@class,"items-list")]//div[contains(@class,"item-content")]');
        if ($nodes === false || $nodes->length === 0) {
            throw new RuntimeException('Не найдены карточки банков на странице /banks/');
        }

        $cards = [];

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $link = $xpath->query('.//a[contains(@class,"link") or contains(@href,"/banks/")]', $node)->item(0);
            $href = $link instanceof DOMElement ? trim((string) $link->getAttribute('href')) : '';
            $slug = $this->slugFromHref($href);

            if ($slug === null) {
                continue;
            }

            $nameNode = $xpath->query('.//h3', $node)->item(0);
            $name = $nameNode !== null ? $this->normalizeText($nameNode->textContent) : '';

            if ($name === '') {
                continue;
            }

            $addressNode = $xpath->query('.//p', $node)->item(0);
            $address = $addressNode !== null ? $this->normalizeText($addressNode->textContent) : null;
            if ($address === '') {
                $address = null;
            }

            $img = $xpath->query('.//img', $node)->item(0);
            $logoUrl = null;
            if ($img instanceof DOMElement) {
                $src = trim((string) $img->getAttribute('src'));
                if ($src !== '') {
                    $logoUrl = $this->absoluteUrl($src);
                }
            }

            $cards[] = [
                'slug' => $slug,
                'name' => $name,
                'address' => $address,
                'logo_url' => $logoUrl,
            ];
        }

        if ($cards === []) {
            throw new RuntimeException('Список банков пуст после разбора HTML');
        }

        return $cards;
    }

    /**
     * @return array{
     *     description: ?string,
     *     address: ?string,
     *     website: ?string,
     *     license: ?string,
     *     mfo: ?string,
     *     inn: ?string,
     *     logo_url: ?string
     * }
     */
    private function fetchDetail(string $slug): array
    {
        $html = $this->getHtml($this->detailUrl($slug));
        $xpath = $this->xpath($html);

        $description = $this->extractDescription($xpath);

        $fields = [
            'address' => null,
            'website' => null,
            'license' => null,
            'mfo' => null,
            'inn' => null,
        ];

        $items = $xpath->query('//li[contains(@class,"item-detail-item")]');
        if ($items !== false) {
            foreach ($items as $item) {
                if (! $item instanceof DOMElement) {
                    continue;
                }

                $labelNode = $xpath->query('.//*[contains(@class,"item-detail-item-label")]', $item)->item(0);
                $valueNode = $xpath->query('.//*[contains(@class,"item-detail-item-text")]', $item)->item(0);
                $label = $labelNode !== null ? mb_strtolower($this->normalizeText($labelNode->textContent)) : '';
                $value = $valueNode !== null ? $this->normalizeText($valueNode->textContent) : '';

                if ($value === '') {
                    continue;
                }

                if (str_contains($label, 'адрес')) {
                    $fields['address'] = $value;
                } elseif (str_contains($label, 'сайт')) {
                    $fields['website'] = $value;
                } elseif (str_contains($label, 'лиценз')) {
                    $fields['license'] = $value;
                } elseif ($label === 'мфо' || str_contains($label, 'мфо')) {
                    $fields['mfo'] = $value;
                } elseif ($label === 'инн' || str_contains($label, 'инн')) {
                    $fields['inn'] = $value;
                }
            }
        }

        $logoUrl = null;
        $og = $xpath->query('//meta[@property="og:image"]/@content')->item(0);
        if ($og !== null) {
            $logoUrl = $this->absoluteUrl(trim($og->nodeValue ?? ''));
        }

        if ($logoUrl === null) {
            $img = $xpath->query('//*[contains(@class,"small-bank-img")]//img')->item(0);
            if ($img instanceof DOMElement) {
                $src = trim((string) $img->getAttribute('src'));
                if ($src !== '') {
                    $logoUrl = $this->absoluteUrl($src);
                }
            }
        }

        return [
            'description' => $description,
            'address' => $fields['address'],
            'website' => $fields['website'],
            'license' => $fields['license'],
            'mfo' => $fields['mfo'],
            'inn' => $fields['inn'],
            'logo_url' => $logoUrl,
        ];
    }

    /**
     * @param  array{slug: string, name: string, address: ?string, logo_url: ?string}  $card
     * @param  array{description: ?string, address: ?string, website: ?string, license: ?string, mfo: ?string, inn: ?string, logo_url: ?string}  $detail
     * @return array{
     *     slug: string,
     *     name: string,
     *     address: ?string,
     *     description: ?string,
     *     website: ?string,
     *     license: ?string,
     *     mfo: ?string,
     *     inn: ?string,
     *     logo_url: ?string
     * }
     */
    private function mergeCardAndDetail(array $card, array $detail): array
    {
        $logoUrl = $card['logo_url'];
        if ($this->isBetterLogoUrl($detail['logo_url'], $logoUrl)) {
            $logoUrl = $detail['logo_url'];
        }

        return [
            'slug' => $card['slug'],
            'name' => $card['name'],
            'address' => $detail['address'] ?? $card['address'],
            'description' => $detail['description'],
            'website' => $detail['website'],
            'license' => $detail['license'],
            'mfo' => $detail['mfo'],
            'inn' => $detail['inn'],
            'logo_url' => $logoUrl,
        ];
    }

    /**
     * @param  array{
     *     slug: string,
     *     name: string,
     *     address: ?string,
     *     description: ?string,
     *     website: ?string,
     *     license: ?string,
     *     mfo: ?string,
     *     inn: ?string,
     *     logo_url: ?string
     * }  $data
     */
    private function upsertBank(array $data, int $sortOrder): void
    {
        $bank = Bank::query()
            ->where(function ($query) use ($data): void {
                $query->where('slug', $data['slug'])
                    ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($data['name'])]);
            })
            ->first();

        $payload = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'address' => $data['address'],
            'description' => $data['description'],
            'website' => $data['website'],
            'license' => $data['license'],
            'mfo' => $data['mfo'],
            'inn' => $data['inn'],
            'is_active' => true,
            'sort_order' => $sortOrder,
        ];

        if ($bank === null) {
            $bank = new Bank($payload);
            $bank->parser_code = null;
            $bank->rates_url = null;
            $bank->save();
        } else {
            $bank->fill($payload);
            $bank->save();
        }

        if (filled($data['logo_url'])) {
            $logoPath = $this->downloadLogo((string) $data['logo_url'], $data['slug'], $bank->logo_path);
            if ($logoPath !== null) {
                $bank->logo_path = $logoPath;
                $bank->save();
            }
        }
    }

    private function downloadLogo(string $url, string $slug, ?string $existingPath): ?string
    {
        try {
            $response = Http::timeout((int) config('banks.wp_timeout', 30))
                ->withHeaders(['User-Agent' => self::USER_AGENT])
                ->get($url);

            if (! $response->successful()) {
                Log::warning('Bank logo download failed', ['url' => $url, 'status' => $response->status()]);

                return null;
            }

            $body = $response->body();
            if ($body === '') {
                return null;
            }

            $extension = $this->guessExtension($url, (string) $response->header('Content-Type'));
            $path = 'banks/'.$slug.'.'.$extension;

            if ($existingPath && $existingPath !== $path) {
                Storage::disk('public')->delete($existingPath);
            }

            Storage::disk('public')->put($path, $body);

            return $path;
        } catch (Throwable $e) {
            Log::warning('Bank logo download exception', ['url' => $url, 'error' => $e->getMessage()]);

            return null;
        }
    }

    private function guessExtension(string $url, string $contentType): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $fromUrl = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($fromUrl, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'], true)) {
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
        if (str_contains($contentType, 'svg')) {
            return 'svg';
        }

        return 'jpg';
    }

    private function isBetterLogoUrl(?string $candidate, ?string $current): bool
    {
        if ($candidate === null || $candidate === '') {
            return false;
        }

        if ($current === null || $current === '') {
            return true;
        }

        $candidateHasThumb = (bool) preg_match('/-\d+x\d+\./', $candidate);
        $currentHasThumb = (bool) preg_match('/-\d+x\d+\./', $current);

        return $currentHasThumb && ! $candidateHasThumb;
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

    private function listUrl(): string
    {
        return rtrim((string) config('banks.wp_base_url'), '/').'/'.ltrim((string) config('banks.wp_list_path', '/banks/'), '/');
    }

    private function detailUrl(string $slug): string
    {
        return rtrim((string) config('banks.wp_base_url'), '/').'/banks/'.rawurlencode($slug).'/';
    }

    private function slugFromHref(string $href): ?string
    {
        if ($href === '') {
            return null;
        }

        $path = parse_url($href, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            $path = $href;
        }

        if (! preg_match('#/banks/([^/]+)/?#', $path, $matches)) {
            return null;
        }

        $slug = Str::lower(urldecode($matches[1]));

        return $slug !== '' && $slug !== 'feed' ? $slug : null;
    }

    private function absoluteUrl(string $url): string
    {
        if ($url === '') {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return rtrim((string) config('banks.wp_base_url'), '/').'/'.ltrim($url, '/');
    }

    private function extractDescription(DOMXPath $xpath): ?string
    {
        $descNode = $xpath->query('//*[@id="_r_s_" or contains(@class,"section__text")]')->item(0);
        if ($descNode !== null) {
            $text = $this->normalizeText($descNode->textContent);
            if ($text !== '') {
                return $text;
            }
        }

        $paragraphs = [];
        $nodes = $xpath->query('//div[@id="content"]//h2[1]/following-sibling::*[not(contains(concat(" ", normalize-space(@class), " "), " info-conditions "))]');
        if ($nodes === false) {
            return null;
        }

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            if (str_contains(' '.$node->getAttribute('class').' ', ' info-conditions ')) {
                break;
            }

            $tag = strtolower($node->tagName);
            if ($tag === 'div' && str_contains(' '.$node->getAttribute('class').' ', ' info-conditions ')) {
                break;
            }

            if ($tag === 'p') {
                $nested = $xpath->query('.//p', $node);
                if ($nested !== false && $nested->length > 0) {
                    foreach ($nested as $inner) {
                        $text = $this->normalizeText($inner->textContent);
                        if ($text !== '') {
                            $paragraphs[] = $text;
                        }
                    }
                } else {
                    $text = $this->normalizeText($node->textContent);
                    if ($text !== '') {
                        $paragraphs[] = $text;
                    }
                }

                continue;
            }

            if ($tag === 'div' || $tag === 'section') {
                break;
            }
        }

        if ($paragraphs === []) {
            return null;
        }

        return implode("\n\n", array_values(array_unique($paragraphs)));
    }

    private function normalizeText(?string $text): string
    {
        $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
