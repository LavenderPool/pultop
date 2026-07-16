<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BankRatingSnapshot;
use Illuminate\View\View;

class BankRatingController extends Controller
{
    public function show(): View
    {
        $snapshot = BankRatingSnapshot::query()
            ->current()
            ->with('rows')
            ->first();

        return view('public.banks.rating', [
            'title' => 'Рейтинг банков Узбекистана',
            'snapshot' => $snapshot,
        ]);
    }
}
