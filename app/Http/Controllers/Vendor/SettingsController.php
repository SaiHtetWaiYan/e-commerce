<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\UpdateStoreSettingsRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('vendor.settings', [
            'vendorProfile' => auth()->user()->vendorProfile,
        ]);
    }

    public function update(UpdateStoreSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();

        if ($request->hasFile('store_logo')) {
            $data['store_logo'] = $request->file('store_logo')->store('vendors/logos', 'public');
        } else {
            unset($data['store_logo']);
        }

        if ($request->hasFile('store_banner')) {
            $data['store_banner'] = $request->file('store_banner')->store('vendors/banners', 'public');
        } else {
            unset($data['store_banner']);
        }

        $profile = $user->vendorProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                ...$data,
                'store_slug' => Str::slug((string) ($data['store_name'] ?? '')).'-'.$user->id,
            ],
        );

        return back()->with('status', "Store settings updated for {$profile->store_name}.");
    }
}
