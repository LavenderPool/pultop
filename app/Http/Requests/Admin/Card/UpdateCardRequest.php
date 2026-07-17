<?php

namespace App\Http\Requests\Admin\Card;

use App\Enums\CardType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCardRequest extends FormRequest
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
        $cardId = $this->route('card')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('cards', 'slug')->ignore($cardId),
            ],
            'bank_id' => ['nullable', 'integer', Rule::exists('banks', 'id')],
            'currency' => ['nullable', 'string', 'max:16'],
            'payment_system' => ['nullable', 'string', 'max:64'],
            'card_type' => ['nullable', 'string', Rule::enum(CardType::class)],
            'category' => ['nullable', 'string', 'max:64'],
            'issue_cost_display' => ['nullable', 'string', 'max:255'],
            'validity_display' => ['nullable', 'string', 'max:255'],
            'special_conditions' => ['nullable', 'string'],
            'apply_url' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'conditions' => ['nullable', 'array'],
            'conditions.*.label' => ['nullable', 'string', 'max:255'],
            'conditions.*.value' => ['nullable', 'string', 'max:2000'],
            'conditions.*.note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Укажите название карты.',
            'title.max' => 'Название не должно превышать 255 символов.',
            'slug.unique' => 'Такой slug уже используется.',
            'slug.alpha_dash' => 'Slug может содержать только латиницу, цифры, дефис и подчёркивание.',
            'bank_id.exists' => 'Выбранный банк не найден.',
            'apply_url.url' => 'Укажите корректный URL заявки.',
            'card_type.Illuminate\Validation\Rules\Enum' => 'Выберите корректный тип карты.',
            'image.image' => 'Файл должен быть изображением.',
            'image.max' => 'Размер изображения не должен превышать 5 МБ.',
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

        if ($this->input('card_type') === '') {
            $this->merge(['card_type' => null]);
        }
    }
}
