<?php

namespace App\Services;

use App\Enums\CardType;
use App\Models\Bank;
use App\Models\Card;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CardService
{
    public function __construct(
        private readonly PublicCacheService $cache,
        private readonly BankService $banks,
    ) {}

    public function activeCount(): int
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_CARDS,
            'active_count',
            fn () => Card::query()->active()->count(),
        );
    }

    /**
     * @param  array{
     *     bank_id?: int|null,
     *     currency?: string|null,
     *     payment_system?: string|null,
     *     card_type?: string|null
     * }  $filters
     * @return Collection<int, Card>
     */
    public function listActive(array $filters = []): Collection
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_CARDS,
            $this->cache->key(['list_active', $filters]),
            function () use ($filters) {
                $query = Card::query()
                    ->active()
                    ->ordered()
                    ->with(['bank']);

                if (filled($filters['bank_id'] ?? null)) {
                    $query->where('bank_id', (int) $filters['bank_id']);
                }

                if (filled($filters['currency'] ?? null)) {
                    $currency = strtoupper((string) $filters['currency']);
                    $query->where(function ($q) use ($currency): void {
                        $q->where('currency', $currency);
                        if ($currency === 'UZS') {
                            $q->orWhereIn('currency', ['sum', 'SUM', 'Sum']);
                        }
                    });
                }

                if (filled($filters['payment_system'] ?? null) && ($filters['payment_system'] ?? '') !== 'all') {
                    $query->where('payment_system', (string) $filters['payment_system']);
                }

                if (filled($filters['card_type'] ?? null) && ($filters['card_type'] ?? '') !== 'all') {
                    $type = CardType::tryFrom((string) $filters['card_type'])
                        ?? CardType::tryFromLabel((string) $filters['card_type']);
                    if ($type !== null) {
                        $query->where('card_type', $type->value);
                    }
                }

                return $query->get();
            },
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array{label?: string, value?: string|null, note?: string|null}>  $conditions
     */
    public function create(array $data, array $conditions = [], ?UploadedFile $image = null): Card
    {
        $card = DB::transaction(function () use ($data, $conditions, $image) {
            $data = $this->normalize($data);

            if ($image !== null) {
                $data['image_path'] = $image->store('cards', 'public');
            }

            $card = Card::query()->create($data);
            $this->syncConditions($card, $conditions);

            return $card->fresh(['bank', 'conditions']);
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_CARDS);

        return $card;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array{label?: string, value?: string|null, note?: string|null}>  $conditions
     */
    public function update(Card $card, array $data, array $conditions = [], ?UploadedFile $image = null): Card
    {
        $card = DB::transaction(function () use ($card, $data, $conditions, $image) {
            $data = $this->normalize($data, $card);

            if ($image !== null) {
                if ($card->image_path) {
                    Storage::disk('public')->delete($card->image_path);
                }
                $data['image_path'] = $image->store('cards', 'public');
            }

            $card->update($data);
            $this->syncConditions($card, $conditions);

            return $card->fresh(['bank', 'conditions']);
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_CARDS);

        return $card;
    }

    public function delete(Card $card): void
    {
        DB::transaction(function () use ($card): void {
            if ($card->image_path) {
                Storage::disk('public')->delete($card->image_path);
            }
            $card->conditions()->delete();
            $card->delete();
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_CARDS);
    }

    /**
     * @return array{
     *     banks: Collection<int, Bank>,
     *     payment_systems: list<string>,
     *     card_types: list<CardType>,
     *     currencies: array<string, string>
     * }
     */
    public function filterOptions(): array
    {
        $meta = $this->cache->remember(
            PublicCacheService::GROUP_CARDS,
            'filter_options_meta',
            fn () => [
                'payment_systems' => Card::query()
                    ->active()
                    ->whereNotNull('payment_system')
                    ->where('payment_system', '!=', '')
                    ->distinct()
                    ->orderBy('payment_system')
                    ->pluck('payment_system')
                    ->all(),
            ],
        );

        return [
            'banks' => $this->banks->listActiveOptions(),
            'payment_systems' => $meta['payment_systems'],
            'card_types' => CardType::cases(),
            'currencies' => [
                'sum' => 'UZS',
                'usd' => 'USD',
                'eur' => 'EUR',
                'rub' => 'RUB',
                'kzt' => 'KZT',
                'gbp' => 'GBP',
                'chf' => 'CHF',
                'jpy' => 'JPY',
            ],
        ];
    }

    /**
     * @return Collection<int, Card>
     */
    public function otherCardsOfBank(Card $card, int $limit = 10): Collection
    {
        if ($card->bank_id === null) {
            return new Collection;
        }

        return Card::query()
            ->active()
            ->ordered()
            ->where('bank_id', $card->bank_id)
            ->where('id', '!=', $card->id)
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data, ?Card $card = null): array
    {
        if (empty($data['slug']) && ! empty($data['title'])) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        if (empty($data['slug']) && $card !== null) {
            $data['slug'] = $card->slug;
        }

        $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['currency'] = filled($data['currency'] ?? null) ? (string) $data['currency'] : 'sum';
        $data['bank_id'] = filled($data['bank_id'] ?? null) ? (int) $data['bank_id'] : null;
        $data['payment_system'] = filled($data['payment_system'] ?? null) ? (string) $data['payment_system'] : null;
        $data['category'] = filled($data['category'] ?? null) ? (string) $data['category'] : null;
        $data['issue_cost_display'] = filled($data['issue_cost_display'] ?? null) ? (string) $data['issue_cost_display'] : null;
        $data['validity_display'] = filled($data['validity_display'] ?? null) ? (string) $data['validity_display'] : null;
        $data['special_conditions'] = filled($data['special_conditions'] ?? null) ? (string) $data['special_conditions'] : null;
        $data['apply_url'] = filled($data['apply_url'] ?? null) ? (string) $data['apply_url'] : null;

        $cardType = $data['card_type'] ?? null;
        if (filled($cardType)) {
            $enum = $cardType instanceof CardType
                ? $cardType
                : (CardType::tryFrom((string) $cardType) ?? CardType::tryFromLabel((string) $cardType));
            $data['card_type'] = $enum?->value;
        } else {
            $data['card_type'] = null;
        }

        return $data;
    }

    /**
     * @param  list<array{label?: string, value?: string|null, note?: string|null}>  $conditions
     */
    private function syncConditions(Card $card, array $conditions): void
    {
        $card->conditions()->delete();
        foreach (array_values($conditions) as $index => $condition) {
            $label = trim((string) ($condition['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $card->conditions()->create([
                'label' => $label,
                'value' => filled($condition['value'] ?? null) ? (string) $condition['value'] : null,
                'note' => filled($condition['note'] ?? null) ? (string) $condition['note'] : null,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
