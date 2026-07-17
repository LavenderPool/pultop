<?php

namespace App\Services;

use App\Enums\CreditTypeSlug;
use App\Models\Credit;
use App\Models\CreditType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreditService
{
    public function __construct(
        private readonly PublicCacheService $cache,
    ) {}

    /**
     * @return Collection<int, Credit>
     */
    public function listActive(?CreditType $type = null): Collection
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_CREDITS,
            $this->cache->key(['list_active', $type?->id]),
            function () use ($type) {
                $query = Credit::query()
                    ->active()
                    ->ordered()
                    ->with(['bank', 'types']);

                if ($type !== null) {
                    $query->whereHas('types', fn ($q) => $q->where('credit_types.id', $type->id));
                }

                return $query->get();
            },
        );
    }

    public function activeCount(): int
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_CREDITS,
            'active_count',
            fn () => Credit::query()->active()->count(),
        );
    }

    /**
     * @return Collection<int, CreditType>
     */
    public function typesWithActiveCounts(): Collection
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_CREDITS,
            'types_with_counts',
            fn () => CreditType::query()
                ->active()
                ->ordered()
                ->withCount(['credits as active_credits_count' => fn ($q) => $q->where('is_active', true)])
                ->get(),
        );
    }

    /**
     * @return Collection<int, Credit>
     */
    public function topAutoloans(int $limit = 10): Collection
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_CREDITS,
            $this->cache->key(['top_autoloans', $limit]),
            fn () => Credit::query()
                ->active()
                ->ordered()
                ->with(['bank'])
                ->whereHas('types', fn ($q) => $q->where('credit_types.slug', CreditTypeSlug::Autoloan->value))
                ->limit($limit)
                ->get(),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int|string>  $typeIds
     * @param  list<array{rate?: string, term?: string|null, note?: string|null}>  $rateRows
     * @param  list<array{label?: string, value?: string|null}>  $conditions
     */
    public function create(array $data, array $typeIds = [], array $rateRows = [], array $conditions = []): Credit
    {
        $credit = DB::transaction(function () use ($data, $typeIds, $rateRows, $conditions) {
            $data = $this->normalize($data);
            $credit = Credit::query()->create($data);
            $this->syncRelations($credit, $typeIds, $rateRows, $conditions);

            return $credit->fresh(['bank', 'types', 'rateRows', 'conditions']);
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_CREDITS);

        return $credit;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int|string>  $typeIds
     * @param  list<array{rate?: string, term?: string|null, note?: string|null}>  $rateRows
     * @param  list<array{label?: string, value?: string|null}>  $conditions
     */
    public function update(Credit $credit, array $data, array $typeIds = [], array $rateRows = [], array $conditions = []): Credit
    {
        $credit = DB::transaction(function () use ($credit, $data, $typeIds, $rateRows, $conditions) {
            $data = $this->normalize($data, $credit);
            $credit->update($data);
            $this->syncRelations($credit, $typeIds, $rateRows, $conditions);

            return $credit->fresh(['bank', 'types', 'rateRows', 'conditions']);
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_CREDITS);

        return $credit;
    }

    public function delete(Credit $credit): void
    {
        DB::transaction(function () use ($credit): void {
            $credit->types()->detach();
            $credit->rateRows()->delete();
            $credit->conditions()->delete();
            $credit->delete();
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_CREDITS);
    }

    /**
     * Заполняет term/amount/currency из *_display для уже импортированных записей.
     */
    public function backfillFilterFields(): int
    {
        $updated = 0;

        Credit::query()->orderBy('id')->chunkById(100, function ($credits) use (&$updated): void {
            foreach ($credits as $credit) {
                $currency = strtoupper((string) $credit->currency);
                if ($currency === '' || $currency === 'SUM') {
                    $currency = $this->currencyFromAmountDisplay($credit->amount_display) ?? 'UZS';
                }

                [$termMin, $termMax] = $this->parseTermMonths($credit->term_display);
                [$amountMin, $amountMax] = $this->parseAmountBounds($credit->amount_display);

                $credit->forceFill([
                    'currency' => $currency,
                    'term_min_months' => $termMin,
                    'term_max_months' => $termMax,
                    'amount_min' => $amountMin,
                    'amount_max' => $amountMax,
                ])->save();

                $updated++;
            }
        });

        return $updated;
    }

    /**
     * @return Collection<int, Credit>
     */
    public function otherCreditsOfBank(Credit $credit, int $limit = 10): Collection
    {
        if ($credit->bank_id === null) {
            return new Collection;
        }

        return Credit::query()
            ->active()
            ->ordered()
            ->where('bank_id', $credit->bank_id)
            ->where('id', '!=', $credit->id)
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data, ?Credit $credit = null): array
    {
        if (empty($data['slug']) && ! empty($data['title'])) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        if (empty($data['slug']) && $credit !== null) {
            $data['slug'] = $credit->slug;
        }

        $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $currency = filled($data['currency'] ?? null) ? strtoupper((string) $data['currency']) : 'UZS';
        $data['currency'] = $currency === 'SUM' ? 'UZS' : $currency;
        $data['bank_id'] = filled($data['bank_id'] ?? null) ? (int) $data['bank_id'] : null;
        $data['rate_display'] = filled($data['rate_display'] ?? null) ? (string) $data['rate_display'] : null;
        $data['term_display'] = filled($data['term_display'] ?? null) ? (string) $data['term_display'] : null;
        $data['amount_display'] = filled($data['amount_display'] ?? null) ? (string) $data['amount_display'] : null;
        foreach (['term_min_months', 'term_max_months', 'amount_min', 'amount_max'] as $numericField) {
            if (! array_key_exists($numericField, $data)) {
                continue;
            }
            $data[$numericField] = filled($data[$numericField]) ? (int) $data[$numericField] : null;
        }
        $data['down_payment'] = filled($data['down_payment'] ?? null) ? (string) $data['down_payment'] : null;
        $data['special_conditions'] = filled($data['special_conditions'] ?? null) ? (string) $data['special_conditions'] : null;
        $data['apply_url'] = filled($data['apply_url'] ?? null) ? (string) $data['apply_url'] : null;

        return $data;
    }

    private function currencyFromAmountDisplay(?string $display): ?string
    {
        if ($display === null || $display === '') {
            return null;
        }

        if (preg_match('/\b(UZS|USD|EUR|RUB|KZT|GBP|CHF|JPY)\b/i', $display, $m)) {
            return strtoupper($m[1]);
        }

        $value = mb_strtolower($display);

        return match (true) {
            str_contains($value, 'сум') => 'UZS',
            str_contains($value, 'доллар') => 'USD',
            str_contains($value, 'евро') => 'EUR',
            str_contains($value, 'руб') => 'RUB',
            default => null,
        };
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function parseTermMonths(?string $display): array
    {
        if ($display === null || $display === '') {
            return [null, null];
        }

        $text = mb_strtolower($display);
        $multiplier = 1;
        if (str_contains($text, 'год') || str_contains($text, 'лет')) {
            $multiplier = 12;
        }

        if (preg_match_all('/(\d+(?:[.,]\d+)?)/u', $display, $m) && $m[1] !== []) {
            $values = array_map(function (string $raw) use ($multiplier): int {
                $num = (float) str_replace(',', '.', $raw);

                return (int) round($num * $multiplier);
            }, $m[1]);

            $min = min($values);
            $max = max($values);

            return [$min > 0 ? $min : null, $max > 0 ? $max : null];
        }

        return [null, null];
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function parseAmountBounds(?string $display): array
    {
        if ($display === null || $display === '') {
            return [null, null];
        }

        $text = mb_strtolower($display);
        if (str_contains($text, 'без огранич') || str_contains($text, 'не огранич')) {
            return [null, null];
        }

        $digits = preg_replace('/\D+/', '', $display) ?? '';
        if ($digits === '') {
            return [null, null];
        }

        $amount = (int) $digits;
        if ($amount <= 0) {
            return [null, null];
        }

        if (str_contains($text, 'от') && ! str_contains($text, 'до')) {
            return [$amount, null];
        }

        return [null, $amount];
    }

    /**
     * @param  list<int|string>  $typeIds
     * @param  list<array{rate?: string, term?: string|null, note?: string|null}>  $rateRows
     * @param  list<array{label?: string, value?: string|null}>  $conditions
     */
    private function syncRelations(Credit $credit, array $typeIds, array $rateRows, array $conditions): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $typeIds))));
        $credit->types()->sync($ids);

        $credit->rateRows()->delete();
        foreach (array_values($rateRows) as $index => $row) {
            $rate = trim((string) ($row['rate'] ?? ''));
            if ($rate === '') {
                continue;
            }

            $credit->rateRows()->create([
                'rate' => $rate,
                'term' => filled($row['term'] ?? null) ? (string) $row['term'] : null,
                'note' => filled($row['note'] ?? null) ? (string) $row['note'] : null,
                'sort_order' => $index + 1,
            ]);
        }

        $credit->conditions()->delete();
        foreach (array_values($conditions) as $index => $condition) {
            $label = trim((string) ($condition['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $credit->conditions()->create([
                'label' => $label,
                'value' => filled($condition['value'] ?? null) ? (string) $condition['value'] : null,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
