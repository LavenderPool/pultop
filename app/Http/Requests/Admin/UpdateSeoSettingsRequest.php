<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSeoSettingsRequest extends FormRequest
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
        /** @var list<string> $keys */
        $keys = array_keys(config('seo.pages', []));

        return [
            'pages' => ['required', 'array'],
            'pages.*.key' => ['required', 'string', Rule::in($keys)],
            'pages.*.title' => ['nullable', 'string', 'max:255'],
            'pages.*.description' => ['nullable', 'string', 'max:500'],
            'pages.*.keywords' => ['nullable', 'string', 'max:500'],
            'pages.*.h1' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pages.required' => 'Список страниц обязателен.',
            'pages.*.key.required' => 'Не указан ключ страницы.',
            'pages.*.key.in' => 'Неизвестный ключ страницы.',
            'pages.*.title.max' => 'Title слишком длинный (макс. 255).',
            'pages.*.description.max' => 'Description слишком длинный (макс. 500).',
            'pages.*.keywords.max' => 'Keywords слишком длинные (макс. 500).',
            'pages.*.h1.max' => 'H1 слишком длинный (макс. 255).',
        ];
    }

    protected function prepareForValidation(): void
    {
        $pages = $this->input('pages');
        if (! is_array($pages)) {
            return;
        }

        $normalized = [];

        foreach ($pages as $page) {
            if (! is_array($page)) {
                continue;
            }

            $normalized[] = [
                'key' => (string) ($page['key'] ?? ''),
                'title' => $this->trimOrNull($page['title'] ?? null),
                'description' => $this->trimOrNull($page['description'] ?? null),
                'keywords' => $this->trimOrNull($page['keywords'] ?? null),
                'h1' => $this->trimOrNull($page['h1'] ?? null),
            ];
        }

        $this->merge(['pages' => $normalized]);
    }

    private function trimOrNull(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }
}
