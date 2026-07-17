<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Services\BankService;
use Illuminate\View\View;

class BankController extends Controller
{
    public function __construct(
        private readonly BankService $banks,
    ) {}

    public function index(): View
    {
        return view('public.banks.index', [
            'banks' => $this->banks->listActive(),
            'title' => 'Банки Узбекистана',
        ]);
    }

    public function show(Bank $bank): View
    {
        if (! $bank->is_active) {
            abort(404);
        }

        $products = $this->banks->productsForBank($bank);

        return view('public.banks.show', [
            'bank' => $bank,
            'title' => $bank->name,
            'credits' => $products['credits'],
            'cards' => $products['cards'],
            'deposits' => $products['deposits'],
        ]);
    }
}
