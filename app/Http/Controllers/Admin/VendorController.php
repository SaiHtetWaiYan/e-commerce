<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

class VendorController extends Controller
{
    public function index(): View
    {
        $vendors = User::query()
            ->vendors()
            ->with('vendorProfile')
            ->latest()
            ->paginate(25);

        return view('admin.vendors.index', ['vendors' => $vendors]);
    }

    public function show(User $vendor): View
    {
        abort_unless($vendor->isVendor(), 404);

        $with = ['vendorProfile', 'products.images'];
        if (Schema::hasTable('campaigns') && Schema::hasTable('campaign_product')) {
            $with['products.campaigns'] = fn ($query) => $query->active()->orderByDesc('starts_at');
        }

        return view('admin.vendors.show', [
            'vendor' => $vendor->load($with),
        ]);
    }

    public function approve(User $vendor): RedirectResponse
    {
        abort_unless($vendor->isVendor(), 404);

        $vendor->vendorProfile()->updateOrCreate(
            ['user_id' => $vendor->id],
            [
                'store_name' => $vendor->vendorProfile?->store_name ?? $vendor->name.' Store',
                'store_slug' => $vendor->vendorProfile?->store_slug ?? 'vendor-'.$vendor->id,
                'is_verified' => true,
                'verified_at' => now(),
            ],
        );

        return back()->with('status', 'Vendor approved.');
    }

    public function reject(User $vendor): RedirectResponse
    {
        abort_unless($vendor->isVendor(), 404);

        if ($vendor->vendorProfile !== null) {
            $vendor->vendorProfile->update([
                'is_verified' => false,
                'verified_at' => null,
            ]);
        }

        return back()->with('status', 'Vendor marked as unverified.');
    }

    public function suspend(User $vendor): RedirectResponse
    {
        abort_unless($vendor->isVendor(), 404);

        $vendor->update(['status' => UserStatus::Suspended]);

        return back()->with('status', "Vendor \"{$vendor->name}\" has been suspended.");
    }

    public function unsuspend(User $vendor): RedirectResponse
    {
        abort_unless($vendor->isVendor(), 404);

        $vendor->update(['status' => UserStatus::Active]);

        return back()->with('status', "Vendor \"{$vendor->name}\" has been re-activated.");
    }
}
