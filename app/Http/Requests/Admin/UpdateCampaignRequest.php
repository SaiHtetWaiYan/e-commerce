<?php

namespace App\Http\Requests\Admin;

use App\Enums\CampaignDiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('campaigns', 'slug')->ignore($this->campaign)],
            'description' => ['nullable', 'string'],
            'banner_image' => ['nullable', 'image', 'max:5120'],
            'thumbnail_image' => ['nullable', 'image', 'max:5120'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'badge_color' => ['nullable', 'string', 'max:20', 'regex:/^#?[0-9a-fA-F]{3,8}$/'],
            'discount_type' => ['required', Rule::enum(CampaignDiscountType::class)],
            'discount_value' => ['nullable', 'numeric', 'min:0', 'required_unless:discount_type,'.CampaignDiscountType::Custom->value],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
