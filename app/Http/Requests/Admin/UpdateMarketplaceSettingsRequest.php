<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarketplaceSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'default_currency' => ['required', 'string', 'max:8'],
            'default_shipping_fee' => ['required', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['required', 'numeric', 'min:0'],
            'default_tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'vendor_default_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'default_carrier' => ['required', 'string', 'max:100'],
            'order_number_prefix' => ['required', 'string', 'max:12'],
            'tracking_prefix' => ['required', 'string', 'max:12'],
            'vendor_require_approval' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'default_tax_rate.max' => 'Tax rate must be between 0 and 100 percent.',
            'vendor_default_commission_rate.max' => 'Commission rate must be between 0 and 100 percent.',
        ];
    }
}
