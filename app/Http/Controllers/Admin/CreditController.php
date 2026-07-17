<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Credit\StoreCreditRequest;
use App\Http\Requests\Admin\Credit\UpdateCreditRequest;
use App\Models\Bank;
use App\Models\Credit;
use App\Models\CreditType;
use App\Services\CreditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreditController extends Controller
{
    public function __construct(
        private readonly CreditService $credits,
    ) {}

    public function index(Request $request): Response
    {
        $typeFilter = $request->integer('type_id') ?: null;

        $query = Credit::query()
            ->with(['bank', 'types'])
            ->ordered();

        if ($typeFilter !== null) {
            $query->whereHas('types', fn ($q) => $q->where('credit_types.id', $typeFilter));
        }

        $items = $query->get()->map(fn (Credit $credit) => $this->transform($credit));

        return Inertia::render('admin/credits/index', [
            'credits' => $items,
            'types' => $this->typeOptions(),
            'filters' => [
                'type_id' => $typeFilter,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/credits/create', [
            'banks' => $this->bankOptions(),
            'types' => $this->typeOptions(),
        ]);
    }

    public function store(StoreCreditRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->credits->create(
            collect($validated)->except(['type_ids', 'rate_rows', 'conditions'])->all(),
            $validated['type_ids'] ?? [],
            $validated['rate_rows'] ?? [],
            $validated['conditions'] ?? [],
        );

        return redirect()
            ->route('admin.credits.index')
            ->with('success', 'Кредит создан.');
    }

    public function edit(Credit $credit): Response
    {
        $credit->load(['bank', 'types', 'rateRows', 'conditions']);

        return Inertia::render('admin/credits/edit', [
            'credit' => $this->transform($credit, detailed: true),
            'banks' => $this->bankOptions(),
            'types' => $this->typeOptions(),
        ]);
    }

    public function update(UpdateCreditRequest $request, Credit $credit): RedirectResponse
    {
        $validated = $request->validated();

        $this->credits->update(
            $credit,
            collect($validated)->except(['type_ids', 'rate_rows', 'conditions'])->all(),
            $validated['type_ids'] ?? [],
            $validated['rate_rows'] ?? [],
            $validated['conditions'] ?? [],
        );

        return redirect()
            ->route('admin.credits.index')
            ->with('success', 'Кредит обновлён.');
    }

    public function destroy(Credit $credit): RedirectResponse
    {
        $this->credits->delete($credit);

        return redirect()
            ->route('admin.credits.index')
            ->with('success', 'Кредит удалён.');
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Credit $credit, bool $detailed = false): array
    {
        $data = [
            'id' => $credit->id,
            'title' => $credit->title,
            'slug' => $credit->slug,
            'bank_id' => $credit->bank_id,
            'bank_name' => $credit->bank?->name,
            'currency' => $credit->currency,
            'rate_display' => $credit->rate_display,
            'term_display' => $credit->term_display,
            'amount_display' => $credit->amount_display,
            'down_payment' => $credit->down_payment,
            'apply_url' => $credit->apply_url,
            'is_active' => $credit->is_active,
            'sort_order' => $credit->sort_order,
            'type_ids' => $credit->relationLoaded('types')
                ? $credit->types->pluck('id')->all()
                : [],
            'type_names' => $credit->relationLoaded('types')
                ? $credit->types->pluck('name')->all()
                : [],
        ];

        if ($detailed) {
            $data['special_conditions'] = $credit->special_conditions;
            $data['rate_rows'] = $credit->rateRows->map(fn ($row) => [
                'rate' => $row->rate,
                'term' => $row->term,
                'note' => $row->note,
            ])->all();
            $data['conditions'] = $credit->conditions->map(fn ($condition) => [
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
     * @return list<array{id: int, slug: string, name: string}>
     */
    private function typeOptions(): array
    {
        return CreditType::query()
            ->ordered()
            ->get(['id', 'slug', 'name'])
            ->map(fn (CreditType $type) => [
                'id' => $type->id,
                'slug' => $type->slug,
                'name' => $type->name,
            ])
            ->all();
    }
}
