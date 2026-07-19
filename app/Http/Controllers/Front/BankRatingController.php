<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\BankService;
use App\Services\SeoService;
use Illuminate\View\View;

class BankRatingController extends Controller
{
    public function __construct(
        private readonly BankService $banks,
        private readonly SeoService $seo,
    ) {}

    public function show(): View
    {
        $seo = $this->seo->resolve('banks.rating', 'Рейтинг банков Узбекистана');

        return view('public.banks.rating', [
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
            'snapshot' => $this->banks->currentRatingSnapshot(),
            'banksByName' => $this->banks->activeBanksByNormalizedName(),
        ]);
    }
}
