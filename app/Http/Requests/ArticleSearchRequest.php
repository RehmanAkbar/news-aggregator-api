<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'category_id' => 'nullable|exists:categories,id',
            'source_id' => 'nullable|exists:sources,id',
            'author' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100'
        ];
    }
}