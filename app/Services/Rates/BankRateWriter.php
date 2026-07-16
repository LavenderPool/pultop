<?php

namespace App\Services\Rates;

use App\Models\Bank;
use App\Models\BankRate;
use App\Models\BankRateHistory;
use App\Models\Currency;
use App\Services\Rates\Dto\ParsedBankRate;
use Illuminate\Support\Facades\DB;

class BankRateWriter
{
    /**
     * @param  list<ParsedBankRate>  $rates
     */
    public function write(Bank $bank, array $rates): int
    {
        if ($rates === []) {
            return 0;
        }

        $currencies = Currency::query()
            ->whereIn('code', collect($rates)->pluck('currencyCode')->unique()->all())
            ->get()
            ->keyBy(fn (Currency $c) => strtoupper($c->code));

        $now = now();
        $written = 0;

        DB::transaction(function () use ($bank, $rates, $currencies, $now, &$written): void {
            foreach ($rates as $rate) {
                $currency = $currencies->get(strtoupper($rate->currencyCode));

                if ($currency === null) {
                    continue;
                }

                BankRate::query()->updateOrCreate(
                    [
                        'bank_id' => $bank->id,
                        'currency_id' => $currency->id,
                        'place' => $rate->place->value,
                    ],
                    [
                        'buy' => $rate->buy,
                        'sell' => $rate->sell,
                        'fetched_at' => $now,
                    ],
                );

                BankRateHistory::query()->create([
                    'bank_id' => $bank->id,
                    'currency_id' => $currency->id,
                    'place' => $rate->place->value,
                    'buy' => $rate->buy,
                    'sell' => $rate->sell,
                    'recorded_at' => $now,
                ]);

                $written++;
            }
        });

        return $written;
    }
}
