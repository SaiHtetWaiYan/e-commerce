<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ReturnStatus;
use App\Mail\ReturnStatusMail;
use App\Mail\VendorReturnNotificationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class ReturnService
{
    public function __construct(public PaymentService $paymentService) {}

    /**
     * @param  array<int, int>  $orderItemIds
     */
    public function createRequest(Order $order, User $user, string $reason, array $orderItemIds): ReturnRequest
    {
        if ($order->status !== OrderStatus::Delivered) {
            throw new InvalidArgumentException('Only delivered orders can be returned.');
        }

        if ($order->delivered_at && $order->delivered_at->diffInDays(now()) > 7) {
            throw new InvalidArgumentException('Return window has expired (7 days).');
        }

        $existing = ReturnRequest::query()
            ->where('order_id', $order->id)
            ->whereIn('status', [ReturnStatus::Pending, ReturnStatus::Approved])
            ->first();

        if ($existing) {
            throw new InvalidArgumentException('A return request already exists for this order.');
        }

        $selectedIds = collect($orderItemIds)
            ->map(fn (int $id): int => (int) $id)
            ->unique()
            ->values();

        $selectedItems = OrderItem::query()
            ->where('order_id', $order->id)
            ->whereIn('id', $selectedIds)
            ->get();

        if ($selectedItems->isEmpty()) {
            throw new InvalidArgumentException('Please select at least one item to return.');
        }

        $returnRequest = DB::transaction(function () use ($order, $user, $reason, $selectedItems): ReturnRequest {
            $returnRequest = ReturnRequest::query()->create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'reason' => $reason,
                'status' => ReturnStatus::Pending,
                'refund_amount' => (float) $selectedItems->sum('subtotal'),
            ]);

            $selectedItems->each(function (OrderItem $item) use ($returnRequest): void {
                $returnRequest->items()->create([
                    'order_item_id' => $item->id,
                    'quantity' => (int) $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ]);
            });

            return $returnRequest->load('items.orderItem');
        });

        // Send vendor notifications
        $vendorIds = $selectedItems->pluck('vendor_id')->unique();
        $vendors = User::whereIn('id', $vendorIds)->get();

        foreach ($vendors as $vendor) {
            if ($vendor->email) {
                Mail::to($vendor->email)->send(
                    new VendorReturnNotificationMail($returnRequest, $vendor->name ?? 'Vendor')
                );
            }
        }

        return $returnRequest;
    }

    public function approve(ReturnRequest $returnRequest, ?string $adminNotes = null, ?float $refundAmount = null): ReturnRequest
    {
        $returnRequest = DB::transaction(function () use ($returnRequest, $adminNotes, $refundAmount): ReturnRequest {
            $returnRequest = ReturnRequest::query()->lockForUpdate()->findOrFail($returnRequest->id);

            if ($returnRequest->status === ReturnStatus::Refunded) {
                return $returnRequest;
            }

            $returnRequest->loadMissing([
                'order.items',
                'items.orderItem.product',
                'items.orderItem.variant',
            ]);

            $returnRequest->update([
                'status' => ReturnStatus::Approved,
                'admin_notes' => $adminNotes,
                'refund_amount' => $refundAmount ?? $returnRequest->refund_amount,
            ]);

            $order = $returnRequest->order;
            $returnedItems = $this->resolveReturnedItems($returnRequest);

            // Restore inventory using atomic increment to prevent race conditions
            foreach ($returnedItems as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
                if ($item->variant) {
                    $item->variant->increment('stock_quantity', $item->quantity);
                }
            }

            $fullOrderReturned = $returnedItems->count() === $order->items->count();

            if ($fullOrderReturned) {
                $order->update(['status' => OrderStatus::Refunded]);
                $this->paymentService->markRefunded($order);
            } else {
                $order->update([
                    'notes' => trim((string) $order->notes.' Partial refund approved for return request #'.$returnRequest->id.'.'),
                ]);
            }

            $returnRequest->update(['status' => ReturnStatus::Refunded]);

            return $returnRequest->fresh(['items.orderItem.product']) ?? $returnRequest;
        });

        // Send approval notification to customer
        if ($returnRequest->order?->user?->email) {
            Mail::to($returnRequest->order->user->email)->send(
                new ReturnStatusMail($returnRequest, 'Your return request has been approved and a refund of $'.number_format($returnRequest->refund_amount, 2).' will be processed.')
            );
        }

        return $returnRequest;
    }

    public function reject(ReturnRequest $returnRequest, ?string $adminNotes = null): ReturnRequest
    {
        $returnRequest->update([
            'status' => ReturnStatus::Rejected,
            'admin_notes' => $adminNotes,
        ]);

        // Send rejection notification to customer
        if ($returnRequest->order?->user?->email) {
            $returnRequest->load('order.user');
            Mail::to($returnRequest->order->user->email)->send(
                new ReturnStatusMail($returnRequest, 'Your return request has been rejected. Reason: '.($adminNotes ?? 'No reason provided.'))
            );
        }

        return $returnRequest;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    protected function resolveReturnedItems(ReturnRequest $returnRequest): Collection
    {
        if ($returnRequest->relationLoaded('items') && $returnRequest->items->isNotEmpty()) {
            return $returnRequest->items
                ->map(fn ($returnItem): ?OrderItem => $returnItem->orderItem)
                ->filter()
                ->values();
        }

        return $returnRequest->order->items;
    }
}
