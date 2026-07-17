<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CardType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Card\StoreCardRequest;
use App\Http\Requests\Admin\Card\UpdateCardRequest;
use App\Models\Bank;
use App\Models\Card;
use App\Services\CardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CardController extends Controller
{
    public function __construct(
        private readonly CardService $cards,
    ) {}

    public function index(Request $request): Response
    {
        $bankFilter = $request->integer('bank_id') ?: null;
        $typeFilter = $request->string('card_type')->toString() ?: null;
        $paymentFilter = $request->string('payment_system')->toString() ?: null;

        $query = Card::query()
            ->with(['bank'])
            ->ordered();

        if ($bankFilter !== null) {
            $query->where('bank_id', $bankFilter);
        }

        if ($typeFilter !== null && $typeFilter !== '') {
            $query->where('card_type', $typeFilter);
        }

        if ($paymentFilter !== null && $paymentFilter !== '') {
            $query->where('payment_system', $paymentFilter);
        }

        $items = $query->get()->map(fn (Card $card) => $this->transform($card));

        return Inertia::render('admin/cards/index', [
            'cards' => $items,
            'banks' => $this->bankOptions(),
            'cardTypes' => $this->cardTypeOptions(),
            'paymentSystems' => $this->paymentSystemOptions(),
            'filters' => [
                'bank_id' => $bankFilter,
                'card_type' => $typeFilter,
                'payment_system' => $paymentFilter,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/cards/create', [
            'banks' => $this->bankOptions(),
            'cardTypes' => $this->cardTypeOptions(),
        ]);
    }

    public function store(StoreCardRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->cards->create(
            collect($validated)->except(['conditions', 'image'])->all(),
            $validated['conditions'] ?? [],
            $request->file('image'),
        );

        return redirect()
            ->route('admin.cards.index')
            ->with('success', 'Карта создана.');
    }

    public function edit(Card $card): Response
    {
        $card->load(['bank', 'conditions']);

        return Inertia::render('admin/cards/edit', [
            'card' => $this->transform($card, detailed: true),
            'banks' => $this->bankOptions(),
            'cardTypes' => $this->cardTypeOptions(),
        ]);
    }

    public function update(UpdateCardRequest $request, Card $card): RedirectResponse
    {
        $validated = $request->validated();

        $this->cards->update(
            $card,
            collect($validated)->except(['conditions', 'image'])->all(),
            $validated['conditions'] ?? [],
            $request->file('image'),
        );

        return redirect()
            ->route('admin.cards.index')
            ->with('success', 'Карта обновлена.');
    }

    public function destroy(Card $card): RedirectResponse
    {
        $this->cards->delete($card);

        return redirect()
            ->route('admin.cards.index')
            ->with('success', 'Карта удалена.');
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Card $card, bool $detailed = false): array
    {
        $data = [
            'id' => $card->id,
            'title' => $card->title,
            'slug' => $card->slug,
            'bank_id' => $card->bank_id,
            'bank_name' => $card->bank?->name,
            'currency' => $card->currency,
            'payment_system' => $card->payment_system,
            'card_type' => $card->card_type?->value,
            'card_type_label' => $card->card_type?->label(),
            'category' => $card->category,
            'issue_cost_display' => $card->issue_cost_display,
            'validity_display' => $card->validity_display,
            'apply_url' => $card->apply_url,
            'image_url' => $card->imageUrl(),
            'is_active' => $card->is_active,
            'sort_order' => $card->sort_order,
        ];

        if ($detailed) {
            $data['special_conditions'] = $card->special_conditions;
            $data['conditions'] = $card->conditions->map(fn ($condition) => [
                'label' => $condition->label,
                'value' => $condition->value,
                'note' => $condition->note,
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
     * @return list<array{value: string, label: string}>
     */
    private function cardTypeOptions(): array
    {
        return array_map(
            fn (CardType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ],
            CardType::cases(),
        );
    }

    /**
     * @return list<string>
     */
    private function paymentSystemOptions(): array
    {
        return Card::query()
            ->whereNotNull('payment_system')
            ->where('payment_system', '!=', '')
            ->distinct()
            ->orderBy('payment_system')
            ->pluck('payment_system')
            ->all();
    }
}
