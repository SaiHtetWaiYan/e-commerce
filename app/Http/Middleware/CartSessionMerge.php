<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use App\Models\CartItem;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CartSessionMerge
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $request->hasSession()) {
            $sessionId = $request->session()->getId();

            if (! empty($sessionId)) {
                $this->mergeSessionCartIntoUserCart($sessionId, (int) $user->id);
            }
        }

        return $next($request);
    }

    protected function mergeSessionCartIntoUserCart(string $sessionId, int $userId): void
    {
        $sessionCart = Cart::query()
            ->whereNull('user_id')
            ->where('session_id', $sessionId)
            ->with('items')
            ->first();

        if ($sessionCart === null || $sessionCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::query()->firstOrCreate(['user_id' => $userId], ['session_id' => $sessionId]);

        DB::transaction(function () use ($sessionCart, $userCart): void {
            foreach ($sessionCart->items as $item) {
                $userItem = CartItem::query()
                    ->where('cart_id', $userCart->id)
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->first();

                if ($userItem !== null) {
                    $userItem->increment('quantity', $item->quantity);

                    continue;
                }

                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);
            }

            $sessionCart->delete();
        });
    }
}
