<?php

namespace App\Http\Controllers\Storefront;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\StripeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;
use Stripe\Exception\ApiErrorException;

class CheckoutController extends Controller
{
    public function __construct(
        public CartService $cartService,
        public OrderService $orderService,
        public StripeService $stripeService,
    ) {}

    public function index(): View|RedirectResponse
    {
        $cart = $this->cartService->resolveCart(auth()->user(), session()->getId());

        if ($cart->items->isEmpty()) {
            return redirect()->route('storefront.cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $totals = $this->cartService->calculateTotals($cart);
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->latest()->get();

        return view('storefront.checkout.index', [
            'cart' => $cart,
            'totals' => $totals,
            'addresses' => $addresses,
            'shippingMethods' => config('marketplace.shipping_methods', []),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = $this->cartService->resolveCart($request->user(), session()->getId());

        try {
            $order = $this->orderService->placeOrder($request->user(), $cart, $request->validatedForOrder());
        } catch (InsufficientStockException $exception) {
            return back()->withErrors(['checkout' => $exception->getMessage()])->withInput();
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['checkout' => $exception->getMessage()]);
        }

        if ($order->payment_method === 'card') {
            return $this->redirectToStripe($order);
        }

        return redirect()->route('storefront.checkout.success', $order);
    }

    public function success(Order $order): View
    {
        $user = auth()->user();

        if ($user !== null && ! $user->isAdmin() && (int) $order->user_id !== (int) $user->id) {
            abort(403);
        }

        return view('storefront.checkout.success', [
            'order' => $order->load(['items', 'shipment']),
        ]);
    }

    protected function redirectToStripe(Order $order): RedirectResponse
    {
        try {
            $session = $this->stripeService->createCheckoutSession(
                $order,
                route('storefront.checkout.success', $order),
                route('storefront.checkout.index'),
            );

            $order->update(['payment_reference' => $session->id]);

            return redirect()->away($session->url);
        } catch (ApiErrorException $e) {
            report($e);

            return redirect()->route('storefront.checkout.success', $order)
                ->with('stripe_error', 'Payment processing is temporarily unavailable. Your order has been placed and you can retry payment later.');
        }
    }
}
