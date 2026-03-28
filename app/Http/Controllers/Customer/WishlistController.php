<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class WishlistController extends Controller
{
    public function index(): View
    {
        $with = ['product.images', 'product.vendor.vendorProfile'];
        if (Schema::hasTable('campaigns') && Schema::hasTable('campaign_product')) {
            $with['product.campaigns'] = fn ($query) => $query->active()->orderByDesc('starts_at');
        }

        $wishlistItems = Wishlist::query()
            ->where('user_id', auth()->id())
            ->with($with)
            ->latest()
            ->paginate(20);

        return view('customer.wishlist', [
            'wishlistItems' => $wishlistItems,
        ]);
    }
}
