<?php

use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\WishlistApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('cart')->middleware('throttle:api-cart')->name('cart.')->group(function (): void {
    Route::get('/', [CartApiController::class, 'index'])->name('index');
    Route::post('/add', [CartApiController::class, 'add'])->name('add');
    Route::patch('/items/{item}', [CartApiController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [CartApiController::class, 'destroy'])->name('items.destroy');
    Route::post('/coupon', [CartApiController::class, 'applyCoupon'])->name('coupon');
});

Route::middleware(['auth', 'throttle:api-wishlist'])->post('/wishlist/toggle/{product}', [WishlistApiController::class, 'toggle'])->name('wishlist.toggle');
Route::get('/search/suggest', [SearchApiController::class, 'suggest'])->middleware('throttle:api-search')->name('search.suggest');
Route::post('/payments/webhook', PaymentWebhookController::class)->middleware('throttle:payment-webhook')->name('payments.webhook');
Route::post('/stripe/webhook', StripeWebhookController::class)->middleware('throttle:payment-webhook')->name('stripe.webhook');
