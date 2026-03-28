<?php

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('delivery agent cannot mark shipment delivered without proof', function () {
    $agent = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create();

    $shipment = Shipment::query()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => $agent->id,
        'status' => 'assigned',
    ]);

    $this->actingAs($agent)
        ->patch(route('delivery.shipments.update', $shipment), [
            'status' => 'delivered',
            'description' => 'Delivered at customer doorstep',
        ])
        ->assertSessionHasErrors('status');

    expect($shipment->fresh()->status->value)->toBe('assigned');
});

test('delivery agent must confirm cod cash collection before completing delivery', function () {
    $agent = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create([
        'status' => 'shipped',
        'payment_method' => 'cod',
        'payment_status' => PaymentStatus::Pending,
    ]);

    $shipment = Shipment::query()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => $agent->id,
        'status' => 'in_transit',
        'delivery_proof_image' => 'shipment-proofs/existing-proof.jpg',
    ]);

    $this->actingAs($agent)
        ->patch(route('delivery.shipments.update', $shipment), [
            'status' => 'delivered',
            'description' => 'Delivered but cash not confirmed',
        ])
        ->assertSessionHasErrors('cash_collected');

    $shipment = $shipment->fresh();
    $order = $order->fresh();

    expect($shipment->status->value)->toBe('in_transit')
        ->and($order->status->value)->toBe('shipped')
        ->and($order->payment_status)->toBe(PaymentStatus::Pending);
});

test('delivery agent can upload proof then complete delivery', function () {
    Storage::fake('public');

    $agent = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create([
        'status' => 'shipped',
    ]);

    $shipment = Shipment::query()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => $agent->id,
        'status' => 'in_transit',
    ]);

    $this->actingAs($agent)
        ->post(route('delivery.shipments.proof', $shipment), [
            'proof_image' => UploadedFile::fake()->image('proof.jpg'),
            'recipient_name' => 'John Customer',
            'recipient_phone' => '555-1234',
            'notes' => 'Front desk accepted parcel.',
        ])
        ->assertSessionHas('status', 'Delivery proof uploaded.');

    $shipment = $shipment->fresh();

    expect($shipment->delivery_proof_image)->not->toBeNull();
    Storage::disk('public')->assertExists((string) $shipment->delivery_proof_image);

    $this->actingAs($agent)
        ->patch(route('delivery.shipments.update', $shipment), [
            'status' => 'delivered',
            'description' => 'Delivered successfully',
            'cash_collected' => '1',
        ])
        ->assertSessionHas('status', 'Shipment updated.');

    $shipment = $shipment->fresh();
    $order = $order->fresh();

    expect($shipment->status->value)->toBe('delivered')
        ->and($order->status->value)->toBe('delivered')
        ->and($order->payment_status)->toBe(PaymentStatus::Paid)
        ->and($order->paid_at)->not->toBeNull()
        ->and($order->statusHistories()->where('status', 'delivered')->exists())->toBeTrue();
});

test('delivery completion marks legacy cash on delivery payment method as paid', function () {
    Storage::fake('public');

    $agent = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create([
        'status' => 'shipped',
        'payment_method' => 'cash_on_delivery',
        'payment_status' => PaymentStatus::Pending,
    ]);

    $shipment = Shipment::query()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => $agent->id,
        'status' => 'in_transit',
    ]);

    $this->actingAs($agent)
        ->post(route('delivery.shipments.proof', $shipment), [
            'proof_image' => UploadedFile::fake()->image('proof.jpg'),
            'recipient_name' => 'Legacy COD Customer',
        ])
        ->assertSessionHas('status', 'Delivery proof uploaded.');

    $this->actingAs($agent)
        ->patch(route('delivery.shipments.update', $shipment), [
            'status' => 'delivered',
            'description' => 'Delivered with cash collected',
            'cash_collected' => '1',
        ])
        ->assertSessionHas('status', 'Shipment updated.');

    $order = $order->fresh();

    expect($order->status->value)->toBe('delivered')
        ->and($order->payment_status)->toBe(PaymentStatus::Paid)
        ->and($order->statusHistories()->where('status', 'delivered')->exists())->toBeTrue();
});
