<?php

namespace App\Enums;

enum CardType: string
{
    case Debit = 'debit';
    case Credit = 'credit';

    public function label(): string
    {
        return match ($this) {
            self::Debit => 'Дебетовая',
            self::Credit => 'Кредитная',
        };
    }

    public static function tryFromLabel(?string $label): ?self
    {
        if ($label === null || $label === '') {
            return null;
        }

        $normalized = mb_strtolower(trim($label));

        return match (true) {
            str_contains($normalized, 'кредит') => self::Credit,
            str_contains($normalized, 'дебет') => self::Debit,
            $normalized === '1' || $normalized === 'credit' => self::Credit,
            $normalized === '0' || $normalized === 'debit' => self::Debit,
            default => null,
        };
    }
}
