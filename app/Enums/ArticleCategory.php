<?php

namespace App\Enums;

enum ArticleCategory: string
{
    case Article = 'article';
    case News = 'news';
    case BankNews = 'bank_news';
    case Events = 'events';

    public function label(): string
    {
        return match ($this) {
            self::Article => 'Статьи',
            self::News => 'Новости',
            self::BankNews => 'Новости банков',
            self::Events => 'События и Акции',
        };
    }

    public function publicSlug(): string
    {
        return match ($this) {
            self::Article => 'stati',
            self::News => 'novosti',
            self::BankNews => 'novosti-bankov',
            self::Events => 'sobytiya-i-akcii',
        };
    }

    public function wpId(): int
    {
        return match ($this) {
            self::Article => 24,
            self::News => 50,
            self::BankNews => 35,
            self::Events => 36,
        };
    }

    public static function fromPublicSlug(string $slug): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->publicSlug() === $slug) {
                return $case;
            }
        }

        return null;
    }

    public static function fromWpId(int $wpId): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->wpId() === $wpId) {
                return $case;
            }
        }

        return null;
    }

    /**
     * @param  list<int>  $wpIds
     */
    public static function resolveFromWpIds(array $wpIds): ?self
    {
        foreach ([self::Article, self::News, self::BankNews, self::Events] as $case) {
            if (in_array($case->wpId(), $wpIds, true)) {
                return $case;
            }
        }

        return null;
    }
}
