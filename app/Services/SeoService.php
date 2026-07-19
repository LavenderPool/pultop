<?php

namespace App\Services;

use App\Models\SeoPage;
use Illuminate\Support\Facades\Cache;

class SeoService
{
    private const CACHE_PREFIX = 'seo_page.';

    public function forKey(string $key): ?SeoPage
    {
        return Cache::rememberForever(self::CACHE_PREFIX.$key, function () use ($key) {
            return SeoPage::query()->where('key', $key)->first();
        });
    }

    /**
     * @return array{title: string, h1: string, metaDescription: ?string, metaKeywords: ?string}
     */
    public function resolve(
        string $key,
        string $fallbackTitle,
        bool $appendAppName = true,
        ?string $fallbackH1 = null,
    ): array {
        $page = $this->forKey($key);

        if (filled($page?->title)) {
            $title = (string) $page->title;
        } elseif ($appendAppName) {
            $title = $fallbackTitle.' - '.config('app.name', 'Pultop');
        } else {
            $title = $fallbackTitle;
        }

        if (filled($page?->h1)) {
            $h1 = (string) $page->h1;
        } elseif ($fallbackH1 !== null) {
            $h1 = $fallbackH1;
        } else {
            $h1 = $fallbackTitle;
        }

        $metaDescription = filled($page?->description) ? (string) $page->description : null;
        $metaKeywords = filled($page?->keywords) ? (string) $page->keywords : null;

        return [
            'title' => $title,
            'h1' => $h1,
            'metaDescription' => $metaDescription,
            'metaKeywords' => $metaKeywords,
        ];
    }

    /**
     * @return list<array{
     *     key: string,
     *     label: string,
     *     title: string,
     *     description: string,
     *     keywords: string,
     *     h1: string
     * }>
     */
    public function pagesForAdmin(): array
    {
        /** @var array<string, array{label: string, source_path: string}> $catalog */
        $catalog = config('seo.pages', []);

        $stored = SeoPage::query()
            ->whereIn('key', array_keys($catalog))
            ->get()
            ->keyBy('key');

        $pages = [];

        foreach ($catalog as $key => $meta) {
            $row = $stored->get($key);

            $pages[] = [
                'key' => $key,
                'label' => (string) ($meta['label'] ?? $key),
                'title' => (string) ($row?->title ?? ''),
                'description' => (string) ($row?->description ?? ''),
                'keywords' => (string) ($row?->keywords ?? ''),
                'h1' => (string) ($row?->h1 ?? ''),
            ];
        }

        return $pages;
    }

    /**
     * @param  array<int, array{key: string, title?: ?string, description?: ?string, keywords?: ?string, h1?: ?string}>  $pages
     */
    public function updateMany(array $pages): void
    {
        /** @var array<string, array{label: string, source_path: string}> $catalog */
        $catalog = config('seo.pages', []);

        foreach ($pages as $page) {
            $key = (string) ($page['key'] ?? '');
            if ($key === '' || ! array_key_exists($key, $catalog)) {
                continue;
            }

            SeoPage::query()->updateOrCreate(
                ['key' => $key],
                [
                    'title' => $this->nullableString($page['title'] ?? null),
                    'description' => $this->nullableString($page['description'] ?? null),
                    'keywords' => $this->nullableString($page['keywords'] ?? null),
                    'h1' => $this->nullableString($page['h1'] ?? null),
                ],
            );

            Cache::forget(self::CACHE_PREFIX.$key);
        }
    }

    public function forgetCache(string $key): void
    {
        Cache::forget(self::CACHE_PREFIX.$key);
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }
}
