<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey((string) config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Checkout Session for a given order.
     *
     * @throws ApiErrorException
     */
    public function createCheckoutSession(Order $order, string $successUrl, string $cancelUrl): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'customer_email' => $order->shipping_address['email'] ?? null,
            'line_items' => $this->buildLineItems($order),
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    /**
     * Issue a full refund for a Stripe PaymentIntent.
     *
     * @throws ApiErrorException
     */
    public function refund(string $paymentIntentId): Refund
    {
        return Refund::create([
            'payment_intent' => $paymentIntentId,
        ]);
    }

    /**
     * Construct and verify a Stripe webhook event from the raw payload.
     *
     * @throws SignatureVerificationException
     * @throws UnexpectedValueException
     */
    public function constructWebhookEvent(string $payload, string $signature): \Stripe\Event
    {
        return Webhook::constructEvent(
            $payload,
            $signature,
            (string) config('services.stripe.webhook_secret')
        );
    }

    /**
     * Build Stripe line_items array from order items.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildLineItems(Order $order): array
    {
        $order->loadMissing('items');

        $currency = strtolower((string) config('marketplace.default_currency', 'usd'));

        // When a discount is applied, use a single summary line item
        // because Stripe Checkout does not support negative line items.
        if ((float) $order->discount_amount > 0) {
            return [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => 'Order '.$order->order_number,
                        'description' => $order->items->count().' item(s) with discount applied',
                    ],
                    'unit_amount' => (int) round((float) $order->total * 100),
                ],
                'quantity' => 1,
            ]];
        }

        $lineItems = [];

        foreach ($order->items as $item) {
            $name = $item->product_name.($item->variant_name ? ' - '.$item->variant_name : '');

            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $name,
                    ],
                    'unit_amount' => (int) round((float) $item->unit_price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        if ((float) $order->shipping_fee > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'Shipping'],
                    'unit_amount' => (int) round((float) $order->shipping_fee * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ((float) $order->tax_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'Tax'],
                    'unit_amount' => (int) round((float) $order->tax_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        return $lineItems;
    }
}
