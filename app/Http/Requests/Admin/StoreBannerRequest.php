<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
            'link' => ['nullable', 'url', 'max:2048'],
            'position' => ['required', 'string', Rule::in(['hero', 'sidebar', 'footer', 'category'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Upload a banner image before saving.',
            'position.in' => 'Choose one of the supported banner positions.',
            'expires_at.after_or_equal' => 'The expiry date must be after the start date.',
        ];
    }
}
