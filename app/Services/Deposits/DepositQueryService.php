<?php

namespace App\Services\Deposits;

use App\Models\Deposit;
use App\Services\PublicCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class DepositQueryService
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
     *     is_online?: bool|null,
     * }  $filters
     * @return LengthAwarePaginator<int, Deposit>
     */
    public function paginate(array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? max(1, (int) config('deposits.per_page', 10));
        $page = Paginator::resolveCurrentPage();

        /** @var LengthAwarePaginator<int, Deposit> $paginator */
        $paginator = $this->cache->remember(
            PublicCacheService::GROUP_DEPOSITS,
            $this->cache->key(['paginate', $filters, $perPage, $page]),
            fn () => $this->filteredQuery($filters)
                ->with(['bank'])
                ->paginate($perPage, ['*'], 'page', $page),
        );

        return $paginator->withQueryString();
    }

    /**
     * @param  array{
     *     bank_id?: int|null,
     *     currency?: string|null,
     *     srok?: string|int|null,
     *     summa?: int|string|null,
     *     is_online?: bool|null,
     * }  $filters
     * @return Builder<Deposit>
     */
    public function filteredQuery(array $filters = []): Builder
    {
        $query = Deposit::query()->active()->ordered();

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

        if (! empty($filters['is_online'])) {
            $query->where('is_online', true);
        }

        return $query;
    }

    private function normalizeTermMonths(string|int $srok): ?int
    {
        $raw = trim((string) $srok);
        if ($raw === '' || $raw === 'all') {
            return null;
        }

        // WP filter: 12 = 1 год, 18 = 1.5 года, etc. Values are already in months.
        if (ctype_digit($raw)) {
            return max(1, (int) $raw);
        }

        return null;
    }
}
