<?php

namespace App\Http\Requests\Storefront;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $paymentMethodMap = [
            'credit_card' => 'card',
            'cash_on_delivery' => 'cod',
            'bank_transfer' => 'transfer',
        ];

        $incoming = (string) $this->input('payment_method', '');

        if (array_key_exists($incoming, $paymentMethodMap)) {
            $this->merge([
                'payment_method' => $paymentMethodMap[$incoming],
            ]);
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'address_id' => ['nullable', Rule::exists('addresses', 'id')->where('user_id', $this->user()?->id)],
            'shipping_name' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'shipping_phone' => ['required_without:address_id', 'nullable', 'string', 'max:30'],
            'shipping_address' => ['required_without:address_id', 'nullable', 'string'],
            'shipping_city' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'shipping_state' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'customer_email' => ['required', 'email', 'max:255'],
            'payment_method' => ['required', 'string', 'in:cod,card,transfer,paypal'],
            'shipping_method' => ['nullable', 'string', 'in:standard,express,same_day'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Prepare the validated data for the order service.
     *
     * @return array<string, mixed>
     */
    public function validatedForOrder(): array
    {
        $data = $this->validated();
        $userId = (int) ($this->user()?->id ?? 0);

        if (! empty($data['address_id'])) {
            $address = Address::query()
                ->where('user_id', $userId)
                ->find($data['address_id']);

            if ($address !== null) {
                $data['shipping_address'] = [
                    'full_name' => $address->full_name,
                    'phone' => $address->phone,
                    'street_address' => $address->street_address,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'email' => $data['customer_email'] ?? null,
                ];
            }
        } else {
            $data['shipping_address'] = [
                'full_name' => $data['shipping_name'] ?? '',
                'phone' => $data['shipping_phone'] ?? '',
                'street_address' => $data['shipping_address'] ?? '',
                'city' => $data['shipping_city'] ?? '',
                'state' => $data['shipping_state'] ?? '',
                'postal_code' => $data['shipping_postal_code'] ?? '',
                'country' => $data['shipping_country'] ?? '',
                'email' => $data['customer_email'] ?? null,
            ];
        }

        return $data;
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Please choose a payment method.',
            'shipping_address.full_name.required' => 'Shipping full name is required.',
            'shipping_address.street_address.required' => 'Shipping street address is required.',
        ];
    }
}
