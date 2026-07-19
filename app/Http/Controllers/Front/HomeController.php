<?php

namespace App\Http\Controllers\Front;

use App\Enums\ArticleCategory;
use App\Enums\RatePlace;
use App\Http\Controllers\Controller;
use App\Services\ArticleService;
use App\Services\BankService;
use App\Services\CreditService;
use App\Services\DepositService;
use App\Services\Gold\GoldQueryService;
use App\Services\Rates\ExchangeRateQueryService;
use App\Services\SeoService;
use App\Services\SettingService;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const DEFAULT_TELEGRAM_URL = 'https://t.me/pul_top_uzb';

    public function __construct(
        private readonly ExchangeRateQueryService $rates,
        private readonly GoldQueryService $gold,
        private readonly ArticleService $articles,
        private readonly CreditService $credits,
        private readonly DepositService $deposits,
        private readonly BankService $banks,
        private readonly SettingService $settings,
        private readonly SeoService $seo,
    ) {}

    public function __invoke(): View
    {
        $byPlace = [
            RatePlace::Cash->value => $this->rates->homepageBestRates(RatePlace::Cash),
            RatePlace::Atm->value => $this->rates->homepageBestRates(RatePlace::Atm),
            RatePlace::App->value => $this->rates->homepageBestRates(RatePlace::App),
        ];

        $homepageArticles = $this->articles->homepageByCategory();
        $articleTabs = array_map(
            fn (ArticleCategory $category) => [
                'value' => $category->value,
                'label' => $category->label(),
                'show_date' => $category !== ArticleCategory::Article,
                'articles' => $homepageArticles[$category->value] ?? collect(),
            ],
            ArticleCategory::cases(),
        );

        $telegramUrl = trim((string) $this->settings->get('social_telegram_url', ''));
        if ($telegramUrl === '') {
            $telegramUrl = self::DEFAULT_TELEGRAM_URL;
        }

        $seo = $this->seo->resolve(
            'home',
            'Кредиты, вклады, курсы валют в Узбекистане | PulTop.Uz',
            appendAppName: false,
            fallbackH1: '',
        );

        return view('index', [
            'homepageRates' => $byPlace[RatePlace::Cash->value],
            'homepageRatesByPlace' => $byPlace,
            'goldPrices' => $this->gold->currentPrices(),
            'goldPricedOn' => $this->gold->latestPricedOn(),
            'articleTabs' => $articleTabs,
            'creditsCount' => $this->credits->activeCount(),
            'depositsCount' => $this->deposits->activeCount(),
            'organizationsCount' => $this->banks->activeCount(),
            'telegramUrl' => $telegramUrl,
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
        ]);
    }
}
