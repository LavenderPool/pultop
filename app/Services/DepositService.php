<?php

namespace App\Services;

use App\Models\Deposit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepositService
{
    public function __construct(
        private readonly PublicCacheService $cache,
    ) {}

    public function activeCount(): int
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_DEPOSITS,
            'active_count',
            fn () => Deposit::query()->active()->count(),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array{rate?: string, term?: string|null, note?: string|null}>  $rateRows
     * @param  list<array{label?: string, value?: string|null}>  $conditions
     */
    public function create(array $data, array $rateRows = [], array $conditions = []): Deposit
    {
        $deposit = DB::transaction(function () use ($data, $rateRows, $conditions) {
            $data = $this->normalize($data);
            $deposit = Deposit::query()->create($data);
            $this->syncRelations($deposit, $rateRows, $conditions);

            return $deposit->fresh(['bank', 'rates', 'conditions']);
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_DEPOSITS);

        return $deposit;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array{rate?: string, term?: string|null, note?: string|null}>  $rateRows
     * @param  list<array{label?: string, value?: string|null}>  $conditions
     */
    public function update(Deposit $deposit, array $data, array $rateRows = [], array $conditions = []): Deposit
    {
        $deposit = DB::transaction(function () use ($deposit, $data, $rateRows, $conditions) {
            $data = $this->normalize($data, $deposit);
            $deposit->update($data);
            $this->syncRelations($deposit, $rateRows, $conditions);

            return $deposit->fresh(['bank', 'rates', 'conditions']);
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_DEPOSITS);

        return $deposit;
    }

    public function delete(Deposit $deposit): void
    {
        DB::transaction(function () use ($deposit): void {
            $deposit->rates()->delete();
            $deposit->conditions()->delete();
            $deposit->delete();
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_DEPOSITS);
    }

    /**
     * Заполняет amount_min/amount_max из amount_display для уже импортированных записей.
     */
    public function backfillAmountBounds(): int
    {
        $updated = 0;

        Deposit::query()->orderBy('id')->chunkById(100, function ($deposits) use (&$updated): void {
            foreach ($deposits as $deposit) {
                [$amountMin, $amountMax] = $this->parseAmountBounds($deposit->amount_display);

                $deposit->forceFill([
                    'amount_min' => $amountMin,
                    'amount_max' => $amountMax,
                ])->save();

                $updated++;
            }
        });

        $this->cache->forgetGroup(PublicCacheService::GROUP_DEPOSITS);

        return $updated;
    }

    /**
     * @return Collection<int, Deposit>
     */
    public function otherDepositsOfBank(Deposit $deposit, int $limit = 10): Collection
    {
        if ($deposit->bank_id === null) {
            return new Collection;
        }

        return Deposit::query()
            ->active()
            ->ordered()
            ->where('bank_id', $deposit->bank_id)
            ->where('id', '!=', $deposit->id)
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data, ?Deposit $deposit = null): array
    {
        if (empty($data['slug']) && ! empty($data['title'])) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        if (empty($data['slug']) && $deposit !== null) {
            $data['slug'] = $deposit->slug;
        }

        $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $data['early_termination'] = filter_var($data['early_termination'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['partial_withdrawal'] = filter_var($data['partial_withdrawal'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['capitalization'] = filter_var($data['capitalization'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['is_online'] = filter_var($data['is_online'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['currency'] = filled($data['currency'] ?? null) ? strtoupper((string) $data['currency']) : 'UZS';
        $data['bank_id'] = filled($data['bank_id'] ?? null) ? (int) $data['bank_id'] : null;
        $data['rate_display'] = filled($data['rate_display'] ?? null) ? (string) $data['rate_display'] : null;
        $data['term_display'] = filled($data['term_display'] ?? null) ? (string) $data['term_display'] : null;
        $data['amount_display'] = filled($data['amount_display'] ?? null) ? (string) $data['amount_display'] : null;
        $data['special_conditions'] = filled($data['special_conditions'] ?? null) ? (string) $data['special_conditions'] : null;
        $data['apply_url'] = filled($data['apply_url'] ?? null) ? (string) $data['apply_url'] : null;
        $data['term_min_months'] = filled($data['term_min_months'] ?? null) ? (int) $data['term_min_months'] : null;
        $data['term_max_months'] = filled($data['term_max_months'] ?? null) ? (int) $data['term_max_months'] : null;
        $data['amount_min'] = filled($data['amount_min'] ?? null) ? (int) $data['amount_min'] : null;
        $data['amount_max'] = filled($data['amount_max'] ?? null) ? (int) $data['amount_max'] : null;

        return $data;
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

        if (str_contains($text, 'от') && str_contains($text, 'до')) {
            if (preg_match_all('/([\d\s]+)/u', $display, $m) && count($m[1]) >= 2) {
                $first = (int) (preg_replace('/\D+/', '', $m[1][0]) ?? '');
                $second = (int) (preg_replace('/\D+/', '', $m[1][1]) ?? '');

                return [
                    $first > 0 ? $first : null,
                    $second > 0 ? $second : null,
                ];
            }
        }

        $digits = preg_replace('/\D+/', '', $display) ?? '';
        if ($digits === '') {
            return [null, null];
        }

        $amount = (int) $digits;
        if ($amount <= 0) {
            return [null, null];
        }

        if (str_contains($text, 'до') && ! str_contains($text, 'от')) {
            return [null, $amount];
        }

        return [$amount, null];
    }

    /**
     * @param  list<array{rate?: string, term?: string|null, note?: string|null}>  $rateRows
     * @param  list<array{label?: string, value?: string|null}>  $conditions
     */
    private function syncRelations(Deposit $deposit, array $rateRows, array $conditions): void
    {
        $deposit->rates()->delete();
        foreach (array_values($rateRows) as $index => $row) {
            $rate = trim((string) ($row['rate'] ?? ''));
            if ($rate === '') {
                continue;
            }

            $deposit->rates()->create([
                'rate' => $rate,
                'term' => filled($row['term'] ?? null) ? (string) $row['term'] : null,
                'note' => filled($row['note'] ?? null) ? (string) $row['note'] : null,
                'sort_order' => $index + 1,
            ]);
        }

        $deposit->conditions()->delete();
        foreach (array_values($conditions) as $index => $condition) {
            $label = trim((string) ($condition['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $deposit->conditions()->create([
                'label' => $label,
                'value' => filled($condition['value'] ?? null) ? (string) $condition['value'] : null,
                'note' => filled($condition['note'] ?? null) ? (string) $condition['note'] : null,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
