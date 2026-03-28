<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeService $stripeService, PaymentService $paymentService): JsonResponse
    {
        $payload = (string) $request->getContent();
        $signature = (string) $request->header('Stripe-Signature', '');

        try {
            $event = $stripeService->constructWebhookEvent($payload, $signature);
        } catch (SignatureVerificationException) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        } catch (UnexpectedValueException) {
            return response()->json(['message' => 'Invalid payload.'], 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleSessionCompleted($event->data->object, $paymentService),
            'checkout.session.expired' => $this->handleSessionExpired($event->data->object, $paymentService),
            default => null,
        };

        return response()->json(['message' => 'Webhook handled.']);
    }

    protected function handleSessionCompleted(object $session, PaymentService $paymentService): void
    {
        $order = $this->resolveOrder($session);

        if ($order === null || $order->isPaid()) {
            return;
        }

        $paymentIntentId = $session->payment_intent ?? $session->id;
        $paymentService->markPaid($order, (string) $paymentIntentId);
    }

    protected function handleSessionExpired(object $session, PaymentService $paymentService): void
    {
        $order = $this->resolveOrder($session);

        if ($order === null || $order->isPaid()) {
            return;
        }

        $paymentService->markFailed($order, 'Stripe checkout session expired.');
    }

    protected function resolveOrder(object $session): ?Order
    {
        $orderId = $session->metadata->order_id ?? null;

        if ($orderId === null) {
            Log::warning('Stripe webhook: missing order_id in session metadata.');

            return null;
        }

        $order = Order::query()->find((int) $orderId);

        if ($order === null) {
            Log::warning('Stripe webhook: order not found.', ['order_id' => $orderId]);
        }

        return $order;
    }
}
