<?php

use App\Models\Order;
use App\Models\User;

test('admin dashboard recent orders include a view link to order details', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $customer->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('href="'.route('admin.orders.show', $order).'"', false)
        ->assertSeeText('View');
});
