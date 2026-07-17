<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Deposit\StoreDepositRequest;
use App\Http\Requests\Admin\Deposit\UpdateDepositRequest;
use App\Models\Bank;
use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DepositController extends Controller
{
    public function __construct(
        private readonly DepositService $deposits,
    ) {}

    public function index(Request $request): Response
    {
        $bankFilter = $request->integer('bank_id') ?: null;
        $activeFilter = $request->has('is_active')
            ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : null;

        $query = Deposit::query()
            ->with(['bank'])
            ->ordered();

        if ($bankFilter !== null) {
            $query->where('bank_id', $bankFilter);
        }

        if ($activeFilter !== null) {
            $query->where('is_active', $activeFilter);
        }

        $items = $query->get()->map(fn (Deposit $deposit) => $this->transform($deposit));

        return Inertia::render('admin/deposits/index', [
            'deposits' => $items,
            'banks' => $this->bankOptions(),
            'filters' => [
                'bank_id' => $bankFilter,
                'is_active' => $activeFilter,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/deposits/create', [
            'banks' => $this->bankOptions(),
            'currencies' => $this->currencyOptions(),
        ]);
    }

    public function store(StoreDepositRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->deposits->create(
            collect($validated)->except(['rate_rows', 'conditions'])->all(),
            $validated['rate_rows'] ?? [],
            $validated['conditions'] ?? [],
        );

        return redirect()
            ->route('admin.deposits.index')
            ->with('success', 'Вклад создан.');
    }

    public function edit(Deposit $deposit): Response
    {
        $deposit->load(['bank', 'rates', 'conditions']);

        return Inertia::render('admin/deposits/edit', [
            'deposit' => $this->transform($deposit, detailed: true),
            'banks' => $this->bankOptions(),
            'currencies' => $this->currencyOptions(),
        ]);
    }

    public function update(UpdateDepositRequest $request, Deposit $deposit): RedirectResponse
    {
        $validated = $request->validated();

        $this->deposits->update(
            $deposit,
            collect($validated)->except(['rate_rows', 'conditions'])->all(),
            $validated['rate_rows'] ?? [],
            $validated['conditions'] ?? [],
        );

        return redirect()
            ->route('admin.deposits.index')
            ->with('success', 'Вклад обновлён.');
    }

    public function destroy(Deposit $deposit): RedirectResponse
    {
        $this->deposits->delete($deposit);

        return redirect()
            ->route('admin.deposits.index')
            ->with('success', 'Вклад удалён.');
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Deposit $deposit, bool $detailed = false): array
    {
        $data = [
            'id' => $deposit->id,
            'title' => $deposit->title,
            'slug' => $deposit->slug,
            'bank_id' => $deposit->bank_id,
            'bank_name' => $deposit->bank?->name,
            'currency' => $deposit->currency,
            'rate_display' => $deposit->rate_display,
            'term_display' => $deposit->term_display,
            'amount_display' => $deposit->amount_display,
            'term_min_months' => $deposit->term_min_months,
            'term_max_months' => $deposit->term_max_months,
            'amount_min' => $deposit->amount_min,
            'amount_max' => $deposit->amount_max,
            'early_termination' => $deposit->early_termination,
            'partial_withdrawal' => $deposit->partial_withdrawal,
            'capitalization' => $deposit->capitalization,
            'is_online' => $deposit->is_online,
            'apply_url' => $deposit->apply_url,
            'is_active' => $deposit->is_active,
            'sort_order' => $deposit->sort_order,
        ];

        if ($detailed) {
            $data['special_conditions'] = $deposit->special_conditions;
            $data['rate_rows'] = $deposit->rates->map(fn ($row) => [
                'rate' => $row->rate,
                'term' => $row->term,
                'note' => $row->note,
            ])->all();
            $data['conditions'] = $deposit->conditions->map(fn ($condition) => [
                'label' => $condition->label,
                'value' => $condition->value,
            ])->all();
        }

        return $data;
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    private function bankOptions(): array
    {
        return Bank::query()
            ->ordered()
            ->get(['id', 'name'])
            ->map(fn (Bank $bank) => [
                'id' => $bank->id,
                'name' => $bank->name,
            ])
            ->all();
    }

    /**
     * @return list<string>
     */
    private function currencyOptions(): array
    {
        return ['UZS', 'USD', 'EUR', 'RUB', 'KZT', 'GBP', 'CHF', 'JPY'];
    }
}
