<?php

namespace App\Enums;

enum CreditTypeSlug: string
{
    case Autoloan = 'avtokredity-v-uzbekistane';
    case Business = 'dlya-biznesa';
    case Mortgage = 'ipotechnye-kredity-v-uzbekistane';
    case Microloan = 'bankovskie-mikrozajmy-v-uzbekistane';
    case Education = 'obrazovatelnye-kredity-v-uzbekistane';
    case Overdraft = 'overdraft-v-uzbekistane';
    case Consumer = 'potrebitelskie-krediti';
    case Entrepreneurs = 'biznesmenam-v-uzbekistane';

    public function name(): string
    {
        return match ($this) {
            self::Autoloan => 'Автокредит',
            self::Business => 'Для бизнеса',
            self::Mortgage => 'Ипотека',
            self::Microloan => 'Микрозайм',
            self::Education => 'На образование',
            self::Overdraft => 'Овердрафт',
            self::Consumer => 'Потребительский',
            self::Entrepreneurs => 'Предпринимателям',
        };
    }

    public function title(): string
    {
        return match ($this) {
            self::Autoloan => 'Автокредиты в Узбекистане',
            self::Business => 'Кредиты для бизнеса',
            self::Mortgage => 'Ипотечные кредиты в Узбекистане',
            self::Microloan => 'Банковские микрозаймы в Узбекистане',
            self::Education => 'Образовательные кредиты в Узбекистане',
            self::Overdraft => 'Овердрафт в Узбекистане',
            self::Consumer => 'Потребительские кредиты',
            self::Entrepreneurs => 'Кредиты предпринимателям',
        };
    }

    /**
     * Публичный путь-алиас из меню (без ведущего слэша).
     * null — только /credit-type/{slug}.
     */
    public function menuAlias(): ?string
    {
        return match ($this) {
            self::Autoloan => 'avtokredity-v-uzbekistane',
            self::Business => null,
            self::Mortgage => 'ipotechnye-kredity-v-uzbekistane',
            self::Microloan => 'bankovskie-mikrozajmy-v-uzbekistane',
            self::Education => 'obrazovatelnye-kredity-v-uzbekistane',
            self::Overdraft => 'overdraft-v-uzbekistane',
            self::Consumer => 'potrebitelskie-krediti',
            self::Entrepreneurs => 'kredity-nachinayushhim-biznesmenam-v-uzbekistane',
        };
    }

    public static function tryFromAlias(string $alias): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $alias || $case->menuAlias() === $alias) {
                return $case;
            }
        }

        return null;
    }
}
