<?php

namespace App\Services\Gold;

use App\Enums\GoldWeight;
use App\Models\GoldPrice;
use App\Models\GoldPriceHistory;
use App\Models\GoldSalePoint;
use App\Support\Money;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GoldQueryService
{
    /**
     * @return list<array{
     *     weight_grams: int,
     *     weight_label: string,
     *     weight_label_long: string,
     *     image: string,
     *     sell_price: string|null,
     *     sell_price_formatted: string,
     *     buyback_good: string|null,
     *     buyback_good_formatted: string,
     *     buyback_damaged: string|null,
     *     buyback_damaged_formatted: string,
     *     diff: string|null,
     *     diff_formatted: string|null,
     *     diff_positive: bool|null,
     *     priced_on: string|null,
     *     wp_index: int
     * }>
     */
    public function currentPrices(): array
    {
        $byWeight = GoldPrice::query()
            ->get()
            ->keyBy('weight_grams');

        $items = [];

        foreach (GoldWeight::ordered() as $weight) {
            /** @var GoldPrice|null $price */
            $price = $byWeight->get($weight->value);

            $diff = $price?->diff;
            $diffPositive = null;

            if ($diff !== null) {
                $diffPositive = (float) $diff >= 0;
            }

            $items[] = [
                'weight_grams' => $weight->value,
                'weight_label' => $weight->label(),
                'weight_label_long' => $weight->labelLong(),
                'image' => $weight->imageFile(),
                'sell_price' => $price?->sell_price,
                'sell_price_formatted' => $this->formatSum($price?->sell_price),
                'buyback_good' => $price?->buyback_good,
                'buyback_good_formatted' => $this->formatSum($price?->buyback_good),
                'buyback_damaged' => $price?->buyback_damaged,
                'buyback_damaged_formatted' => $this->formatSum($price?->buyback_damaged),
                'diff' => $diff,
                'diff_formatted' => $diff !== null ? $this->formatDiff($diff) : null,
                'diff_positive' => $diffPositive,
                'priced_on' => $price?->priced_on?->format('d.m.Y'),
                'wp_index' => $weight->wpIndex(),
            ];
        }

        return $items;
    }

    public function latestPricedOn(): ?string
    {
        $date = GoldPrice::query()->max('priced_on');

        if ($date === null) {
            return null;
        }

        return Carbon::parse($date)->format('d.m.Y');
    }

    /**
     * @return list<string>
     */
    public function regions(): array
    {
        return GoldSalePoint::query()
            ->active()
            ->orderBy('sort_order')
            ->pluck('region')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{region: string, bank_name: string, address: string, phones: list<string>}>
     */
    public function salePoints(): Collection
    {
        return GoldSalePoint::query()
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (GoldSalePoint $point) => [
                'region' => $point->region,
                'bank_name' => $point->bank_name,
                'address' => $point->address,
                'phones' => $this->splitPhones($point->phone),
            ]);
    }

    /**
     * @return list<string>
     */
    private function splitPhones(?string $phone): array
    {
        if ($phone === null) {
            return [];
        }

        $phone = trim($phone);

        if ($phone === '') {
            return [];
        }

        if (str_contains($phone, "\n")) {
            $parts = preg_split('/\R+/u', $phone) ?: [];
        } else {
            $parts = preg_split('/\s+(?=\+)/u', $phone) ?: [];
        }

        $phones = [];

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part !== '') {
                $phones[] = $part;
            }
        }

        return $phones;
    }

    /**
     * Chart payload compatible with WP pul_get_gold_chart.
     *
     * @return array{
     *     data: array{labels: list<string>, datasets: list<array<string, mixed>>, options: array<string, mixed>},
     *     rows: list<array{date: string, price: float, diff: float|null}>
     * }
     */
    public function chartPayload(int $wpIndex, int $period): array
    {
        $weight = GoldWeight::fromWpIndex($wpIndex) ?? GoldWeight::G5;
        $period = in_array($period, [7, 30, 90], true) ? $period : 7;

        $rows = GoldPriceHistory::query()
            ->where('weight_grams', $weight->value)
            ->orderByDesc('price_date')
            ->limit($period)
            ->get()
            ->sortBy('price_date')
            ->values();

        $labels = [];
        $data = [];
        $tableRows = [];

        foreach ($rows as $row) {
            $labels[] = $row->price_date->format('d.m.Y');
            $data[] = (float) $row->price;
        }

        foreach ($rows->sortByDesc('price_date')->values() as $row) {
            $tableRows[] = [
                'date' => $row->price_date->format('d.m.Y'),
                'price' => (float) $row->price,
                'diff' => $row->diff !== null ? (float) $row->diff : null,
            ];
        }

        return [
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'type' => 'line',
                    'label' => 'Цена за '.$weight->value.' гр. ',
                    'data' => $data,
                    'backgroundColor' => ['rgba(31, 54, 92, 0.9)'],
                    'borderColor' => ['rgba(248, 176, 2, 1)'],
                    'borderWidth' => 1,
                ]],
                'options' => [
                    'scales' => [
                        'y' => ['beginAtZero' => true],
                    ],
                ],
            ],
            'rows' => $tableRows,
        ];
    }

    private function formatSum(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return Money::formatRate($value, 0).' сум';
    }

    private function formatDiff(mixed $value): string
    {
        $number = (float) $value;
        $formatted = Money::formatRate(abs($number), 0);

        if ($number > 0) {
            return '+'.$formatted.' сум';
        }

        if ($number < 0) {
            return '-'.$formatted.' сум';
        }

        return $formatted.' сум';
    }
}
