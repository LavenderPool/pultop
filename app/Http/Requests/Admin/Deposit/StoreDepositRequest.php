<?php

namespace App\Http\Requests\Admin\Deposit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepositRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('deposits', 'slug')],
            'bank_id' => ['nullable', 'integer', Rule::exists('banks', 'id')],
            'currency' => ['nullable', 'string', 'max:16'],
            'rate_display' => ['nullable', 'string', 'max:255'],
            'term_display' => ['nullable', 'string', 'max:255'],
            'amount_display' => ['nullable', 'string', 'max:255'],
            'term_min_months' => ['nullable', 'integer', 'min:0', 'max:1200'],
            'term_max_months' => ['nullable', 'integer', 'min:0', 'max:1200'],
            'amount_min' => ['nullable', 'integer', 'min:0'],
            'amount_max' => ['nullable', 'integer', 'min:0'],
            'early_termination' => ['sometimes', 'boolean'],
            'partial_withdrawal' => ['sometimes', 'boolean'],
            'capitalization' => ['sometimes', 'boolean'],
            'is_online' => ['sometimes', 'boolean'],
            'special_conditions' => ['nullable', 'string'],
            'apply_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'rate_rows' => ['nullable', 'array'],
            'rate_rows.*.rate' => ['nullable', 'string', 'max:255'],
            'rate_rows.*.term' => ['nullable', 'string', 'max:255'],
            'rate_rows.*.note' => ['nullable', 'string', 'max:500'],
            'conditions' => ['nullable', 'array'],
            'conditions.*.label' => ['nullable', 'string', 'max:255'],
            'conditions.*.value' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Укажите название вклада.',
            'title.max' => 'Название не должно превышать 255 символов.',
            'slug.unique' => 'Такой slug уже используется.',
            'slug.alpha_dash' => 'Slug может содержать только латиницу, цифры, дефис и подчёркивание.',
            'bank_id.exists' => 'Выбранный банк не найден.',
            'apply_url.url' => 'Укажите корректный URL заявки.',
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['is_active', 'early_termination', 'partial_withdrawal', 'capitalization', 'is_online'] as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $value = $this->input($field);
            if (is_array($value)) {
                $value = end($value);
            }

            $this->merge([
                $field => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
    }
}
