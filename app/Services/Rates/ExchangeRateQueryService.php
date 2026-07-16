<?php

namespace App\Services\Rates;

use App\Enums\RateOperation;
use App\Enums\RatePlace;
use App\Models\BankRate;
use App\Models\CbuRateHistory;
use App\Models\Currency;
use Illuminate\Support\Collection;

class ExchangeRateQueryService
{
    /**
     * @return Collection<int, Currency>
     */
    public function activeCurrencies(): Collection
    {
        return Currency::query()
            ->active()
            ->ordered()
            ->with('cbuRate')
            ->get();
    }

    public function findCurrency(string $code): ?Currency
    {
        return Currency::query()
            ->active()
            ->where('code', strtoupper($code))
            ->with('cbuRate')
            ->first();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function bankRatesFor(
        Currency $currency,
        RateOperation $operation = RateOperation::Buy,
        RatePlace $place = RatePlace::Cash,
    ): array {
        $column = $operation === RateOperation::Buy ? 'sell' : 'buy';
        $direction = $operation === RateOperation::Buy ? 'asc' : 'desc';

        $rates = BankRate::query()
            ->with('bank')
            ->where('currency_id', $currency->id)
            ->where('place', $place)
            ->whereNotNull($column)
            ->whereHas('bank', fn ($q) => $q->active())
            ->orderBy($column, $direction)
            ->get();

        return $rates->map(function (BankRate $rate) use ($operation, $column) {
            $bank = $rate->bank;

            return [
                'bank_id' => $bank->id,
                'bank_name' => $bank->name,
                'bank_slug' => $bank->slug,
                'logo_url' => $bank->logoUrl(),
                'website' => $bank->website,
                'rate' => $rate->{$column},
                'buy' => $rate->buy,
                'sell' => $rate->sell,
                'operation' => $operation->value,
                'place' => $rate->place->value,
                'fetched_at' => $rate->fetched_at?->format('d.m.Y H:i'),
                'fetched_at_iso' => $rate->fetched_at?->toIso8601String(),
            ];
        })->all();
    }

    /**
     * @return array{cash: array{buy: ?string, sell: ?string}, atm: array{buy: ?string, sell: ?string}, app: array{buy: ?string, sell: ?string}}
     */
    public function bestRates(Currency $currency): array
    {
        $result = [];

        foreach (RatePlace::cases() as $place) {
            // Со стороны банка: лучшая покупка = max(buy), лучшая продажа = min(sell).
            $buyBest = BankRate::query()
                ->where('currency_id', $currency->id)
                ->where('place', $place)
                ->whereNotNull('buy')
                ->whereHas('bank', fn ($q) => $q->active())
                ->orderByDesc('buy')
                ->value('buy');

            $sellBest = BankRate::query()
                ->where('currency_id', $currency->id)
                ->where('place', $place)
                ->whereNotNull('sell')
                ->whereHas('bank', fn ($q) => $q->active())
                ->orderBy('sell')
                ->value('sell');

            $result[$place->value] = [
                'buy' => $buyBest !== null ? (string) $buyBest : null,
                'sell' => $sellBest !== null ? (string) $sellBest : null,
            ];
        }

        return $result;
    }

    /**
     * Данные для виджета на главной: по каждой валюте CBU + лучшая покупка/продажа (cash).
     *
     * @return list<array<string, mixed>>
     */
    public function homepageBestRates(RatePlace $place = RatePlace::Cash): array
    {
        return $this->activeCurrencies()->map(function (Currency $currency) use ($place) {
            $bestBuy = BankRate::query()
                ->with('bank')
                ->where('currency_id', $currency->id)
                ->where('place', $place)
                ->whereNotNull('buy')
                ->whereHas('bank', fn ($q) => $q->active())
                ->orderByDesc('buy')
                ->first();

            $bestSell = BankRate::query()
                ->with('bank')
                ->where('currency_id', $currency->id)
                ->where('place', $place)
                ->whereNotNull('sell')
                ->whereHas('bank', fn ($q) => $q->active())
                ->orderBy('sell')
                ->first();

            return [
                'code' => $currency->code,
                'name_ru' => $currency->name_ru,
                'flag' => $currency->flag,
                'cbu_rate' => $currency->cbuRate?->rate,
                'cbu_diff' => $currency->cbuRate?->diff,
                'best_buy' => $bestBuy ? [
                    'rate' => (string) $bestBuy->buy,
                    'bank_name' => $bestBuy->bank->name,
                    'bank_slug' => $bestBuy->bank->slug,
                ] : null,
                'best_sell' => $bestSell ? [
                    'rate' => (string) $bestSell->sell,
                    'bank_name' => $bestSell->bank->name,
                    'bank_slug' => $bestSell->bank->slug,
                ] : null,
            ];
        })->all();
    }

    /**
     * @return list<array{date: string, rate: string}>
     */
    public function cbuHistory(Currency $currency, int $days = 30): array
    {
        return CbuRateHistory::query()
            ->where('currency_id', $currency->id)
            ->where('rate_date', '>=', now()->subDays($days)->toDateString())
            ->orderBy('rate_date')
            ->get()
            ->map(fn (CbuRateHistory $row) => [
                'date' => $row->rate_date->format('Y-m-d'),
                'rate' => (string) $row->rate,
                'diff' => $row->diff !== null ? (string) $row->diff : null,
            ])
            ->all();
    }
}
