<?php

namespace App\Models;

use App\Enums\ArticleCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'wp_id',
        'category',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_path',
        'meta_title',
        'meta_description',
        'published_at',
        'is_published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ArticleCategory::class,
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'wp_id' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function coverUrl(): ?string
    {
        if ($this->cover_path === null || $this->cover_path === '') {
            return null;
        }

        return Storage::disk('public')->url($this->cover_path);
    }

    public function pageTitle(): string
    {
        return filled($this->meta_title) ? (string) $this->meta_title : $this->title;
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopeInCategory(Builder $query, ArticleCategory $category): Builder
    {
        return $query->where('category', $category->value);
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }
}
