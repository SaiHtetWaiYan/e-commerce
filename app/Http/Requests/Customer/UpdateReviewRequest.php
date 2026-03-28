<?php

namespace App\Http\Requests\Customer;

use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $review = $this->route('review');

        return $review instanceof Review && (int) $review->user_id === (int) $this->user()?->id;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:3000'],
            'media' => ['nullable', 'array', 'max:5'],
            'media.*' => [
                'file',
                File::types(['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'mov', 'webm'])->max(12 * 1024),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Rating is required.',
            'rating.min' => 'Rating must be between 1 and 5.',
            'rating.max' => 'Rating must be between 1 and 5.',
            'media.max' => 'You can upload up to 5 review files.',
        ];
    }
}
