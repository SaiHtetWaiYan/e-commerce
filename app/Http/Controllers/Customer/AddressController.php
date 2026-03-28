<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Http\Requests\Customer\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = Address::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('customer.addresses.index', ['addresses' => $addresses]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (($validated['is_default'] ?? false) === true) {
            Address::query()->where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $request->user()->addresses()->create($validated);

        return back()->with('status', 'Address added.');
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $validated = $request->validated();

        if (($validated['is_default'] ?? false) === true) {
            Address::query()->where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return back()->with('status', 'Address updated.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        abort_unless((int) $address->user_id === (int) auth()->id(), 403);

        $address->delete();

        return back()->with('status', 'Address removed.');
    }
}
