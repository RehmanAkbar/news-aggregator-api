<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'exists:categories,id',
            'preferred_sources' => 'nullable|array',
            'preferred_sources.*' => 'exists:sources,id',
            'preferred_authors' => 'nullable|array',
            'preferred_authors.*' => 'string|max:255'
        ];
    }
}