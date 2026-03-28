<?php

namespace App\Http\Requests\Customer;

use App\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $orderItem = OrderItem::query()->find($this->input('order_item_id'));

        if (! $orderItem instanceof OrderItem) {
            return false;
        }

        $order = $orderItem->order;

        return $order !== null
            && (int) $order->user_id === (int) $this->user()?->id
            && (int) $orderItem->product_id === (int) $this->input('product_id');
    }

    public function rules(): array
    {
        return [
            'order_item_id' => ['required', 'integer', 'exists:order_items,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
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
