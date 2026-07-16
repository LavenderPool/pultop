<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private const CACHE_PREFIX = 'settings.';

    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever(self::CACHE_PREFIX.$key, function () use ($key, $default) {
            $setting = Setting::query()->where('key', $key)->first();

            return $setting?->value ?? $default;
        });
    }

    public function getBool(string $key, bool $default = false): bool
    {
        $value = $this->get($key, $default ? '1' : '0');

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function set(string $key, mixed $value): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value],
        );

        Cache::forget(self::CACHE_PREFIX.$key);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function isRatesParseEnabled(): bool
    {
        return $this->getBool('rates_parse_enabled', true);
    }

    public function ratesParseCron(): string
    {
        return (string) $this->get('rates_parse_cron', '0 * * * *');
    }

    public function ratesParseConcurrency(): int
    {
        return max(1, min(20, (int) $this->get('rates_parse_concurrency', 5)));
    }

    public function ratesParseDelayMs(): int
    {
        return max(0, min(10000, (int) $this->get('rates_parse_delay_ms', 300)));
    }

    public function isGoldParseEnabled(): bool
    {
        return $this->getBool('gold_parse_enabled', true);
    }

    public function goldParseCron(): string
    {
        return (string) $this->get('gold_parse_cron', '0 8 * * *');
    }

    public function goldParseDelayMs(): int
    {
        return max(0, min(10000, (int) $this->get('gold_parse_delay_ms', 300)));
    }

    /**
     * @return array{
     *     social_facebook_url: string,
     *     social_x_url: string,
     *     social_instagram_url: string,
     *     social_youtube_url: string,
     *     social_telegram_url: string
     * }
     */
    public function generalSettings(): array
    {
        return [
            'social_facebook_url' => (string) $this->get('social_facebook_url', ''),
            'social_x_url' => (string) $this->get('social_x_url', ''),
            'social_instagram_url' => (string) $this->get('social_instagram_url', ''),
            'social_youtube_url' => (string) $this->get('social_youtube_url', ''),
            'social_telegram_url' => (string) $this->get('social_telegram_url', ''),
        ];
    }

    /**
     * Non-empty social URLs for the public top-bar.
     *
     * @return list<array{key: string, url: string, class: string, title: string}>
     */
    public function socialLinks(): array
    {
        $definitions = [
            [
                'key' => 'social_facebook_url',
                'class' => 'facebook',
                'title' => 'Facebook page opens in new window',
            ],
            [
                'key' => 'social_x_url',
                'class' => 'twitter',
                'title' => 'X page opens in new window',
            ],
            [
                'key' => 'social_instagram_url',
                'class' => 'instagram',
                'title' => 'Instagram page opens in new window',
            ],
            [
                'key' => 'social_youtube_url',
                'class' => 'you-tube',
                'title' => 'YouTube page opens in new window',
            ],
            [
                'key' => 'social_telegram_url',
                'class' => 'telegram',
                'title' => 'Telegram page opens in new window',
            ],
        ];

        $links = [];

        foreach ($definitions as $definition) {
            $url = trim((string) $this->get($definition['key'], ''));
            if ($url === '') {
                continue;
            }

            $links[] = [
                'key' => $definition['key'],
                'url' => $url,
                'class' => $definition['class'],
                'title' => $definition['title'],
            ];
        }

        return $links;
    }
}
