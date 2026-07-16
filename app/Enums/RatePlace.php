<?php

namespace App\Enums;

enum RatePlace: string
{
    case Cash = 'cash';
    case Atm = 'atm';
    case App = 'app';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'В обменном пункте',
            self::Atm => 'В банкомате',
            self::App => 'В приложении',
        };
    }
}
