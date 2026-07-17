<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Article\StoreArticleRequest;
use App\Http\Requests\Admin\Article\UpdateArticleRequest;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articles,
    ) {}

    public function index(Request $request): Response
    {
        $categoryFilter = $request->string('category')->toString();
        $category = filled($categoryFilter)
            ? ArticleCategory::tryFrom($categoryFilter)
            : null;

        $query = Article::query()->ordered();
        if ($category !== null) {
            $query->inCategory($category);
        }

        $items = $query->get()->map(fn (Article $article) => $this->transform($article));

        return Inertia::render('admin/articles/index', [
            'articles' => $items,
            'categories' => $this->categoryOptions(),
            'filters' => [
                'category' => $category?->value,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/articles/create', [
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $this->articles->create(
            $request->safe()->except('cover'),
            $request->file('cover'),
        );

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Материал создан.');
    }

    public function edit(Article $article): Response
    {
        return Inertia::render('admin/articles/edit', [
            'article' => $this->transform($article),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $this->articles->update(
            $article,
            $request->safe()->except('cover'),
            $request->file('cover'),
        );

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Материал обновлён.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->articles->delete($article);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Материал удалён.');
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Article $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'category' => $article->category->value,
            'category_label' => $article->category->label(),
            'excerpt' => $article->excerpt,
            'body' => $article->body,
            'meta_title' => $article->meta_title,
            'meta_description' => $article->meta_description,
            'published_at' => $article->published_at?->format('Y-m-d\TH:i'),
            'is_published' => $article->is_published,
            'cover_url' => $article->coverUrl(),
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function categoryOptions(): array
    {
        return array_map(
            fn (ArticleCategory $category) => [
                'value' => $category->value,
                'label' => $category->label(),
            ],
            ArticleCategory::cases(),
        );
    }
}
