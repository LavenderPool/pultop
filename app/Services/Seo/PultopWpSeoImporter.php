<?php

namespace App\Services\Seo;

use App\Models\SeoPage;
use App\Services\SeoService;
use App\Services\Wp\PultopWpDom;
use Illuminate\Support\Facades\Http;

class PultopWpSeoImporter
{
    public function __construct(
        private readonly SeoService $seo,
    ) {}

    /**
     * @return array{
     *     ok_count: int,
     *     fail_count: int,
     *     pages: list<array{key: string, ok: bool, message: string, title: ?string, description: ?string, keywords: ?string, h1: ?string}>
     * }
     */
    public function import(bool $dryRun = false): array
    {
        /** @var array<string, array{label: string, source_path: string}> $catalog */
        $catalog = config('seo.pages', []);
        $baseUrl = rtrim((string) config('seo.wp_base_url', 'https://pultop.uz'), '/');
        $timeout = (int) config('seo.wp_timeout', 30);

        $okCount = 0;
        $failCount = 0;
        $pages = [];

        foreach ($catalog as $key => $meta) {
            $path = (string) ($meta['source_path'] ?? '/');
            $url = $baseUrl.($path === '/' ? '/' : '/'.ltrim($path, '/'));

            try {
                $response = Http::timeout($timeout)
                    ->withHeaders([
                        'User-Agent' => 'PultopSeoImporter/1.0',
                        'Accept' => 'text/html',
                    ])
                    ->get($url);

                if (! $response->successful()) {
                    $failCount++;
                    $pages[] = [
                        'key' => $key,
                        'ok' => false,
                        'message' => "HTTP {$response->status()} — {$url}",
                        'title' => null,
                        'description' => null,
                        'keywords' => null,
                        'h1' => null,
                    ];

                    continue;
                }

                $parsed = $this->parseHtml($response->body());

                if (! $dryRun) {
                    SeoPage::query()->updateOrCreate(
                        ['key' => $key],
                        [
                            'title' => $parsed['title'],
                            'description' => $parsed['description'],
                            'keywords' => $parsed['keywords'],
                            'h1' => $parsed['h1'],
                        ],
                    );
                    $this->seo->forgetCache($key);
                }

                $okCount++;
                $pages[] = [
                    'key' => $key,
                    'ok' => true,
                    'message' => $url,
                    'title' => $parsed['title'],
                    'description' => $parsed['description'],
                    'keywords' => $parsed['keywords'],
                    'h1' => $parsed['h1'],
                ];
            } catch (\Throwable $e) {
                $failCount++;
                $pages[] = [
                    'key' => $key,
                    'ok' => false,
                    'message' => $e->getMessage().' — '.$url,
                    'title' => null,
                    'description' => null,
                    'keywords' => null,
                    'h1' => null,
                ];
            }
        }

        return [
            'ok_count' => $okCount,
            'fail_count' => $failCount,
            'pages' => $pages,
        ];
    }

    /**
     * @return array{title: ?string, description: ?string, keywords: ?string, h1: ?string}
     */
    public function parseHtml(string $html): array
    {
        $xpath = PultopWpDom::xpath($html);

        $titleNode = $xpath->query('//title')->item(0);
        $title = $titleNode !== null
            ? PultopWpDom::normalizeText($titleNode->textContent)
            : null;

        $description = $this->metaContent($xpath, 'description');
        if ($description === null || $description === '') {
            $ogNodes = $xpath->query('//meta[@property="og:description"]/@content');
            if ($ogNodes !== false && $ogNodes->length > 0) {
                $description = PultopWpDom::normalizeText($ogNodes->item(0)?->nodeValue);
            }
        }

        $keywords = $this->metaContent($xpath, 'keywords');

        $h1 = null;
        $h1Nodes = $xpath->query('//h1');
        if ($h1Nodes !== false) {
            foreach ($h1Nodes as $node) {
                $text = PultopWpDom::normalizeText($node->textContent);
                if ($text !== '') {
                    $h1 = $text;
                    break;
                }
            }
        }

        if ($title === '') {
            $title = null;
        }
        if ($description === '') {
            $description = null;
        }
        if ($keywords === '') {
            $keywords = null;
        }
        if ($h1 === '') {
            $h1 = null;
        }

        if ($description !== null && mb_strlen($description) > 500) {
            $description = mb_substr($description, 0, 500);
        }
        if ($keywords !== null && mb_strlen($keywords) > 500) {
            $keywords = mb_substr($keywords, 0, 500);
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'h1' => $h1,
        ];
    }

    private function metaContent(\DOMXPath $xpath, string $name): ?string
    {
        $expression = sprintf(
            '//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="%s"]/@content',
            strtolower($name),
        );
        $nodes = $xpath->query($expression);
        if ($nodes === false || $nodes->length === 0) {
            return null;
        }

        return PultopWpDom::normalizeText($nodes->item(0)?->nodeValue);
    }
}
