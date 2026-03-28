<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, PaymentService $paymentService): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'string', 'max:100'],
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'status' => ['required', 'string', 'in:paid,failed'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $secret = (string) config('services.payments.webhook_secret');

        if ($secret !== '') {
            $signature = (string) $request->header('X-Payment-Signature', '');
            $expected = hash_hmac('sha256', (string) $request->getContent(), $secret);

            if ($signature === '' || ! hash_equals($expected, $signature)) {
                return response()->json(['message' => 'Invalid webhook signature.'], 401);
            }
        }

        $cacheKey = 'payments:webhook:event:'.$validated['event_id'];

        if (! Cache::add($cacheKey, true, now()->addDay())) {
            return response()->json(['message' => 'Event already processed.'], 202);
        }

        $order = Order::query()->findOrFail((int) $validated['order_id']);
        $reference = $validated['payment_reference'] ?? null;

        if ($validated['status'] === 'paid') {
            if ($order->payment_status !== PaymentStatus::Paid) {
                $paymentService->markPaid($order, $reference);
            }
        } else {
            $paymentService->markFailed($order, $validated['reason'] ?? null);
        }

        $freshOrder = $order->fresh();

        return response()->json([
            'message' => 'Payment webhook processed.',
            'order_id' => $order->id,
            'payment_status' => $freshOrder?->payment_status?->value,
        ]);
    }
}
