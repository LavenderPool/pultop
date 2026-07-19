<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\Gold\GoldQueryService;
use App\Services\SeoService;
use Illuminate\View\View;

class GoldController extends Controller
{
    public function __construct(
        private readonly GoldQueryService $gold,
        private readonly SeoService $seo,
    ) {}

    public function show(): View
    {
        $prices = $this->gold->currentPrices();
        $regions = $this->gold->regions();
        $defaultRegion = in_array('г. Ташкент', $regions, true)
            ? 'г. Ташкент'
            : ($regions[0] ?? null);

        $seo = $this->seo->resolve('gold.show', 'Стоимость золотых слитков');

        return view('public.gold.show', [
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
            'prices' => $prices,
            'pricedOn' => $this->gold->latestPricedOn(),
            'regions' => $regions,
            'defaultRegion' => $defaultRegion,
            'salePoints' => $this->gold->salePoints(),
            'chartApiUrl' => route('api.gold-chart'),
        ]);
    }
}
