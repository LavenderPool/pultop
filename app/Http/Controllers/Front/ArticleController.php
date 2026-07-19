<?php

namespace App\Http\Controllers\Front;

use App\Enums\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ArticleService;
use App\Services\SeoService;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articles,
        private readonly SeoService $seo,
    ) {}

    public function index(): View
    {
        $seo = $this->seo->resolve('articles.index', 'Статьи и новости');

        return view('public.articles.index', [
            'articles' => $this->articles->paginatePublished(),
            'categories' => ArticleCategory::cases(),
            'activeCategory' => null,
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
        ]);
    }

    public function category(string $category): View
    {
        $resolved = ArticleCategory::fromPublicSlug($category);
        if ($resolved === null) {
            abort(404);
        }

        $seo = $this->seo->resolve(
            'articles.category.'.$resolved->publicSlug(),
            $resolved->label(),
        );

        return view('public.articles.index', [
            'articles' => $this->articles->paginatePublished($resolved),
            'categories' => ArticleCategory::cases(),
            'activeCategory' => $resolved,
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
        ]);
    }

    public function show(Article $article): View
    {
        if (! $article->is_published) {
            abort(404);
        }

        return view('public.articles.show', [
            'article' => $article,
            'title' => $article->pageTitle(),
            'metaDescription' => $article->meta_description,
        ]);
    }
}
