<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\VendorPayout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    public function calculateVendorEarnings(User $vendor, Carbon $periodStart, Carbon $periodEnd): array
    {
        $totalSales = (float) OrderItem::query()
            ->forVendor($vendor->id)
            ->whereHas('order', function ($q) use ($periodStart, $periodEnd): void {
                $q->where('status', OrderStatus::Delivered)
                    ->whereBetween('delivered_at', [$periodStart, $periodEnd]);
            })
            ->sum('subtotal');

        $commissionRate = $vendor->vendorProfile?->commission_rate ?? 10.0;
        $commission = round($totalSales * ($commissionRate / 100), 2);
        $netAmount = round($totalSales - $commission, 2);

        return [
            'total_sales' => $totalSales,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commission,
            'net_amount' => $netAmount,
        ];
    }

    public function createPayout(User $vendor, Carbon $periodStart, Carbon $periodEnd, string $paymentMethod = 'bank_transfer'): VendorPayout
    {
        return DB::transaction(function () use ($vendor, $periodStart, $periodEnd, $paymentMethod): VendorPayout {
            $earnings = $this->calculateVendorEarnings($vendor, $periodStart, $periodEnd);

            $payout = VendorPayout::query()->firstOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                ],
                [
                    'amount' => $earnings['total_sales'],
                    'commission_amount' => $earnings['commission_amount'],
                    'net_amount' => $earnings['net_amount'],
                    'status' => 'pending',
                    'payment_method' => $paymentMethod,
                ],
            );

            if (! $payout->wasRecentlyCreated) {
                return $payout;
            }

            $payout->histories()->create([
                'performed_by' => auth()->id(),
                'action' => 'created',
                'note' => 'Payout created.',
                'meta' => [
                    'total_sales' => $earnings['total_sales'],
                    'commission_rate' => $earnings['commission_rate'],
                ],
            ]);

            return $payout;
        });
    }

    public function markAsPaid(VendorPayout $payout, string $reference): VendorPayout
    {
        return DB::transaction(function () use ($payout, $reference): VendorPayout {
            $payout = VendorPayout::query()->lockForUpdate()->findOrFail($payout->id);

            if ($payout->status === 'paid') {
                return $payout;
            }

            $payout->update([
                'status' => 'paid',
                'payment_reference' => $reference,
                'paid_at' => now(),
            ]);

            $payout->histories()->create([
                'performed_by' => auth()->id(),
                'action' => 'paid',
                'note' => 'Payout marked as paid.',
                'meta' => [
                    'payment_reference' => $reference,
                ],
            ]);

            return $payout;
        });
    }
}
