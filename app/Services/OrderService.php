<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Events\OrderPlaced;
use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        public CartService $cartService,
        public PaymentService $paymentService,
    ) {}

    public function placeOrder(User $user, Cart $cart, array $checkoutData): Order
    {
        $cart->load(['items.product.images', 'items.variant', 'coupon']);

        if ($cart->items->isEmpty()) {
            throw new InvalidArgumentException('Cannot place an order with an empty cart.');
        }

        return DB::transaction(function () use ($user, $cart, $checkoutData): Order {
            // Validate and lock stock before proceeding
            foreach ($cart->items as $item) {
                $product = Product::query()->lockForUpdate()->find($item->product_id);

                if ($product === null) {
                    throw new InsufficientStockException("Product \"{$item->product->name}\" is no longer available.");
                }

                $availableStock = $product->stock_quantity;

                if ($item->variant_id !== null) {
                    $variant = ProductVariant::query()->lockForUpdate()->find($item->variant_id);
                    $availableStock = $variant?->stock_quantity ?? 0;
                }

                if ($availableStock < $item->quantity) {
                    throw new InsufficientStockException(
                        "Insufficient stock for \"{$product->name}\". Only {$availableStock} available, but {$item->quantity} requested."
                    );
                }
            }

            $totals = $this->cartService->calculateTotals($cart);
            $normalizedPaymentMethod = $this->paymentService->normalizePaymentMethod((string) ($checkoutData['payment_method'] ?? 'cod'));
            $paymentInit = $this->paymentService->initializePayment($normalizedPaymentMethod);

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'coupon_id' => $cart->coupon_id,
                'subtotal' => $totals['subtotal'],
                'discount_amount' => $totals['discount_amount'],
                'shipping_fee' => $totals['shipping_fee'],
                'tax_amount' => $totals['tax_amount'],
                'total' => $totals['total'],
                'status' => OrderStatus::Pending,
                'payment_status' => $paymentInit['status'] ?? PaymentStatus::Pending,
                'payment_method' => $normalizedPaymentMethod,
                'payment_reference' => $paymentInit['reference'] ?? null,
                'shipping_address' => $checkoutData['shipping_address'],
                'billing_address' => $checkoutData['billing_address'] ?? null,
                'notes' => $checkoutData['notes'] ?? null,
                'paid_at' => ($paymentInit['status'] ?? PaymentStatus::Pending) === PaymentStatus::Paid ? now() : null,
            ]);

            foreach ($cart->items as $item) {
                $this->createOrderItemFromCartItem($order, $item);
            }

            $order->statusHistories()->create([
                'status' => $order->status->value,
                'comment' => 'Order placed by customer.',
                'changed_by' => $user->id,
                'created_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                $this->decrementInventory($item);
            }

            $requiresShipment = $cart->items->contains(fn (CartItem $item): bool => ! (bool) ($item->product?->is_digital ?? false));

            if ($requiresShipment) {
                $this->createInitialShipment($order, (string) ($checkoutData['shipping_method'] ?? 'standard'));
            }

            if ($cart->coupon !== null) {
                $cart->coupon()->increment('used_count');
            }

            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);

            $order = $order->fresh(['items.product', 'items.vendor', 'shipment']) ?? $order;

            OrderPlaced::dispatch($order);

            return $order;
        });
    }

    public function cancelOrder(Order $order, ?string $reason = null): Order
    {
        if (! $order->canBeCancelled()) {
            throw new InvalidArgumentException('This order cannot be cancelled.');
        }

        return DB::transaction(function () use ($order, $reason): Order {
            // Restore inventory
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                    $item->product->decrement('total_sold', $item->quantity);
                }
                if ($item->variant) {
                    $item->variant->increment('stock_quantity', $item->quantity);
                }
            }

            // Refund if already paid
            if ($order->isPaid()) {
                $this->paymentService->markRefunded($order);
            }

            $order->update(['status' => OrderStatus::Cancelled]);

            $order->statusHistories()->create([
                'status' => OrderStatus::Cancelled->value,
                'comment' => $reason ? 'Cancelled by customer: '.$reason : 'Cancelled by customer.',
                'changed_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return $order->fresh() ?? $order;
        });
    }

    public function synchronizeStatusFromItems(Order $order): Order
    {
        $order->loadMissing('items');
        $statuses = $order->items->pluck('status')->filter();

        if ($statuses->isEmpty()) {
            return $order;
        }

        $allMatch = fn (string $status): bool => $statuses->every(fn (string $itemStatus): bool => $itemStatus === $status);

        $resolvedStatus = match (true) {
            $allMatch(OrderStatus::Cancelled->value) => OrderStatus::Cancelled,
            $allMatch(OrderStatus::Refunded->value) => OrderStatus::Refunded,
            $allMatch(OrderStatus::Delivered->value) => OrderStatus::Delivered,
            $statuses->contains(OrderStatus::Shipped->value) => OrderStatus::Shipped,
            $statuses->contains(OrderStatus::Processing->value) => OrderStatus::Processing,
            $statuses->contains(OrderStatus::Confirmed->value) => OrderStatus::Confirmed,
            default => OrderStatus::Pending,
        };

        $order->forceFill([
            'status' => $resolvedStatus,
            'shipped_at' => $resolvedStatus === OrderStatus::Shipped && $order->shipped_at === null ? now() : $order->shipped_at,
            'delivered_at' => $resolvedStatus === OrderStatus::Delivered && $order->delivered_at === null ? now() : $order->delivered_at,
        ])->save();

        return $order->fresh(['items']) ?? $order;
    }

    protected function createOrderItemFromCartItem(Order $order, CartItem $item): void
    {
        $product = $item->product;

        if ($product === null) {
            throw new InvalidArgumentException('Cart item is missing product information.');
        }

        $order->items()->create([
            'product_id' => $product->id,
            'variant_id' => $item->variant_id,
            'vendor_id' => $product->vendor_id,
            'product_name' => $product->name,
            'variant_name' => $item->variant?->name,
            'product_image' => $product->primary_image,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'subtotal' => (float) $item->unit_price * $item->quantity,
            'status' => OrderStatus::Pending->value,
        ]);
    }

    protected function decrementInventory(CartItem $item): void
    {
        if ($item->variant !== null) {
            $item->variant->decrement('stock_quantity', $item->quantity);
        }

        if ($item->product !== null) {
            $item->product->decrement('stock_quantity', $item->quantity);
            $item->product->increment('total_sold', $item->quantity);
        }
    }

    protected function generateOrderNumber(): string
    {
        $prefix = (string) config('marketplace.order.number_prefix', 'ORD');

        return $prefix.'-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    protected function createInitialShipment(Order $order, string $shippingMethod): void
    {
        $shippingMethodDays = (string) data_get(config('marketplace.shipping_methods', []), "{$shippingMethod}.days", '5');
        preg_match('/\d+/', $shippingMethodDays, $matches);
        $leadDays = isset($matches[0]) ? (int) $matches[0] : 5;

        $order->shipment()->create([
            'status' => ShipmentStatus::Pending,
            'tracking_number' => app(ShipmentService::class)->generateTrackingNumber(),
            'carrier_name' => config('marketplace.default_carrier', 'Marketplace Express'),
            'estimated_delivery_date' => now()->addDays(max(1, $leadDays))->toDateString(),
        ]);
    }
}
