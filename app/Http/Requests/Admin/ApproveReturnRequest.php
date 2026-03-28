<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ApproveReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'refund_amount' => ['nullable', 'numeric', 'min:0'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $returnRequest = $this->route('returnRequest');

                if ($returnRequest === null) {
                    return;
                }

                $maxRefund = (float) $returnRequest->refund_amount;
                $inputRefund = (float) $this->input('refund_amount', $maxRefund);

                if ($inputRefund > $maxRefund) {
                    $validator->errors()->add('refund_amount', 'Refund amount cannot exceed the requested refundable total.');
                }
            },
        ];
    }
}
