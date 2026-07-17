<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\BankService;
use Illuminate\View\View;

class BankRatingController extends Controller
{
    public function __construct(
        private readonly BankService $banks,
    ) {}

    public function show(): View
    {
        return view('public.banks.rating', [
            'title' => 'Рейтинг банков Узбекистана',
            'snapshot' => $this->banks->currentRatingSnapshot(),
            'banksByName' => $this->banks->activeBanksByNormalizedName(),
        ]);
    }
}
