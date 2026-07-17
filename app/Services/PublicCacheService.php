<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class PublicCacheService
{
    public const GROUP_RATES = 'rates';

    public const GROUP_GOLD = 'gold';

    public const GROUP_BANKS = 'banks';

    public const GROUP_CREDITS = 'credits';

    public const GROUP_DEPOSITS = 'deposits';

    public const GROUP_CARDS = 'cards';

    public const GROUP_ARTICLES = 'articles';

    /** @var list<string> */
    public const GROUPS = [
        self::GROUP_RATES,
        self::GROUP_GOLD,
        self::GROUP_BANKS,
        self::GROUP_CREDITS,
        self::GROUP_DEPOSITS,
        self::GROUP_CARDS,
        self::GROUP_ARTICLES,
    ];

    /** @var array<string, int> */
    private const TTL_SECONDS = [
        self::GROUP_RATES => 3600,
        self::GROUP_GOLD => 21600,
        self::GROUP_BANKS => 86400,
        self::GROUP_CREDITS => 86400,
        self::GROUP_DEPOSITS => 86400,
        self::GROUP_CARDS => 86400,
        self::GROUP_ARTICLES => 3600,
    ];

    /**
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public function remember(string $group, string $key, Closure $callback, ?int $ttl = null): mixed
    {
        $this->assertGroup($group);

        if (! $this->enabled()) {
            return $callback();
        }

        $ttl ??= self::TTL_SECONDS[$group];
        $cacheKey = $this->cacheKey($group, $key);

        return Cache::remember($cacheKey, $ttl, $callback);
    }

    public function forgetGroup(string $group): void
    {
        $this->assertGroup($group);

        if (! $this->enabled()) {
            return;
        }

        $versionKey = $this->versionKey($group);
        $current = (int) Cache::get($versionKey, 1);
        Cache::forever($versionKey, $current + 1);
    }

    public function flushAll(): void
    {
        Cache::flush();
    }

    /**
     * @param  array<string, mixed>  $parts
     */
    public function key(array $parts): string
    {
        ksort($parts);

        return md5((string) json_encode($parts, JSON_THROW_ON_ERROR));
    }

    private function cacheKey(string $group, string $key): string
    {
        $version = (int) Cache::get($this->versionKey($group), 1);

        return "public:{$group}:v{$version}:{$key}";
    }

    private function versionKey(string $group): string
    {
        return "public:{$group}:version";
    }

    private function enabled(): bool
    {
        return ! app()->environment('local');
    }

    private function assertGroup(string $group): void
    {
        if (! in_array($group, self::GROUPS, true)) {
            throw new InvalidArgumentException("Unknown public cache group [{$group}].");
        }
    }
}
