<?php

namespace App\Http\Requests\Admin\Article;

use App\Enums\ArticleCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
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
        $articleId = $this->route('article')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('articles', 'slug')->ignore($articleId)],
            'category' => ['required', Rule::enum(ArticleCategory::class)],
            'excerpt' => ['nullable', 'string', 'max:5000'],
            'body' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['sometimes', 'boolean'],
            'cover' => ['nullable', 'image', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Укажите заголовок.',
            'title.max' => 'Заголовок не должен превышать 255 символов.',
            'slug.unique' => 'Такой slug уже используется.',
            'slug.alpha_dash' => 'Slug может содержать только латиницу, цифры, дефис и подчёркивание.',
            'category.required' => 'Выберите категорию.',
            'body.required' => 'Укажите текст материала.',
            'cover.image' => 'Обложка должна быть изображением.',
            'cover.max' => 'Размер обложки не должен превышать 4 МБ.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_published')) {
            $value = $this->input('is_published');
            if (is_array($value)) {
                $value = end($value);
            }

            $this->merge([
                'is_published' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
    }
}
