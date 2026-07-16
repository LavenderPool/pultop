<?php

namespace App\Http\Requests\Admin\Bank;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBankRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('banks', 'slug')],
            'address' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'website' => ['nullable', 'url', 'max:255'],
            'license' => ['nullable', 'string', 'max:255'],
            'mfo' => ['nullable', 'string', 'max:64'],
            'inn' => ['nullable', 'string', 'max:64'],
            'parser_code' => ['nullable', 'string', 'max:64', 'alpha_dash'],
            'rates_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Укажите название банка.',
            'name.max' => 'Название не должно превышать 255 символов.',
            'slug.unique' => 'Такой slug уже используется.',
            'slug.alpha_dash' => 'Slug может содержать только латиницу, цифры, дефис и подчёркивание.',
            'website.url' => 'Укажите корректный URL сайта.',
            'rates_url.url' => 'Укажите корректный URL источника курсов.',
            'logo.image' => 'Логотип должен быть изображением.',
            'logo.max' => 'Размер логотипа не должен превышать 2 МБ.',
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
    }
}
