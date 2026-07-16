<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\View\View;

class BankController extends Controller
{
    public function index(): View
    {
        $banks = Bank::query()
            ->active()
            ->ordered()
            ->get();

        return view('public.banks.index', [
            'banks' => $banks,
            'title' => 'Банки Узбекистана',
        ]);
    }

    public function show(Bank $bank): View
    {
        if (! $bank->is_active) {
            abort(404);
        }

        return view('public.banks.show', [
            'bank' => $bank,
            'title' => $bank->name,
        ]);
    }
}
