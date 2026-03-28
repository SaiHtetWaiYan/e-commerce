<?php

use App\Models\Banner;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can create a banner with an uploaded image', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
        'title' => 'Mega Flash Sale',
        'image' => UploadedFile::fake()->image('hero-banner.jpg', 1600, 600),
        'link' => 'https://example.test/campaigns/mega-flash-sale',
        'position' => 'hero',
        'sort_order' => 3,
        'is_active' => '1',
    ]);

    $banner = Banner::query()->first();

    expect($banner)->not->toBeNull();

    $response->assertRedirect(route('admin.banners.edit', $banner))
        ->assertSessionHas('status', 'Banner created successfully.');

    Storage::disk('public')->assertExists($banner->image);

    expect($banner->title)->toBe('Mega Flash Sale')
        ->and($banner->position)->toBe('hero')
        ->and($banner->sort_order)->toBe(3)
        ->and($banner->is_active)->toBeTrue();
});

test('admin can update and delete a banner', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    Storage::disk('public')->put('banners/original-banner.jpg', 'original-banner');

    $banner = Banner::factory()->create([
        'title' => 'Original Banner',
        'image' => 'banners/original-banner.jpg',
        'position' => 'hero',
        'sort_order' => 1,
    ]);

    $updateResponse = $this->actingAs($admin)->put(route('admin.banners.update', $banner), [
        'title' => 'Updated Banner',
        'image' => UploadedFile::fake()->image('updated-banner.jpg', 1200, 500),
        'link' => 'https://example.test/campaigns/updated',
        'position' => 'sidebar',
        'sort_order' => 9,
    ]);

    $banner->refresh();
    $updatedImagePath = $banner->image;

    $updateResponse->assertRedirect()
        ->assertSessionHas('status', 'Banner updated successfully.');

    Storage::disk('public')->assertMissing('banners/original-banner.jpg');
    Storage::disk('public')->assertExists($updatedImagePath);

    expect($banner->title)->toBe('Updated Banner')
        ->and($banner->position)->toBe('sidebar')
        ->and($banner->sort_order)->toBe(9)
        ->and($banner->link)->toBe('https://example.test/campaigns/updated')
        ->and($banner->is_active)->toBeFalse();

    $deleteResponse = $this->actingAs($admin)->delete(route('admin.banners.destroy', $banner));

    $deleteResponse->assertRedirect(route('admin.banners.index'))
        ->assertSessionHas('status', 'Banner deleted successfully.');

    expect(Banner::query()->find($banner->id))->toBeNull();
    Storage::disk('public')->assertMissing($updatedImagePath);
});
