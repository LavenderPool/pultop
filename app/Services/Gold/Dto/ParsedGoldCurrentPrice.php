<?php

namespace App\Services\Gold\Dto;

use App\Enums\GoldWeight;
use Carbon\CarbonInterface;

final readonly class ParsedGoldCurrentPrice
{
    public function __construct(
        public GoldWeight $weight,
        public string $sellPrice,
        public ?string $buybackGood,
        public ?string $buybackDamaged,
        public ?string $diff,
        public ?CarbonInterface $pricedOn,
    ) {}
}
