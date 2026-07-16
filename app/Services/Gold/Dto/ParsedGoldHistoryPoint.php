<?php

namespace App\Services\Gold\Dto;

use App\Enums\GoldWeight;
use Carbon\CarbonInterface;

final readonly class ParsedGoldHistoryPoint
{
    public function __construct(
        public GoldWeight $weight,
        public CarbonInterface $priceDate,
        public string $price,
        public ?string $diff,
    ) {}
}
