<?php

namespace App\Http\Requests\Storefront;

use App\Models\CartItem;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = $this->route('item');

        if (! $item instanceof CartItem) {
            return false;
        }

        $userId = $this->user()?->id;

        if ($userId !== null) {
            return (int) $item->cart->user_id === (int) $userId;
        }

        $session = $this->session();

        return $session !== null && $item->cart->session_id === $session->getId();
    }

    public function rules(): array
    {
        return [
            'action' => ['nullable', 'string', 'in:increase,decrease'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'Quantity is required.',
        ];
    }
}
