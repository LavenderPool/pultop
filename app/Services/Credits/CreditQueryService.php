<?php

namespace App\Services\Credits;

use App\Models\Credit;
use App\Models\CreditType;
use App\Services\PublicCacheService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CreditQueryService
{
    public function __construct(
        private readonly PublicCacheService $cache,
    ) {}

    /**
     * @param  array{
     *     bank_id?: int|null,
     *     currency?: string|null,
     *     srok?: string|int|null,
     *     summa?: int|string|null,
     * }  $filters
     * @return Collection<int, Credit>
     */
    public function list(CreditType $type, array $filters = []): Collection
    {
        return $this->cache->remember(
            PublicCacheService::GROUP_CREDITS,
            $this->cache->key(['list', $type->id, $filters]),
            fn () => $this->filteredQuery($type, $filters)
                ->with(['bank', 'types'])
                ->get(),
        );
    }

    /**
     * @param  array{
     *     bank_id?: int|null,
     *     currency?: string|null,
     *     srok?: string|int|null,
     *     summa?: int|string|null,
     * }  $filters
     * @return Builder<Credit>
     */
    public function filteredQuery(CreditType $type, array $filters = []): Builder
    {
        $query = Credit::query()
            ->active()
            ->ordered()
            ->whereHas('types', fn ($q) => $q->where('credit_types.id', $type->id));

        $bankId = filled($filters['bank_id'] ?? null) ? (int) $filters['bank_id'] : null;
        if ($bankId !== null) {
            $query->where('bank_id', $bankId);
        }

        $currency = filled($filters['currency'] ?? null) ? strtoupper((string) $filters['currency']) : null;
        if ($currency !== null && $currency !== 'ALL') {
            $query->where(function (Builder $q) use ($currency): void {
                $q->where('currency', $currency);
                if ($currency === 'UZS') {
                    $q->orWhere('currency', 'sum');
                }
            });
        }

        $srok = $filters['srok'] ?? null;
        if (filled($srok) && (string) $srok !== 'all') {
            $months = $this->normalizeTermMonths($srok);
            if ($months !== null) {
                $query->where(function (Builder $q) use ($months): void {
                    $q->where(function (Builder $inner) use ($months): void {
                        $inner->whereNotNull('term_min_months')
                            ->whereNotNull('term_max_months')
                            ->where('term_min_months', '<=', $months)
                            ->where('term_max_months', '>=', $months);
                    })->orWhere(function (Builder $inner) use ($months): void {
                        $inner->whereNotNull('term_min_months')
                            ->whereNull('term_max_months')
                            ->where('term_min_months', '<=', $months);
                    })->orWhere(function (Builder $inner) use ($months): void {
                        $inner->whereNull('term_min_months')
                            ->whereNotNull('term_max_months')
                            ->where('term_max_months', '>=', $months);
                    });
                });
            }
        }

        $summa = $filters['summa'] ?? null;
        if (filled($summa)) {
            $amount = (int) preg_replace('/\D+/', '', (string) $summa);
            if ($amount > 0) {
                $query->where(function (Builder $q) use ($amount): void {
                    $q->where(function (Builder $inner) use ($amount): void {
                        $inner->whereNull('amount_max')
                            ->where(function (Builder $min) use ($amount): void {
                                $min->whereNull('amount_min')->orWhere('amount_min', '<=', $amount);
                            });
                    })->orWhere(function (Builder $inner) use ($amount): void {
                        $inner->whereNotNull('amount_max')
                            ->where('amount_max', '>=', $amount)
                            ->where(function (Builder $min) use ($amount): void {
                                $min->whereNull('amount_min')->orWhere('amount_min', '<=', $amount);
                            });
                    });
                });
            }
        }

        return $query;
    }

    private function normalizeTermMonths(string|int $srok): ?int
    {
        $raw = trim((string) $srok);
        if ($raw === '' || $raw === 'all') {
            return null;
        }

        if (ctype_digit($raw)) {
            return max(1, (int) $raw);
        }

        return null;
    }
}
