<?php

namespace Tests\Feature\Customer;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\User;
use App\Notifications\Vendor\NewReviewNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

test('customer can view their reviews', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'product_id' => $product->id,
        'order_id' => Order::factory()->create(['user_id' => $user->id])->id,
    ]);

    Review::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'order_item_id' => $orderItem->id,
        'rating' => 4,
        'comment' => 'Great product!',
        'is_verified_purchase' => true,
        'is_approved' => true,
    ]);

    $response = $this->actingAs($user)->get('/customer/reviews');

    $response->assertStatus(200);
    $response->assertSee('Great product!');
});

test('customer can submit a review for a purchased product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);
    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'vendor_id' => $product->vendor_id,
        'product_name' => $product->name,
    ]);

    $response = $this->actingAs($user)->post('/customer/reviews', [
        'order_item_id' => $orderItem->id,
        'product_id' => $product->id,
        'rating' => 5,
        'comment' => 'Excellent purchase',
    ]);

    $response->assertSessionHas('status', 'Review submitted.');
    expect(Review::where('user_id', $user->id)->where('product_id', $product->id)->exists())->toBeTrue();
});

test('customer cannot review a product they did not purchase', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $otherUser->id]);
    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'vendor_id' => $product->vendor_id,
        'product_name' => $product->name,
    ]);

    $response = $this->actingAs($user)->post('/customer/reviews', [
        'order_item_id' => $orderItem->id,
        'product_id' => $product->id,
        'rating' => 5,
        'comment' => 'Trying to fake a review',
    ]);

    $response->assertStatus(403);
});

test('customer can update their review', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'order_id' => Order::factory()->create(['user_id' => $user->id])->id,
        'product_id' => $product->id,
    ]);

    $review = Review::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'order_item_id' => $orderItem->id,
        'rating' => 3,
        'comment' => 'Okay product.',
        'is_verified_purchase' => true,
        'is_approved' => true,
    ]);

    $response = $this->actingAs($user)->put("/customer/reviews/{$review->id}", [
        'rating' => 5,
        'comment' => 'Actually, it is awesome!',
    ]);

    $response->assertSessionHas('status', 'Review updated successfully.');
    expect($review->fresh()->rating)->toBe(5)
        ->and($review->fresh()->comment)->toBe('Actually, it is awesome!');
});

test('customer can submit a review with photos and videos', function () {
    Storage::fake('public');
    Notification::fake();

    $user = User::factory()->create();
    $vendor = User::factory()->vendor()->create();
    $product = Product::factory()->create([
        'vendor_id' => $vendor->id,
    ]);
    $order = Order::factory()->create(['user_id' => $user->id]);
    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'vendor_id' => $vendor->id,
        'product_name' => $product->name,
    ]);

    $response = $this->actingAs($user)->post('/customer/reviews', [
        'order_item_id' => $orderItem->id,
        'product_id' => $product->id,
        'rating' => 5,
        'comment' => 'Excellent with proof',
        'media' => [
            UploadedFile::fake()->image('review-photo.jpg'),
            UploadedFile::fake()->create('review-video.mp4', 2048, 'video/mp4'),
        ],
    ]);

    $response->assertRedirect()
        ->assertSessionHas('status', 'Review submitted.');

    $review = Review::query()->where('user_id', $user->id)->latest()->first();

    expect($review)->not->toBeNull();
    expect($review->reviewImages()->count())->toBe(2)
        ->and($review->reviewImages()->pluck('media_type')->all())->toBe(['image', 'video']);

    foreach ($review->reviewImages as $reviewImage) {
        Storage::disk('public')->assertExists($reviewImage->file_path);
    }

    Notification::assertSentTo($vendor, NewReviewNotification::class);
});

test('customer can replace review media when updating a review', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $product = Product::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'order_id' => Order::factory()->create(['user_id' => $user->id])->id,
        'product_id' => $product->id,
    ]);

    $review = Review::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'order_item_id' => $orderItem->id,
        'rating' => 4,
        'comment' => 'Original review',
        'is_verified_purchase' => true,
        'is_approved' => true,
    ]);

    Storage::disk('public')->put('reviews/original-review.jpg', 'original-review');

    ReviewImage::factory()->create([
        'review_id' => $review->id,
        'file_path' => 'reviews/original-review.jpg',
        'media_type' => 'image',
    ]);

    $response = $this->actingAs($user)->put("/customer/reviews/{$review->id}", [
        'rating' => 5,
        'comment' => 'Updated review',
        'media' => [
            UploadedFile::fake()->image('replacement-review.jpg'),
        ],
    ]);

    $response->assertRedirect()
        ->assertSessionHas('status', 'Review updated successfully.');

    $review->refresh()->load('reviewImages');

    expect($review->rating)->toBe(5)
        ->and($review->comment)->toBe('Updated review')
        ->and($review->reviewImages)->toHaveCount(1);

    Storage::disk('public')->assertMissing('reviews/original-review.jpg');
    Storage::disk('public')->assertExists($review->reviewImages->first()->file_path);
});

test('customer cannot update someone elses review', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'order_id' => Order::factory()->create(['user_id' => $otherUser->id])->id,
        'product_id' => $product->id,
    ]);

    $review = Review::query()->create([
        'user_id' => $otherUser->id,
        'product_id' => $product->id,
        'order_item_id' => $orderItem->id,
        'rating' => 3,
        'comment' => 'Okay product.',
        'is_verified_purchase' => true,
        'is_approved' => true,
    ]);

    $response = $this->actingAs($user)->put("/customer/reviews/{$review->id}", [
        'rating' => 1,
        'comment' => 'Hacking',
    ]);

    $response->assertStatus(403);
});

test('customer can delete their review', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'order_id' => Order::factory()->create(['user_id' => $user->id])->id,
        'product_id' => $product->id,
    ]);

    $review = Review::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'order_item_id' => $orderItem->id,
        'rating' => 4,
        'is_verified_purchase' => true,
        'is_approved' => true,
    ]);

    $response = $this->actingAs($user)->delete("/customer/reviews/{$review->id}");

    $response->assertSessionHas('status', 'Review deleted successfully.');
    expect(Review::where('id', $review->id)->exists())->toBeFalse();
});
