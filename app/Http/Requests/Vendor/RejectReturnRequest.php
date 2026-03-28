<?php

namespace App\Http\Requests\Vendor;

use App\Enums\ReturnStatus;
use App\Models\ReturnRequest;
use Illuminate\Foundation\Http\FormRequest;

class RejectReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        $returnRequest = $this->route('returnRequest');

        if (! $returnRequest instanceof ReturnRequest || ! $this->user()?->isVendor()) {
            return false;
        }

        $vendorId = (int) $this->user()->id;
        $totalItems = $returnRequest->items()->count();
        $vendorOwnedItems = $returnRequest->items()
            ->whereHas('orderItem', fn ($query) => $query->where('vendor_id', $vendorId))
            ->count();

        return $returnRequest->status === ReturnStatus::Pending
            && $totalItems > 0
            && $totalItems === $vendorOwnedItems;
    }

    public function rules(): array
    {
        return [
            'notes' => ['required', 'string', 'max:1000'],
        ];
    }
}
