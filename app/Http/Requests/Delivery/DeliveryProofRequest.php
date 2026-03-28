<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDeliveryAgent() ?? false;
    }

    public function rules(): array
    {
        return [
            'proof_image' => ['required', 'image', 'max:5120'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
