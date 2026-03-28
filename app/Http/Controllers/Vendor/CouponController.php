<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\StoreCouponRequest;
use App\Http\Requests\Vendor\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::query()
            ->where('vendor_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('vendor.coupons.index', ['coupons' => $coupons]);
    }

    public function create(): View
    {
        return view('vendor.coupons.create');
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        Coupon::query()->create([
            ...$request->validated(),
            'vendor_id' => $request->user()->id,
            'code' => strtoupper((string) $request->validated('code')),
        ]);

        return redirect()->route('vendor.coupons.index')->with('status', 'Coupon created.');
    }

    public function edit(Coupon $coupon): View
    {
        abort_unless((int) $coupon->vendor_id === (int) auth()->id(), 403);

        return view('vendor.coupons.edit', ['coupon' => $coupon]);
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        abort_unless((int) $coupon->vendor_id === (int) auth()->id(), 403);

        $coupon->update([
            ...$request->validated(),
            'code' => strtoupper((string) $request->validated('code')),
        ]);

        return redirect()->route('vendor.coupons.index')->with('status', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        abort_unless((int) $coupon->vendor_id === (int) auth()->id(), 403);

        $coupon->delete();

        return back()->with('status', 'Coupon deleted.');
    }
}
