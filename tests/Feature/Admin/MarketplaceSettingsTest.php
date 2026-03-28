<?php

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('app setting factory creates marketplace override records', function () {
    $setting = AppSetting::factory()->create();

    expect(array_key_exists($setting->key, AppSetting::marketplaceFieldMeta()))->toBeTrue()
        ->and($setting->value)->not->toBe('');
});

test('admin can update marketplace settings', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        'default_currency' => 'eur',
        'default_shipping_fee' => 8.5,
        'free_shipping_threshold' => 120,
        'default_tax_rate' => 12.5,
        'vendor_default_commission_rate' => 18,
        'default_carrier' => 'FastShip',
        'order_number_prefix' => 'mk',
        'tracking_prefix' => 'trk',
    ]);

    $response->assertRedirect()
        ->assertSessionHas('status', 'Marketplace settings updated successfully.');

    $settings = AppSetting::resolvedMarketplaceSettings();

    expect($settings['marketplace.default_currency'])->toBe('EUR')
        ->and($settings['marketplace.default_shipping_fee'])->toBe(8.5)
        ->and($settings['marketplace.free_shipping_threshold'])->toBe(120.0)
        ->and($settings['marketplace.default_tax_rate'])->toBe(0.125)
        ->and($settings['marketplace.vendor.default_commission_rate'])->toBe(18.0)
        ->and($settings['marketplace.default_carrier'])->toBe('FastShip')
        ->and($settings['marketplace.order.number_prefix'])->toBe('MK')
        ->and($settings['marketplace.tracking_prefix'])->toBe('TRK')
        ->and($settings['marketplace.vendor.require_approval'])->toBeFalse();

    expect(AppSetting::query()->where('key', 'marketplace.vendor.require_approval')->value('value'))->toBe('0');

    $this->actingAs($admin)
        ->get(route('admin.settings.edit'))
        ->assertSuccessful()
        ->assertSee('Marketplace Settings')
        ->assertSee('EUR')
        ->assertSee('FastShip')
        ->assertSee('MK')
        ->assertSee('TRK');
});

test('admin settings page falls back to config defaults when app_settings table is missing', function () {
    $admin = User::factory()->admin()->create();

    Schema::dropIfExists('app_settings');
    AppSetting::clearMarketplaceCache();

    $this->actingAs($admin)
        ->get(route('admin.settings.edit'))
        ->assertSuccessful()
        ->assertSee('Marketplace Settings')
        ->assertSee((string) config('marketplace.default_currency'))
        ->assertSee((string) config('marketplace.default_carrier'));
});

test('marketplace settings update is skipped gracefully when app_settings table is missing', function () {
    $admin = User::factory()->admin()->create();

    Schema::dropIfExists('app_settings');
    AppSetting::clearMarketplaceCache();

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), [
            'default_currency' => 'eur',
            'default_shipping_fee' => 8.5,
            'free_shipping_threshold' => 120,
            'default_tax_rate' => 12.5,
            'vendor_default_commission_rate' => 18,
            'default_carrier' => 'FastShip',
            'order_number_prefix' => 'mk',
            'tracking_prefix' => 'trk',
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Run php artisan migrate before saving marketplace settings.');

    expect(Schema::hasTable('app_settings'))->toBeFalse();
});
