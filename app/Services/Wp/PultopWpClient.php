<?php

namespace App\Services\Wp;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PultopWpClient
{
    public const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout,
        private readonly string $ajaxPath,
    ) {}

    public static function fromConfigKey(string $key): self
    {
        return new self(
            rtrim((string) config("{$key}.wp_base_url"), '/'),
            max(5, (int) config("{$key}.wp_timeout", 30)),
            (string) config("{$key}.wp_ajax_path", '/wp-admin/admin-ajax.php'),
        );
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function timeout(): int
    {
        return $this->timeout;
    }

    public function getHtml(string $url, ?string $referer = null): string
    {
        $headers = [
            'User-Agent' => self::USER_AGENT,
            'Accept' => 'text/html',
        ];
        if ($referer !== null) {
            $headers['Referer'] = $referer;
        }

        $response = Http::timeout($this->timeout)
            ->withHeaders($headers)
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Не удалось загрузить '.$url.' (HTTP '.$response->status().')');
        }

        return $response->body();
    }

    /**
     * Параллельная загрузка HTML.
     *
     * @param  array<string, string>  $urls  key => url
     * @return array<string, string|\Throwable> key => body или исключение
     */
    public function getHtmlMany(array $urls, ?string $referer = null): array
    {
        if ($urls === []) {
            return [];
        }

        $headers = [
            'User-Agent' => self::USER_AGENT,
            'Accept' => 'text/html',
        ];
        if ($referer !== null) {
            $headers['Referer'] = $referer;
        }

        $timeout = $this->timeout;
        $responses = Http::pool(function ($pool) use ($urls, $headers, $timeout) {
            foreach ($urls as $key => $url) {
                $pool->as((string) $key)
                    ->timeout($timeout)
                    ->withHeaders($headers)
                    ->get($url);
            }
        });

        $result = [];
        foreach ($urls as $key => $url) {
            $response = $responses[(string) $key] ?? null;
            if ($response instanceof \Throwable) {
                $result[(string) $key] = $response;

                continue;
            }
            if ($response === null || ! $response->successful()) {
                $status = is_object($response) && method_exists($response, 'status')
                    ? $response->status()
                    : 0;
                $result[(string) $key] = new RuntimeException(
                    'Не удалось загрузить '.$url.' (HTTP '.$status.')',
                );

                continue;
            }
            $result[(string) $key] = $response->body();
        }

        return $result;
    }

    public function extractNonce(string $html, string $errorLabel): string
    {
        if (! preg_match('/name=["\']nonce["\']\s+value=["\']([^"\']+)["\']/', $html, $m)
            && ! preg_match('/value=["\']([^"\']+)["\']\s+name=["\']nonce["\']/', $html, $m)
            && ! preg_match('/["\']nonce["\']\s*:\s*["\']([^"\']+)["\']/', $html, $m)) {
            throw new RuntimeException('Nonce не найден на странице '.$errorLabel);
        }

        return $m[1];
    }

    public function refreshNonce(string $listUrl): string
    {
        $html = $this->getHtml($listUrl, $listUrl);

        return $this->extractNonce($html, $listUrl);
    }

    /**
     * @param  array<string, scalar|null>  $fields  доп. поля формы (без action/paged/nonce)
     */
    public function postLoadMore(
        string $action,
        int $page,
        string &$nonce,
        string $referer,
        array $fields,
        bool $retryNonce = true,
    ): string {
        $ajaxUrl = $this->baseUrl.$this->ajaxPath;

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html, */*;q=0.8',
                'X-Requested-With' => 'XMLHttpRequest',
                'Origin' => $this->baseUrl,
                'Referer' => $referer,
            ])
            ->asForm()
            ->post($ajaxUrl, array_merge($fields, [
                'action' => $action,
                'paged' => $page,
                'nonce' => $nonce,
            ]));

        if (! $response->successful()) {
            throw new RuntimeException('HTTP '.$response->status().' при '.$action);
        }

        $body = $response->body();
        $trimmed = trim($body);

        if ($trimmed === 'Stop!' || $trimmed === 'Invalid referer' || str_starts_with($trimmed, 'Invalid request')) {
            if (! $retryNonce) {
                throw new RuntimeException($trimmed !== '' ? $trimmed : 'Invalid request');
            }

            $nonce = $this->refreshNonce($referer);

            return $this->postLoadMore(
                $action,
                $page,
                $nonce,
                $referer,
                $fields,
                retryNonce: false,
            );
        }

        return $body;
    }

    /**
     * Постраничный ajax load_more: цикл до пустой страницы (парсер вернул []).
     *
     * @param  array<string, scalar|null>  $fields
     * @param  callable(string): list<mixed>  $parsePage
     * @return list<mixed>
     */
    public function fetchAllPages(
        string $action,
        string $listUrl,
        array $fields,
        callable $parsePage,
        int $startPage = 1,
        ?string &$nonce = null,
    ): array {
        $nonce ??= $this->refreshNonce($listUrl);
        $items = [];
        $page = max(1, $startPage);

        while (true) {
            $html = $this->postLoadMore($action, $page, $nonce, $listUrl, $fields);
            $pageItems = $parsePage($html);

            if ($pageItems === []) {
                break;
            }

            foreach ($pageItems as $item) {
                $items[] = $item;
            }

            $page++;
        }

        return $items;
    }
}
