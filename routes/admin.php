<?php

use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\ProductModerationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
    Route::patch('/vendors/{vendor}/approve', [VendorController::class, 'approve'])->name('vendors.approve');
    Route::patch('/vendors/{vendor}/reject', [VendorController::class, 'reject'])->name('vendors.reject');
    Route::patch('/vendors/{vendor}/suspend', [VendorController::class, 'suspend'])->name('vendors.suspend');
    Route::patch('/vendors/{vendor}/unsuspend', [VendorController::class, 'unsuspend'])->name('vendors.unsuspend');

    // Product Moderation
    Route::get('/products/review', [ProductModerationController::class, 'index'])->name('products.review.index');
    Route::get('/products/review/{product}', [ProductModerationController::class, 'show'])->name('products.review.show');
    Route::patch('/products/review/{product}/approve', [ProductModerationController::class, 'approve'])->name('products.review.approve');
    Route::patch('/products/review/{product}/reject', [ProductModerationController::class, 'reject'])->name('products.review.reject');
    Route::post('/products/review/bulk-approve', [ProductModerationController::class, 'bulkApprove'])->name('products.review.bulk-approve');
    Route::post('/products/review/bulk-reject', [ProductModerationController::class, 'bulkReject'])->name('products.review.bulk-reject');

    // Orders
    Route::get('/orders/export', [OrderController::class, 'exportCsv'])->name('orders.export');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/bulk-status', [OrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-status');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');

    Route::get('/brands', [\App\Http\Controllers\Admin\BrandController::class, 'index'])->name('brands.index');
    Route::post('/brands', [\App\Http\Controllers\Admin\BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [\App\Http\Controllers\Admin\BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [\App\Http\Controllers\Admin\BrandController::class, 'update'])->name('brands.update');

    // Shipments
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::patch('/shipments/{shipment}/assign', [ShipmentController::class, 'assign'])->name('shipments.assign');
    Route::patch('/shipments/{shipment}/retry', [ShipmentController::class, 'retry'])->name('shipments.retry');
    Route::patch('/shipments/{shipment}/eta', [ShipmentController::class, 'updateEta'])->name('shipments.eta');

    // Returns
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/{returnRequest}', [ReturnController::class, 'show'])->name('returns.show');
    Route::patch('/returns/{returnRequest}/approve', [ReturnController::class, 'approve'])->name('returns.approve');
    Route::patch('/returns/{returnRequest}/reject', [ReturnController::class, 'reject'])->name('returns.reject');
    Route::post('/returns/bulk-approve', [ReturnController::class, 'bulkApprove'])->name('returns.bulk-approve');
    Route::post('/returns/bulk-reject', [ReturnController::class, 'bulkReject'])->name('returns.bulk-reject');

    // Payouts
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::get('/payouts/create', [PayoutController::class, 'create'])->name('payouts.create');
    Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');
    Route::patch('/payouts/{payout}/pay', [PayoutController::class, 'markPaid'])->name('payouts.pay');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');

    // Banners
    Route::resource('banners', BannerController::class)->except(['show']);

    // Marketplace Settings
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Campaigns
    Route::resource('campaigns', CampaignController::class);
    Route::post('/campaigns/{campaign}/products', [CampaignController::class, 'addProducts'])->name('campaigns.add-products');
    Route::delete('/campaigns/{campaign}/products/{product}', [CampaignController::class, 'removeProduct'])->name('campaigns.remove-product');
    Route::patch('/campaigns/{campaign}/toggle', [CampaignController::class, 'toggle'])->name('campaigns.toggle');

    // Disputes
    Route::get('/disputes', [DisputeController::class, 'index'])->name('disputes.index');
    Route::get('/disputes/{dispute}', [DisputeController::class, 'show'])->name('disputes.show');
    Route::patch('/disputes/{dispute}/resolve', [DisputeController::class, 'resolve'])->name('disputes.resolve');

    // Account Settings
    Route::get('/account', [AccountSettingsController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountSettingsController::class, 'update'])->name('account.update');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.mark-read');
});
