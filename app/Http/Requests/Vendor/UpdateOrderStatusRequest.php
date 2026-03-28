<?php

namespace App\Http\Requests\Vendor;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        if (! $order instanceof Order) {
            return false;
        }

        if (! ($this->user()?->can('update', $order) ?? false)) {
            return false;
        }

        return ! in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Refunded], true);
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(OrderStatus::class)->only([
                    OrderStatus::Confirmed,
                    OrderStatus::Processing,
                    OrderStatus::Shipped,
                    OrderStatus::Delivered,
                ]),
            ],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Order status is required.',
            'status.enum' => 'Vendors can only set confirmed, processing, shipped, or delivered statuses.',
        ];
    }
}
