<?php

namespace App\Http\Controllers\Front;

use App\Enums\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articles,
    ) {}

    public function index(): View
    {
        return view('public.articles.index', [
            'articles' => $this->articles->paginatePublished(),
            'categories' => ArticleCategory::cases(),
            'activeCategory' => null,
            'title' => 'Статьи и новости',
        ]);
    }

    public function category(string $category): View
    {
        $resolved = ArticleCategory::fromPublicSlug($category);
        if ($resolved === null) {
            abort(404);
        }

        return view('public.articles.index', [
            'articles' => $this->articles->paginatePublished($resolved),
            'categories' => ArticleCategory::cases(),
            'activeCategory' => $resolved,
            'title' => $resolved->label(),
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
