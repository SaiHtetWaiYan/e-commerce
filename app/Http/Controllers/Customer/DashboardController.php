<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $recentOrders = Order::query()
            ->forUser((int) $user->id)
            ->with('items')
            ->latest()
            ->limit(5)
            ->get();

        $wishlistCount = Wishlist::query()->where('user_id', $user->id)->count();

        return view('customer.dashboard', [
            'recentOrders' => $recentOrders,
            'wishlistCount' => $wishlistCount,
        ]);
    }
}
