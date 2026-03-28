<?php

use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\Vendor\CampaignController;
use App\Http\Controllers\Vendor\ConversationController;
use App\Http\Controllers\Vendor\CouponController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\InventoryController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\PayoutController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ReportController;
use App\Http\Controllers\Vendor\ReturnController;
use App\Http\Controllers\Vendor\SettingsController;
use App\Http\Controllers\Vendor\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:vendor', 'vendor.approved'])->prefix('vendor')->name('vendor.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/bulk-status', [ProductController::class, 'bulkUpdateStatus'])->name('products.bulk-status');

    Route::get('/orders/export', [OrderController::class, 'exportCsv'])->name('orders.export');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    // Returns
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/{returnRequest}', [ReturnController::class, 'show'])->name('returns.show');
    Route::patch('/returns/{returnRequest}/approve', [ReturnController::class, 'approve'])->name('returns.approve');
    Route::patch('/returns/{returnRequest}/reject', [ReturnController::class, 'reject'])->name('returns.reject');

    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [CouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');

    // Campaigns
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::post('/campaigns/{campaign}/enroll', [CampaignController::class, 'enroll'])->name('campaigns.enroll');
    Route::delete('/campaigns/{campaign}/products/{product}', [CampaignController::class, 'withdraw'])->name('campaigns.withdraw');

    // Payouts
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');

    // Conversations
    Route::get('/messages', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/messages/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/messages/{conversation}', [ConversationController::class, 'reply'])->name('conversations.reply');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::patch('/inventory/{product}/stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');

    // Shipments
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Account Settings
    Route::get('/account', [AccountSettingsController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountSettingsController::class, 'update'])->name('account.update');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Vendor\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\Vendor\NotificationController::class, 'markAllRead'])->name('notifications.mark-read');
});
