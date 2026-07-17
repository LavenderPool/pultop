<?php

namespace App\Services;

use App\Enums\ArticleCategory;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleService
{
    public function __construct(
        private readonly PublicCacheService $cache,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $cover = null): Article
    {
        $article = DB::transaction(function () use ($data, $cover) {
            $data = $this->normalize($data);

            if ($cover !== null) {
                $data['cover_path'] = $cover->store('articles', 'public');
            }

            return Article::query()->create($data);
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_ARTICLES);

        return $article;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Article $article, array $data, ?UploadedFile $cover = null): Article
    {
        $article = DB::transaction(function () use ($article, $data, $cover) {
            $data = $this->normalize($data, $article);

            if ($cover !== null) {
                if ($article->cover_path) {
                    Storage::disk('public')->delete($article->cover_path);
                }
                $data['cover_path'] = $cover->store('articles', 'public');
            }

            $article->update($data);

            return $article->fresh();
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_ARTICLES);

        return $article;
    }

    public function delete(Article $article): void
    {
        DB::transaction(function () use ($article): void {
            if ($article->cover_path) {
                Storage::disk('public')->delete($article->cover_path);
            }

            $article->delete();
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_ARTICLES);
    }

    /**
     * @return LengthAwarePaginator<int, Article>
     */
    public function paginatePublished(?ArticleCategory $category = null, int $perPage = 12): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();

        return $this->cache->remember(
            PublicCacheService::GROUP_ARTICLES,
            $this->cache->key(['paginate', $category?->value, $perPage, $page]),
            function () use ($category, $perPage, $page) {
                $query = Article::query()->published()->ordered();

                if ($category !== null) {
                    $query->inCategory($category);
                }

                return $query->paginate($perPage, ['*'], 'page', $page);
            },
        );
    }

    /**
     * @return array<string, Collection<int, Article>>
     */
    public function homepageByCategory(?int $limit = null): array
    {
        $limit ??= max(1, (int) config('articles.homepage_limit', 6));

        return $this->cache->remember(
            PublicCacheService::GROUP_ARTICLES,
            $this->cache->key(['homepage', $limit]),
            function () use ($limit) {
                $result = [];

                foreach (ArticleCategory::cases() as $category) {
                    $result[$category->value] = Article::query()
                        ->published()
                        ->inCategory($category)
                        ->ordered()
                        ->limit($limit)
                        ->get();
                }

                return $result;
            },
        );
    }

    /**
     * @return Collection<int, Article>
     */
    public function sidebarNews(?int $limit = null): Collection
    {
        $limit ??= max(1, (int) config('articles.sidebar_limit', 5));

        return $this->cache->remember(
            PublicCacheService::GROUP_ARTICLES,
            $this->cache->key(['sidebar_news', $limit]),
            fn () => Article::query()
                ->published()
                ->inCategory(ArticleCategory::News)
                ->ordered()
                ->limit($limit)
                ->get(),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data, ?Article $existing = null): array
    {
        if (empty($data['slug']) && ! empty($data['title'])) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        if (empty($data['slug'])) {
            $data['slug'] = 'article-'.Str::lower(Str::random(8));
        } else {
            $data['slug'] = Str::slug((string) $data['slug']);
        }

        $data['is_published'] = filter_var($data['is_published'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $data['excerpt'] = filled($data['excerpt'] ?? null) ? (string) $data['excerpt'] : null;
        $data['meta_title'] = filled($data['meta_title'] ?? null) ? (string) $data['meta_title'] : null;
        $data['meta_description'] = filled($data['meta_description'] ?? null) ? (string) $data['meta_description'] : null;
        $data['published_at'] = filled($data['published_at'] ?? null)
            ? $data['published_at']
            : ($existing?->published_at ?? now());

        if (isset($data['category']) && $data['category'] instanceof ArticleCategory) {
            $data['category'] = $data['category']->value;
        }

        unset($data['cover'], $data['wp_id']);

        return $data;
    }
}
