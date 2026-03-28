<?php

namespace App\Http\Requests\Customer;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubmitReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        if (! $order instanceof Order) {
            return false;
        }

        return (int) $order->user_id === (int) $this->user()?->id;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:2000'],
            'order_item_ids' => ['required', 'array', 'min:1'],
            'order_item_ids.*' => ['integer', 'distinct', 'exists:order_items,id'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $order = $this->route('order');

                if (! $order instanceof Order) {
                    $validator->errors()->add('order_item_ids', 'Order not found.');

                    return;
                }

                $selectedIds = collect($this->input('order_item_ids', []))
                    ->filter(fn ($id): bool => is_numeric($id))
                    ->map(fn ($id): int => (int) $id)
                    ->unique()
                    ->values();

                if ($selectedIds->isEmpty()) {
                    return;
                }

                $countForOrder = OrderItem::query()
                    ->where('order_id', $order->id)
                    ->whereIn('id', $selectedIds)
                    ->count();

                if ($countForOrder !== $selectedIds->count()) {
                    $validator->errors()->add('order_item_ids', 'Selected items must belong to this order.');
                }
            },
        ];
    }
}
