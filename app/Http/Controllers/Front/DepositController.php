<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\BankService;
use App\Services\Deposits\DepositQueryService;
use App\Services\DepositService;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function __construct(
        private readonly DepositQueryService $query,
        private readonly DepositService $deposits,
        private readonly BankService $banks,
        private readonly SeoService $seo,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);
        $paginator = $this->query->paginate($filters);
        $seo = $this->seo->resolve('deposits.index', 'Сравнение вкладов в банках Узбекистана');

        return view('public.deposits.index', [
            'deposits' => $paginator,
            'title' => $seo['title'],
            'h1' => $seo['h1'],
            'metaDescription' => $seo['metaDescription'],
            'metaKeywords' => $seo['metaKeywords'],
            'banks' => $this->banks->listActiveOptions(),
            'currencies' => ['UZS', 'USD', 'EUR', 'RUB', 'KZT', 'GBP', 'CHF', 'JPY'],
            'termOptions' => $this->termOptions(),
            'filters' => $filters,
            'hasMore' => $paginator->hasMorePages(),
        ]);
    }

    public function show(Deposit $deposit): View
    {
        if (! $deposit->is_active) {
            abort(404);
        }

        $deposit->load(['bank', 'rates', 'conditions']);

        return view('public.deposits.show', [
            'deposit' => $deposit,
            'title' => $deposit->title,
            'otherDeposits' => $this->deposits->otherDepositsOfBank($deposit),
        ]);
    }

    /**
     * @return array{
     *     bank_id: int|null,
     *     currency: string|null,
     *     srok: string|null,
     *     summa: string|null,
     *     is_online: bool,
     * }
     */
    private function filtersFromRequest(Request $request): array
    {
        return [
            'bank_id' => $request->integer('bank_id') ?: null,
            'currency' => filled($request->input('currency')) ? (string) $request->input('currency') : null,
            'srok' => filled($request->input('srok')) ? (string) $request->input('srok') : 'all',
            'summa' => filled($request->input('summa')) ? (string) $request->input('summa') : null,
            'is_online' => $request->boolean('is_online'),
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function termOptions(): array
    {
        return [
            ['value' => 'all', 'label' => 'Любой'],
            ['value' => '1', 'label' => '1 месяц'],
            ['value' => '2', 'label' => '2 месяца'],
            ['value' => '3', 'label' => '3 месяца'],
            ['value' => '4', 'label' => '4 месяца'],
            ['value' => '5', 'label' => '5 месяцев'],
            ['value' => '6', 'label' => '6 месяцев'],
            ['value' => '7', 'label' => '7 месяцев'],
            ['value' => '8', 'label' => '8 месяцев'],
            ['value' => '9', 'label' => '9 месяцев'],
            ['value' => '12', 'label' => '1 год'],
            ['value' => '18', 'label' => '1,5 года'],
            ['value' => '24', 'label' => '2 года'],
            ['value' => '36', 'label' => '3 года'],
            ['value' => '48', 'label' => '4 года'],
            ['value' => '60', 'label' => '5 лет'],
            ['value' => '72', 'label' => '6 лет'],
            ['value' => '84', 'label' => '7 лет'],
        ];
    }
}
