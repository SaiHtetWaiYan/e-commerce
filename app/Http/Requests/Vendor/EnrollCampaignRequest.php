<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isVendor();
    }

    public function rules(): array
    {
        $vendorId = (int) $this->user()->id;

        return [
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where(function ($query) use ($vendorId) {
                    $query->where('vendor_id', $vendorId);
                }),
            ],
            'custom_prices' => ['nullable', 'array'],
            'custom_prices.*' => ['nullable', 'numeric', 'min:0.01'],
        ];
    }
}
