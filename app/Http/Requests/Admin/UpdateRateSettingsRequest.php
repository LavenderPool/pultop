<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRateSettingsRequest extends FormRequest
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
            'rates_parse_enabled' => ['required', 'boolean'],
            'rates_parse_cron' => [
                'required',
                'string',
                Rule::in(['0 * * * *', '*/30 * * * *', '0 */2 * * *', '0 9-18 * * *']),
            ],
            'rates_parse_concurrency' => ['required', 'integer', 'min:1', 'max:20'],
            'rates_parse_delay_ms' => ['required', 'integer', 'min:0', 'max:10000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rates_parse_enabled.required' => 'Укажите, включён ли парсер.',
            'rates_parse_cron.required' => 'Выберите расписание парсера.',
            'rates_parse_cron.in' => 'Выбрано недопустимое расписание.',
            'rates_parse_concurrency.required' => 'Укажите число потоков.',
            'rates_parse_concurrency.min' => 'Минимум 1 поток.',
            'rates_parse_concurrency.max' => 'Максимум 20 потоков.',
            'rates_parse_delay_ms.required' => 'Укажите задержку между запросами.',
            'rates_parse_delay_ms.min' => 'Задержка не может быть отрицательной.',
            'rates_parse_delay_ms.max' => 'Задержка не больше 10000 мс.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('rates_parse_enabled')) {
            $value = $this->input('rates_parse_enabled');
            if (is_array($value)) {
                $value = end($value);
            }

            $this->merge([
                'rates_parse_enabled' => filter_var(
                    $value,
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE,
                ) ?? false,
            ]);
        }
    }
}
