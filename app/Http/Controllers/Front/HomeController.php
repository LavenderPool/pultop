<?php

namespace App\Http\Controllers\Front;

use App\Enums\RatePlace;
use App\Http\Controllers\Controller;
use App\Services\Gold\GoldQueryService;
use App\Services\Rates\ExchangeRateQueryService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly ExchangeRateQueryService $rates,
        private readonly GoldQueryService $gold,
    ) {}

    public function __invoke(): View
    {
        $byPlace = [
            RatePlace::Cash->value => $this->rates->homepageBestRates(RatePlace::Cash),
            RatePlace::Atm->value => $this->rates->homepageBestRates(RatePlace::Atm),
            RatePlace::App->value => $this->rates->homepageBestRates(RatePlace::App),
        ];

        return view('index', [
            'homepageRates' => $byPlace[RatePlace::Cash->value],
            'homepageRatesByPlace' => $byPlace,
            'goldPrices' => $this->gold->currentPrices(),
            'goldPricedOn' => $this->gold->latestPricedOn(),
        ]);
    }
}
