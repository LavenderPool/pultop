<?php

namespace App\Http\Controllers\Front;

use App\Enums\CreditTypeSlug;
use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\CreditType;
use App\Services\BankService;
use App\Services\Credits\CreditQueryService;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreditController extends Controller
{
    public function __construct(
        private readonly CreditService $credits,
        private readonly CreditQueryService $query,
        private readonly BankService $banks,
    ) {}

    public function index(): View
    {
        return view('public.credits.hub', [
            'title' => 'Кредиты',
            'types' => $this->credits->typesWithActiveCounts(),
            'topCredits' => $this->credits->topAutoloans(10),
        ]);
    }

    public function byType(Request $request, string $type): View
    {
        $enum = CreditTypeSlug::tryFromAlias($type);
        if ($enum === null) {
            abort(404);
        }

        $creditType = CreditType::query()
            ->active()
            ->where('slug', $enum->value)
            ->firstOrFail();

        $filters = $this->filtersFromRequest($request);

        return view('public.credits.index', [
            'credits' => $this->query->list($creditType, $filters),
            'title' => $creditType->name,
            'type' => $creditType,
            'banks' => $this->banks->listActiveOptions(),
            'currencies' => ['UZS', 'USD', 'EUR', 'RUB', 'KZT', 'GBP', 'CHF', 'JPY'],
            'termOptions' => $this->termOptions(),
            'filters' => $filters,
        ]);
    }

    public function show(Credit $credit): View
    {
        if (! $credit->is_active) {
            abort(404);
        }

        $credit->load(['bank', 'rateRows', 'conditions', 'types']);

        return view('public.credits.show', [
            'credit' => $credit,
            'title' => $credit->title,
            'otherCredits' => $this->credits->otherCreditsOfBank($credit),
        ]);
    }

    /**
     * @return array{
     *     bank_id: int|null,
     *     currency: string|null,
     *     srok: string|null,
     *     summa: string|null,
     * }
     */
    private function filtersFromRequest(Request $request): array
    {
        return [
            'bank_id' => $request->integer('bank_id') ?: null,
            'currency' => filled($request->input('currency')) ? (string) $request->input('currency') : null,
            'srok' => filled($request->input('srok')) ? (string) $request->input('srok') : 'all',
            'summa' => filled($request->input('summa')) ? (string) $request->input('summa') : null,
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
