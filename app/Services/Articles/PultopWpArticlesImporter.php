<?php

namespace App\Services\Articles;

use App\Enums\ArticleCategory;
use App\Models\Article;
use App\Services\PublicCacheService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PultopWpArticlesImporter
{
    private const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    public function __construct(
        private readonly PublicCacheService $cache,
    ) {}

    /**
     * @return array{
     *     ok_count: int,
     *     fail_count: int,
     *     errors: list<string>,
     *     articles: list<array{slug: string, title: string, ok: bool, message: string}>
     * }
     */
    public function import(bool $dryRun = false, ?int $limit = null, ?ArticleCategory $category = null): array
    {
        $ok = 0;
        $fail = 0;
        $errors = [];
        $results = [];
        $processed = 0;

        $wpCategoryIds = $category !== null
            ? [$category->wpId()]
            : array_map(fn (ArticleCategory $c) => $c->wpId(), ArticleCategory::cases());

        $page = 1;
        $perPage = max(1, min(100, (int) config('articles.per_page', 100)));
        $delayMs = max(0, (int) config('articles.parse_delay_ms', 200));

        while (true) {
            $posts = $this->fetchPostsPage($wpCategoryIds, $page, $perPage);

            if ($posts === []) {
                break;
            }

            foreach ($posts as $post) {
                if ($limit !== null && $processed >= $limit) {
                    break 2;
                }

                if ($delayMs > 0 && $processed > 0) {
                    usleep($delayMs * 1000);
                }

                $processed++;

                try {
                    $mapped = $this->mapPost($post);

                    if (! $dryRun) {
                        $this->upsertArticle($mapped);
                    }

                    $ok++;
                    $results[] = [
                        'slug' => $mapped['slug'],
                        'title' => $mapped['title'],
                        'ok' => true,
                        'message' => $dryRun ? 'dry-run OK' : 'OK',
                    ];
                } catch (Throwable $e) {
                    $fail++;
                    $slug = (string) ($post['slug'] ?? 'unknown');
                    $title = html_entity_decode(strip_tags((string) data_get($post, 'title.rendered', $slug)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $message = $e->getMessage();
                    $errors[] = "{$slug}: {$message}";
                    $results[] = [
                        'slug' => $slug,
                        'title' => $title,
                        'ok' => false,
                        'message' => $message,
                    ];
                    Log::warning('WP article import failed', [
                        'slug' => $slug,
                        'error' => $message,
                    ]);
                }
            }

            if (count($posts) < $perPage) {
                break;
            }

            $page++;
        }

        if (! $dryRun && $ok > 0) {
            $this->cache->forgetGroup(PublicCacheService::GROUP_ARTICLES);
        }

        return [
            'ok_count' => $ok,
            'fail_count' => $fail,
            'errors' => $errors,
            'articles' => $results,
        ];
    }

    /**
     * @param  list<int>  $categoryIds
     * @return list<array<string, mixed>>
     */
    private function fetchPostsPage(array $categoryIds, int $page, int $perPage): array
    {
        $url = $this->baseUrl().'/wp-json/wp/v2/posts';

        $response = Http::timeout((int) config('articles.wp_timeout', 30))
            ->withHeaders(['User-Agent' => self::USER_AGENT])
            ->acceptJson()
            ->get($url, [
                'categories' => implode(',', $categoryIds),
                'per_page' => $perPage,
                'page' => $page,
                '_embed' => 1,
                'status' => 'publish',
            ]);

        if ($response->status() === 400 && $page > 1) {
            return [];
        }

        if (! $response->successful()) {
            throw new RuntimeException("WP REST posts HTTP {$response->status()} (page {$page})");
        }

        $json = $response->json();
        if (! is_array($json)) {
            throw new RuntimeException('WP REST вернул некорректный JSON');
        }

        /** @var list<array<string, mixed>> $json */
        return $json;
    }

    /**
     * @param  array<string, mixed>  $post
     * @return array{
     *     wp_id: int,
     *     category: ArticleCategory,
     *     title: string,
     *     slug: string,
     *     excerpt: ?string,
     *     body: string,
     *     cover_url: ?string,
     *     meta_title: ?string,
     *     meta_description: ?string,
     *     published_at: string
     * }
     */
    private function mapPost(array $post): array
    {
        $wpId = (int) ($post['id'] ?? 0);
        if ($wpId <= 0) {
            throw new RuntimeException('У поста нет id');
        }

        $wpCategories = array_map('intval', $post['categories'] ?? []);
        $category = ArticleCategory::resolveFromWpIds($wpCategories);
        if ($category === null) {
            throw new RuntimeException('Категория поста не входит в целевые (24/50/35/36)');
        }

        $title = html_entity_decode(strip_tags((string) data_get($post, 'title.rendered', '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $title = trim($title);
        if ($title === '') {
            throw new RuntimeException('Пустой заголовок');
        }

        $slug = Str::slug((string) ($post['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($title);
        }
        if ($slug === '') {
            $slug = 'post-'.$wpId;
        }

        $body = (string) data_get($post, 'content.rendered', '');
        $excerptHtml = (string) data_get($post, 'excerpt.rendered', '');
        $excerpt = trim(html_entity_decode(strip_tags($excerptHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($excerpt === '') {
            $excerpt = null;
        }

        $yoast = is_array($post['yoast_head_json'] ?? null) ? $post['yoast_head_json'] : [];
        $metaTitle = isset($yoast['title']) ? trim((string) $yoast['title']) : null;
        $metaDescription = isset($yoast['description']) ? trim((string) $yoast['description']) : null;
        if ($metaTitle === '') {
            $metaTitle = null;
        }
        if ($metaDescription === '') {
            $metaDescription = null;
        }

        $publishedAt = (string) ($post['date'] ?? now()->toDateTimeString());
        $coverUrl = $this->featuredImageUrl($post);

        return [
            'wp_id' => $wpId,
            'category' => $category,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'body' => $body,
            'cover_url' => $coverUrl,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'published_at' => $publishedAt,
        ];
    }

    /**
     * @param  array{
     *     wp_id: int,
     *     category: ArticleCategory,
     *     title: string,
     *     slug: string,
     *     excerpt: ?string,
     *     body: string,
     *     cover_url: ?string,
     *     meta_title: ?string,
     *     meta_description: ?string,
     *     published_at: string
     * }  $data
     */
    private function upsertArticle(array $data): void
    {
        $existing = Article::query()->where('wp_id', $data['wp_id'])->first();
        $slug = $this->uniqueSlug($data['slug'], $existing?->id);

        $coverPath = $existing?->cover_path;
        if ($data['cover_url'] !== null) {
            $downloaded = $this->downloadCover($data['cover_url'], $slug, $coverPath);
            if ($downloaded !== null) {
                $coverPath = $downloaded;
            }
        }

        $payload = [
            'wp_id' => $data['wp_id'],
            'category' => $data['category']->value,
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'],
            'body' => $data['body'],
            'cover_path' => $coverPath,
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'published_at' => $data['published_at'],
            'is_published' => true,
        ];

        if ($existing !== null) {
            $existing->update($payload);

            return;
        }

        Article::query()->create($payload);
    }

    private function uniqueSlug(string $slug, ?int $ignoreId): string
    {
        $base = $slug;
        $suffix = 0;

        while (true) {
            $candidate = $suffix === 0 ? $base : $base.'-'.$suffix;
            $query = Article::query()->where('slug', $candidate);
            if ($ignoreId !== null) {
                $query->where('id', '!=', $ignoreId);
            }

            if (! $query->exists()) {
                return $candidate;
            }

            $suffix++;
        }
    }

    /**
     * @param  array<string, mixed>  $post
     */
    private function featuredImageUrl(array $post): ?string
    {
        $media = data_get($post, '_embedded.wp:featuredmedia.0');
        if (! is_array($media)) {
            return null;
        }

        $url = trim((string) ($media['source_url'] ?? ''));

        return $url !== '' ? $url : null;
    }

    private function downloadCover(string $url, string $slug, ?string $existingPath): ?string
    {
        try {
            $response = Http::timeout((int) config('articles.wp_timeout', 30))
                ->withHeaders(['User-Agent' => self::USER_AGENT])
                ->get($url);

            if (! $response->successful()) {
                Log::warning('Article cover download failed', ['url' => $url, 'status' => $response->status()]);

                return null;
            }

            $body = $response->body();
            if ($body === '') {
                return null;
            }

            $extension = $this->guessExtension($url, (string) $response->header('Content-Type'));
            $path = 'articles/'.$slug.'.'.$extension;

            if ($existingPath && $existingPath !== $path) {
                Storage::disk('public')->delete($existingPath);
            }

            Storage::disk('public')->put($path, $body);

            return $path;
        } catch (Throwable $e) {
            Log::warning('Article cover download exception', ['url' => $url, 'error' => $e->getMessage()]);

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

        return 'jpg';
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('articles.wp_base_url', 'https://pultop.uz'), '/');
    }
}
