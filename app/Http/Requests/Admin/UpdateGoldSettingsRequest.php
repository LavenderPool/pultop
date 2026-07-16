<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGoldSettingsRequest extends FormRequest
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
            'gold_parse_enabled' => ['required', 'boolean'],
            'gold_parse_cron' => [
                'required',
                'string',
                Rule::in(['0 8 * * *', '0 9 * * *', '0 */6 * * *', '0 * * * *']),
            ],
            'gold_parse_delay_ms' => ['required', 'integer', 'min:0', 'max:10000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gold_parse_enabled.required' => 'Укажите, включён ли парсер.',
            'gold_parse_cron.required' => 'Выберите расписание парсера.',
            'gold_parse_cron.in' => 'Выбрано недопустимое расписание.',
            'gold_parse_delay_ms.required' => 'Укажите задержку между запросами.',
            'gold_parse_delay_ms.min' => 'Задержка не может быть отрицательной.',
            'gold_parse_delay_ms.max' => 'Задержка не больше 10000 мс.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('gold_parse_enabled')) {
            $value = $this->input('gold_parse_enabled');
            if (is_array($value)) {
                $value = end($value);
            }

            $this->merge([
                'gold_parse_enabled' => filter_var(
                    $value,
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE,
                ) ?? false,
            ]);
        }
    }
}
