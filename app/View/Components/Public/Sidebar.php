<?php

namespace App\View\Components\Public;

use App\Services\ArticleService;
use App\Services\Gold\GoldQueryService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    /**
     * @var list<array{title: string, url: string, image: string, alt: string}>
     */
    public array $news;

    /**
     * @var list<array<string, mixed>>
     */
    public array $goldPrices = [];

    public ?string $goldPricedOn = null;

    public function __construct(
        ArticleService $articles,
        GoldQueryService $gold,
        public bool $showGold = true,
    ) {
        $this->news = $articles->sidebarNews()
            ->map(fn ($article) => [
                'title' => $article->title,
                'url' => route('articles.show', $article),
                'image' => $article->coverUrl() ?? asset('favicon/cropped-logo-180x180.png'),
                'alt' => $article->title,
            ])
            ->all();

        if ($this->showGold) {
            $this->goldPrices = $gold->currentPrices();
            $this->goldPricedOn = $gold->latestPricedOn();
        }
    }

    public function render(): View
    {
        return view('components.public.sidebar');
    }
}
