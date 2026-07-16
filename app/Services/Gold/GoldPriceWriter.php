<?php

namespace App\Services\Gold;

use App\Models\GoldPrice;
use App\Models\GoldPriceHistory;
use App\Models\GoldSalePoint;
use App\Services\Gold\Dto\ParsedGoldCurrentPrice;
use App\Services\Gold\Dto\ParsedGoldHistoryPoint;
use App\Services\Gold\Dto\ParsedGoldSalePoint;
use Illuminate\Support\Facades\DB;

class GoldPriceWriter
{
    /**
     * @param  list<ParsedGoldHistoryPoint>  $history
     * @param  list<ParsedGoldCurrentPrice>  $current
     * @param  list<ParsedGoldSalePoint>  $salePoints
     * @return array{history_written: int, current_written: int, sale_points_written: int}
     */
    public function write(array $history, array $current, array $salePoints): array
    {
        $historyWritten = 0;
        $currentWritten = 0;
        $salePointsWritten = 0;

        DB::transaction(function () use ($history, $current, $salePoints, &$historyWritten, &$currentWritten, &$salePointsWritten): void {
            $now = now();

            foreach ($history as $point) {
                GoldPriceHistory::query()->updateOrCreate(
                    [
                        'weight_grams' => $point->weight->value,
                        'price_date' => $point->priceDate->toDateString(),
                    ],
                    [
                        'price' => $point->price,
                        'diff' => $point->diff,
                    ],
                );
                $historyWritten++;
            }

            foreach ($current as $item) {
                GoldPrice::query()->updateOrCreate(
                    ['weight_grams' => $item->weight->value],
                    [
                        'sell_price' => $item->sellPrice,
                        'buyback_good' => $item->buybackGood,
                        'buyback_damaged' => $item->buybackDamaged,
                        'diff' => $item->diff,
                        'priced_on' => $item->pricedOn?->toDateString(),
                        'fetched_at' => $now,
                    ],
                );
                $currentWritten++;
            }

            if ($salePoints !== []) {
                $salePointsWritten = $this->syncSalePoints($salePoints);
            }
        });

        return [
            'history_written' => $historyWritten,
            'current_written' => $currentWritten,
            'sale_points_written' => $salePointsWritten,
        ];
    }

    /**
     * @param  list<ParsedGoldSalePoint>  $salePoints
     */
    private function syncSalePoints(array $salePoints): int
    {
        $keepIds = [];

        foreach ($salePoints as $point) {
            $model = GoldSalePoint::query()->updateOrCreate(
                [
                    'region' => $point->region,
                    'bank_name' => $point->bankName,
                    'address' => $point->address,
                ],
                [
                    'phone' => $point->phone,
                    'sort_order' => $point->sortOrder,
                    'is_active' => true,
                ],
            );
            $keepIds[] = $model->id;
        }

        GoldSalePoint::query()
            ->whereNotIn('id', $keepIds)
            ->update(['is_active' => false]);

        return count($keepIds);
    }
}
