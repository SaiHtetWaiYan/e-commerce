<?php

namespace Tests\Feature\Admin;

use App\Enums\DisputeStatus;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

test('admin can view disputes list', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $order = Order::factory()->create(['user_id' => $customer->id]);
    OrderItem::factory()->create(['order_id' => $order->id, 'vendor_id' => $vendor->id]);

    Dispute::query()->create([
        'order_id' => $order->id,
        'complainant_id' => $customer->id,
        'respondent_id' => $vendor->id,
        'subject' => 'Test dispute',
        'description' => 'Test description',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.disputes.index'));

    $response->assertSuccessful();
    $response->assertSee('Test dispute');
});

test('admin can view a dispute detail', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $order = Order::factory()->create(['user_id' => $customer->id]);
    OrderItem::factory()->create(['order_id' => $order->id, 'vendor_id' => $vendor->id]);

    $dispute = Dispute::query()->create([
        'order_id' => $order->id,
        'complainant_id' => $customer->id,
        'respondent_id' => $vendor->id,
        'subject' => 'Damaged item',
        'description' => 'Item arrived damaged',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.disputes.show', $dispute));

    $response->assertSuccessful();
    $response->assertSee('Damaged item');
});

test('admin can resolve a dispute', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $order = Order::factory()->create(['user_id' => $customer->id]);

    $dispute = Dispute::query()->create([
        'order_id' => $order->id,
        'complainant_id' => $customer->id,
        'respondent_id' => $vendor->id,
        'subject' => 'Test',
        'description' => 'Test description',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.disputes.resolve', $dispute), [
        'status' => 'resolved',
        'resolution' => 'Full refund issued to customer.',
    ]);

    $response->assertRedirect();
    expect($dispute->fresh()->status)->toBe(DisputeStatus::Resolved)
        ->and($dispute->fresh()->resolution)->toBe('Full refund issued to customer.')
        ->and($dispute->fresh()->resolved_by)->toBe($admin->id);
});
