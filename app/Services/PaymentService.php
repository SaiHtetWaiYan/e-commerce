<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;

class PaymentService
{
    public function initializePayment(?string $paymentMethod = null): array
    {
        $method = $this->normalizePaymentMethod($paymentMethod);

        return match ($method) {
            'cod' => [
                'status' => PaymentStatus::Pending,
                'reference' => null,
            ],
            'card' => [
                'status' => PaymentStatus::Pending,
                'reference' => $this->generateReference('CARD'),
            ],
            'transfer' => [
                'status' => PaymentStatus::Pending,
                'reference' => $this->generateReference('TRF'),
            ],
            'paypal' => [
                'status' => PaymentStatus::Pending,
                'reference' => $this->generateReference('PPL'),
            ],
            default => [
                'status' => PaymentStatus::Pending,
                'reference' => null,
            ],
        };
    }

    public function markPaid(Order $order, ?string $reference = null): Order
    {
        $order->forceFill([
            'payment_status' => PaymentStatus::Paid,
            'payment_reference' => $reference ?? $order->payment_reference,
            'paid_at' => now(),
        ])->save();

        return $order;
    }

    public function markFailed(Order $order, ?string $reason = null): Order
    {
        $order->forceFill([
            'payment_status' => PaymentStatus::Failed,
            'notes' => trim((string) ($order->notes.' '.($reason ?? ''))),
        ])->save();

        return $order;
    }

    public function markRefunded(Order $order): Order
    {
        if ($order->payment_method === 'card' && $order->payment_reference !== null && str_starts_with($order->payment_reference, 'pi_')) {
            try {
                app(StripeService::class)->refund($order->payment_reference);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                report($e);
            }
        }

        $order->forceFill([
            'payment_status' => PaymentStatus::Refunded,
        ])->save();

        return $order;
    }

    public function confirmCashOnDelivery(Order $order): Order
    {
        if ($order->payment_status === PaymentStatus::Paid) {
            return $order;
        }

        return $this->markPaid(
            $order,
            $order->payment_reference ?: $this->generateReference('COD')
        );
    }

    public function verifyBankTransfer(Order $order, string $reference): Order
    {
        return $this->markPaid($order, $reference);
    }

    public function normalizePaymentMethod(?string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'credit_card' => 'card',
            'cash_on_delivery' => 'cod',
            'bank_transfer' => 'transfer',
            null, '' => 'cod',
            default => (string) $paymentMethod,
        };
    }

    protected function generateReference(string $prefix): string
    {
        return strtoupper($prefix.'-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT));
    }
}
