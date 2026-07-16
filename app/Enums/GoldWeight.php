<?php

namespace App\Enums;

enum GoldWeight: int
{
    case G5 = 5;
    case G10 = 10;
    case G20 = 20;
    case G50 = 50;
    case G100 = 100;

    public function wpIndex(): int
    {
        return match ($this) {
            self::G5 => 0,
            self::G10 => 1,
            self::G20 => 2,
            self::G50 => 3,
            self::G100 => 4,
        };
    }

    public function label(): string
    {
        return $this->value.'гр.';
    }

    public function labelLong(): string
    {
        return $this->value.' грамм';
    }

    public function imageFile(): string
    {
        return match ($this) {
            self::G5 => 'gr05.jpg',
            self::G10 => 'gr10.jpg',
            self::G20 => 'gr20.jpg',
            self::G50 => 'gr50.jpg',
            self::G100 => 'gr100.jpg',
        };
    }

    public static function fromWpIndex(int $index): ?self
    {
        return match ($index) {
            0 => self::G5,
            1 => self::G10,
            2 => self::G20,
            3 => self::G50,
            4 => self::G100,
            default => null,
        };
    }

    /**
     * @return list<self>
     */
    public static function ordered(): array
    {
        return [self::G5, self::G10, self::G20, self::G50, self::G100];
    }
}
