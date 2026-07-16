<?php

namespace App\Services\Rates\Dto;

use App\Enums\RatePlace;

final readonly class ParsedBankRate
{
    public function __construct(
        public string $currencyCode,
        public RatePlace $place,
        public ?string $buy,
        public ?string $sell,
    ) {}
}
