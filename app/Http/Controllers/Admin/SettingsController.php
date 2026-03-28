<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateMarketplaceSettingsRequest;
use App\Models\AppSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $settings = AppSetting::resolvedMarketplaceSettings();

        return view('admin.settings.edit', [
            'settings' => [
                'logo' => (string) ($settings['marketplace.logo'] ?? ''),
                'default_currency' => (string) ($settings['marketplace.default_currency'] ?? 'USD'),
                'default_shipping_fee' => (float) ($settings['marketplace.default_shipping_fee'] ?? 0),
                'free_shipping_threshold' => (float) ($settings['marketplace.free_shipping_threshold'] ?? 0),
                'default_tax_rate' => round((float) ($settings['marketplace.default_tax_rate'] ?? 0) * 100, 2),
                'vendor_default_commission_rate' => (float) ($settings['marketplace.vendor.default_commission_rate'] ?? 0),
                'default_carrier' => (string) ($settings['marketplace.default_carrier'] ?? ''),
                'order_number_prefix' => (string) ($settings['marketplace.order.number_prefix'] ?? ''),
                'tracking_prefix' => (string) ($settings['marketplace.tracking_prefix'] ?? ''),
                'vendor_require_approval' => (bool) ($settings['marketplace.vendor.require_approval'] ?? true),
            ],
        ]);
    }

    public function update(UpdateMarketplaceSettingsRequest $request): RedirectResponse
    {
        if (! AppSetting::marketplaceTableExists()) {
            return back()->with('status', 'Run php artisan migrate before saving marketplace settings.');
        }

        $validated = $request->validated();
        
        $payload = [
            'marketplace.default_currency' => strtoupper((string) $validated['default_currency']),
            'marketplace.default_shipping_fee' => round((float) $validated['default_shipping_fee'], 2),
            'marketplace.free_shipping_threshold' => round((float) $validated['free_shipping_threshold'], 2),
            'marketplace.default_tax_rate' => round((float) $validated['default_tax_rate'] / 100, 4),
            'marketplace.vendor.default_commission_rate' => round((float) $validated['vendor_default_commission_rate'], 2),
            'marketplace.default_carrier' => (string) $validated['default_carrier'],
            'marketplace.order.number_prefix' => strtoupper((string) $validated['order_number_prefix']),
            'marketplace.tracking_prefix' => strtoupper((string) $validated['tracking_prefix']),
            'marketplace.vendor.require_approval' => $request->boolean('vendor_require_approval'),
        ];

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            $payload['marketplace.logo'] = $path;
        }

        AppSetting::updateMarketplaceSettings($payload);

        return back()->with('status', 'Marketplace settings updated successfully.');
    }
}
