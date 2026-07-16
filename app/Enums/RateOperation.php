<?php

namespace App\Enums;

enum RateOperation: string
{
    case Buy = 'buy';
    case Sell = 'sell';

    public function label(): string
    {
        return match ($this) {
            self::Buy => 'Я покупаю',
            self::Sell => 'Я продаю',
        };
    }
}
