<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorPayout;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(): View
    {
        $payouts = VendorPayout::query()
            ->where('vendor_id', auth()->id())
            ->latest()
            ->paginate(15);

        $pendingTotal = (float) VendorPayout::query()
            ->where('vendor_id', auth()->id())
            ->where('status', 'pending')
            ->sum('net_amount');

        $paidTotal = (float) VendorPayout::query()
            ->where('vendor_id', auth()->id())
            ->where('status', 'paid')
            ->sum('net_amount');

        return view('vendor.payouts.index', compact('payouts', 'pendingTotal', 'paidTotal'));
    }
}
