<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;

class WishlistApiController extends Controller
{
    public function toggle(Product $product): JsonResponse
    {
        $user = request()->user();

        if ($user === null) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $existing = Wishlist::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing instanceof Wishlist) {
            $existing->delete();

            return response()->json([
                'message' => 'Removed from wishlist.',
                'status' => 'removed',
                'wishlisted' => false,
            ]);
        }

        Wishlist::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return response()->json([
            'message' => 'Added to wishlist.',
            'status' => 'added',
            'wishlisted' => true,
        ]);
    }
}
