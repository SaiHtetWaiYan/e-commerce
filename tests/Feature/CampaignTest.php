<?php

use App\Enums\CampaignDiscountType;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\User;

it('allows admin to create update and delete campaigns', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.campaigns.create'))
        ->assertSuccessful();

    $storeResponse = $this->actingAs($admin)->post(route('admin.campaigns.store'), [
        'name' => '3.3 Mega Sale',
        'slug' => '3-3-mega-sale',
        'discount_type' => CampaignDiscountType::Percentage->value,
        'discount_value' => 30,
        'max_discount_amount' => 25,
        'starts_at' => now()->subHour()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'is_active' => 1,
    ]);

    $campaign = Campaign::query()->first();

    expect($campaign)->not->toBeNull();

    $this->actingAs($admin)
        ->get(route('admin.campaigns.show', $campaign))
        ->assertSuccessful();

    $storeResponse
        ->assertRedirect(route('admin.campaigns.show', $campaign));

    $this->assertDatabaseHas('campaigns', [
        'id' => $campaign->id,
        'name' => '3.3 Mega Sale',
        'slug' => '3-3-mega-sale',
    ]);

    $updateResponse = $this->actingAs($admin)->put(route('admin.campaigns.update', $campaign), [
        'name' => '3.3 Mega Sale Updated',
        'slug' => '3-3-mega-sale-updated',
        'discount_type' => CampaignDiscountType::Fixed->value,
        'discount_value' => 10,
        'starts_at' => now()->subHour()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addDays(4)->format('Y-m-d H:i:s'),
        'is_active' => 1,
    ]);

    $updateResponse
        ->assertRedirect(route('admin.campaigns.show', $campaign));

    expect($campaign->fresh()->name)->toBe('3.3 Mega Sale Updated')
        ->and($campaign->fresh()->discount_type)->toBe(CampaignDiscountType::Fixed);

    $deleteResponse = $this->actingAs($admin)->delete(route('admin.campaigns.destroy', $campaign));

    $deleteResponse
        ->assertRedirect(route('admin.campaigns.index'));

    $this->assertDatabaseMissing('campaigns', [
        'id' => $campaign->id,
    ]);
});

it('calculates campaign prices for percentage fixed and custom discounts', function () {
    $product = Product::factory()->create([
        'base_price' => 100,
    ]);

    $percentageCampaign = Campaign::factory()->active()->percentage(30)->create();
    expect($percentageCampaign->getCampaignPriceForProduct($product))->toBe(70.0);

    $fixedCampaign = Campaign::factory()->active()->fixed(12)->create();
    expect($fixedCampaign->getCampaignPriceForProduct($product))->toBe(88.0);

    $customCampaign = Campaign::factory()->active()->custom()->create();
    $product->campaigns()->attach($customCampaign->id, [
        'custom_price' => 55,
        'sort_order' => 1,
    ]);

    $product = $product->fresh()->load('campaigns');
    expect($product->getActiveCampaignPrice())->toBe(55.0)
        ->and($product->getEffectivePrice())->toBe(55.0);
});

it('shows enrolled products with campaign prices on landing page', function () {
    $campaign = Campaign::factory()->active()->percentage(25)->create([
        'name' => '9.9 Super Deals',
        'slug' => '9-9-super-deals',
        'max_discount_amount' => null,
    ]);

    $product = Product::factory()->create([
        'name' => 'Gaming Mouse',
        'base_price' => 100,
    ]);

    $campaign->products()->attach($product->id, [
        'sort_order' => 1,
    ]);

    $this->get(route('storefront.campaigns.show', $campaign->slug))
        ->assertSuccessful()
        ->assertSeeText('9.9 Super Deals')
        ->assertSeeText('Gaming Mouse')
        ->assertSee('$75.00', false);
});

it('shows campaign badge on product cards when product is enrolled in an active campaign', function () {
    $campaign = Campaign::factory()->active()->create([
        'name' => 'Badge Campaign',
        'badge_text' => 'MEGA',
        'badge_color' => '#ef4444',
    ]);

    $product = Product::factory()->create([
        'name' => 'Bluetooth Speaker',
        'base_price' => 120,
    ]);

    $campaign->products()->attach($product->id, [
        'custom_discount_percentage' => 10,
    ]);

    $this->get(route('storefront.products.index'))
        ->assertSuccessful()
        ->assertSeeText('MEGA')
        ->assertSee('$108.00', false);
});

it('sanitizes stored campaign badge colors before rendering storefront styles', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $campaign = Campaign::factory()->active()->create([
        'badge_text' => 'FLASH',
        'badge_color' => 'expression(1)',
    ]);

    $product = Product::factory()->create([
        'vendor_id' => $vendor->id,
    ]);

    $campaign->products()->attach($product->id, [
        'sort_order' => 1,
    ]);

    $this->get(route('storefront.products.show', $product->slug))
        ->assertSuccessful()
        ->assertSee('background-color: #f97316;', false)
        ->assertDontSee('expression(1)', false);
});
