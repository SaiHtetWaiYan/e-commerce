<?php

namespace App\Http\Requests\Vendor;

use App\Enums\ReturnStatus;
use App\Models\ReturnRequest;
use Illuminate\Foundation\Http\FormRequest;

class ApproveReturnRequest extends FormRequest
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
        /** @var ReturnRequest|null $returnRequest */
        $returnRequest = $this->route('returnRequest');

        return [
            'refund_amount' => ['nullable', 'numeric', 'min:0', 'max:'.number_format((float) ($returnRequest?->refund_amount ?? 0), 2, '.', '')],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
