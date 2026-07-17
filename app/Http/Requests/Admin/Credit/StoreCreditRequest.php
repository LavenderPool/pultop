<?php

namespace App\Http\Requests\Admin\Credit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCreditRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('credits', 'slug')],
            'bank_id' => ['nullable', 'integer', Rule::exists('banks', 'id')],
            'currency' => ['nullable', 'string', 'max:16'],
            'rate_display' => ['nullable', 'string', 'max:255'],
            'term_display' => ['nullable', 'string', 'max:255'],
            'amount_display' => ['nullable', 'string', 'max:255'],
            'down_payment' => ['nullable', 'string', 'max:255'],
            'special_conditions' => ['nullable', 'string'],
            'apply_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'type_ids' => ['nullable', 'array'],
            'type_ids.*' => ['integer', Rule::exists('credit_types', 'id')],
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
            'title.required' => 'Укажите название кредита.',
            'title.max' => 'Название не должно превышать 255 символов.',
            'slug.unique' => 'Такой slug уже используется.',
            'slug.alpha_dash' => 'Slug может содержать только латиницу, цифры, дефис и подчёркивание.',
            'bank_id.exists' => 'Выбранный банк не найден.',
            'apply_url.url' => 'Укажите корректный URL заявки.',
            'type_ids.*.exists' => 'Один из типов кредита не найден.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $value = $this->input('is_active');
            if (is_array($value)) {
                $value = end($value);
            }

            $this->merge([
                'is_active' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }

        $typeIds = $this->input('type_ids', []);
        if (! is_array($typeIds)) {
            $typeIds = [$typeIds];
        }

        $this->merge([
            'type_ids' => array_values(array_filter($typeIds, fn ($id) => filled($id))),
            'apply_url' => filled($this->input('apply_url')) ? $this->input('apply_url') : null,
            'bank_id' => filled($this->input('bank_id')) ? $this->input('bank_id') : null,
        ]);
    }
}
