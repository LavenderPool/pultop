<?php

namespace App\Support;

class Money
{
    public static function formatRate(mixed $value, int $decimals = 2): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return number_format((float) $value, $decimals, '.', ' ');
    }
}
