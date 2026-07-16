<?php

namespace App\Services\Gold\Dto;

final readonly class ParsedGoldSalePoint
{
    public function __construct(
        public string $region,
        public string $bankName,
        public string $address,
        public ?string $phone,
        public int $sortOrder,
    ) {}
}
