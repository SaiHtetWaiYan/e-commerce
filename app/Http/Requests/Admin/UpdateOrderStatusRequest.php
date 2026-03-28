<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $order instanceof Order
            ? ($this->user()?->isAdmin() ?? false)
            : false;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(OrderStatus::class)->only([
                    OrderStatus::Hold,
                    OrderStatus::Cancelled,
                    OrderStatus::Refunded,
                ]),
            ],
            'comment' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Order status is required.',
            'status.enum' => 'Admins can only set hold, cancelled, or refunded statuses.',
        ];
    }
}
