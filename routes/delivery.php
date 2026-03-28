<?php

use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\Delivery\DashboardController;
use App\Http\Controllers\Delivery\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:delivery_agent'])->prefix('delivery')->name('delivery.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::patch('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update');
    Route::post('/shipments/{shipment}/proof', [ShipmentController::class, 'uploadProof'])->name('shipments.proof');

    // Account Settings
    Route::get('/account', [AccountSettingsController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountSettingsController::class, 'update'])->name('account.update');
});
