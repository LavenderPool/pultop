<?php

namespace App\View\Components\Public;

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
        GoldQueryService $gold,
        public bool $showGold = true,
    ) {
        $this->news = $this->mockNews();

        if ($this->showGold) {
            $this->goldPrices = $gold->currentPrices();
            $this->goldPricedOn = $gold->latestPricedOn();
        }
    }

    public function render(): View
    {
        return view('components.public.sidebar');
    }

    /**
     * @return list<array{title: string, url: string, image: string, alt: string}>
     */
    private function mockNews(): array
    {
        return [
            [
                'title' => 'Суму удалось сохранить равновесие: как прошла очередная неделя июня',
                'url' => '#',
                'image' => 'https://pultop.uz/wp-content/uploads/2026/06/kartinka-3-3--150x84.png',
                'alt' => 'к 18 июня 2026 года доллар США в паре с узбекским сумом остался примерно на тех же отметках, как и неделей ранее',
            ],
            [
                'title' => 'Сум попал под внешнее влияние: почему падает курс',
                'url' => '#',
                'image' => 'https://pultop.uz/wp-content/uploads/2026/06/kartinka-4-150x84.png',
                'alt' => 'Сум попал под внешнее влияние',
            ],
            [
                'title' => 'Курс сума остается крепким: что произошло за последние две недели',
                'url' => '#',
                'image' => 'https://pultop.uz/wp-content/uploads/2026/06/alpari-150x84.jpg',
                'alt' => '',
            ],
            [
                'title' => 'Сум стабилен: у него есть опора на золото и замедление инфляции',
                'url' => '#',
                'image' => 'https://pultop.uz/wp-content/uploads/2026/05/kartinka-2-150x84.png',
                'alt' => '',
            ],
            [
                'title' => 'Сум поддержали рост экспортной выручки и денежных переводов',
                'url' => '#',
                'image' => 'https://pultop.uz/wp-content/uploads/2026/05/kartinka-3-2--150x84.png',
                'alt' => '',
            ],
        ];
    }
}
