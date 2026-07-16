<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\Gold\GoldQueryService;
use Illuminate\View\View;

class GoldController extends Controller
{
    public function __construct(
        private readonly GoldQueryService $gold,
    ) {}

    public function show(): View
    {
        $prices = $this->gold->currentPrices();
        $regions = $this->gold->regions();
        $defaultRegion = in_array('г. Ташкент', $regions, true)
            ? 'г. Ташкент'
            : ($regions[0] ?? null);

        return view('public.gold.show', [
            'title' => 'Стоимость золотых слитков',
            'prices' => $prices,
            'pricedOn' => $this->gold->latestPricedOn(),
            'regions' => $regions,
            'defaultRegion' => $defaultRegion,
            'salePoints' => $this->gold->salePoints(),
            'chartApiUrl' => route('api.gold-chart'),
        ]);
    }
}
