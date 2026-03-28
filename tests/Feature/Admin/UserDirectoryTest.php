<?php

use App\Models\User;

test('admin sees customers vendors and delivery agents in separate sections', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create([
        'name' => 'Customer Alpha',
        'email' => 'customer-alpha@example.test',
    ]);
    $vendor = User::factory()->vendor()->hasVendorProfile([
        'store_name' => 'Vendor Alpha Store',
    ])->create([
        'name' => 'Vendor Alpha',
        'email' => 'vendor-alpha@example.test',
    ]);
    $deliveryAgent = User::factory()->deliveryAgent()->create([
        'name' => 'Delivery Alpha',
        'email' => 'delivery-alpha@example.test',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Customers')
        ->assertSee('Vendors')
        ->assertSee('Delivery Agents')
        ->assertSee($customer->name)
        ->assertSee($vendor->name)
        ->assertSee($deliveryAgent->name)
        ->assertSee('Vendor Alpha Store');
});
