<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;

test('payout generation command creates vendor payout for delivered sales', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $customer = User::factory()->create();

    $periodStart = Carbon::parse('2026-02-01')->startOfDay();
    $periodEnd = Carbon::parse('2026-02-07')->endOfDay();

    $order = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'delivered_at' => Carbon::parse('2026-02-03 10:30:00'),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'vendor_id' => $vendor->id,
        'subtotal' => 100.00,
    ]);

    $this->artisan('payouts:generate', [
        '--from' => $periodStart->toDateString(),
        '--to' => $periodEnd->toDateString(),
    ])->assertExitCode(0);

    $this->assertDatabaseHas('vendor_payouts', [
        'vendor_id' => $vendor->id,
        'period_start' => $periodStart->toDateString(),
        'period_end' => $periodEnd->toDateString(),
    ]);

    $this->artisan('payouts:generate', [
        '--from' => $periodStart->toDateString(),
        '--to' => $periodEnd->toDateString(),
    ])->assertExitCode(0);

    $this->assertDatabaseCount('vendor_payouts', 1);
});
