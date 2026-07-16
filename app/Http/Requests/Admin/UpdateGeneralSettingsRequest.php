<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'social_facebook_url' => ['nullable', 'string', 'url', 'max:500'],
            'social_x_url' => ['nullable', 'string', 'url', 'max:500'],
            'social_instagram_url' => ['nullable', 'string', 'url', 'max:500'],
            'social_youtube_url' => ['nullable', 'string', 'url', 'max:500'],
            'social_telegram_url' => ['nullable', 'string', 'url', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'social_facebook_url.url' => 'Укажите корректный URL Facebook.',
            'social_facebook_url.max' => 'URL Facebook слишком длинный.',
            'social_x_url.url' => 'Укажите корректный URL X.',
            'social_x_url.max' => 'URL X слишком длинный.',
            'social_instagram_url.url' => 'Укажите корректный URL Instagram.',
            'social_instagram_url.max' => 'URL Instagram слишком длинный.',
            'social_youtube_url.url' => 'Укажите корректный URL YouTube.',
            'social_youtube_url.max' => 'URL YouTube слишком длинный.',
            'social_telegram_url.url' => 'Укажите корректный URL Telegram.',
            'social_telegram_url.max' => 'URL Telegram слишком длинный.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $keys = [
            'social_facebook_url',
            'social_x_url',
            'social_instagram_url',
            'social_youtube_url',
            'social_telegram_url',
        ];

        $merged = [];

        foreach ($keys as $key) {
            if (! $this->has($key)) {
                continue;
            }

            $value = trim((string) $this->input($key, ''));
            $merged[$key] = $value === '' ? null : $value;
        }

        $this->merge($merged);
    }
}
