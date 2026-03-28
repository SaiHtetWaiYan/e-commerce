<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class AccountSettingsController extends Controller
{
    public function edit(): View
    {
        $layout = $this->resolveLayout();

        return view('account.settings', [
            'user' => auth()->user(),
            'layout' => $layout,
        ]);
    }

    public function update(UpdateAccountRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->validated('password'));
        }

        $request->user()->update($data);

        return back()->with('status', 'Account updated successfully.');
    }

    private function resolveLayout(): string
    {
        if (request()->routeIs('admin.*')) {
            return 'admin';
        }

        if (request()->routeIs('vendor.*')) {
            return 'vendor';
        }

        if (request()->routeIs('delivery.*')) {
            return 'delivery';
        }

        return 'admin';
    }
}
