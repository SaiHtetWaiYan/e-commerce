<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('customer can start conversation with attachment', function () {
    Storage::fake('public');

    $customer = User::factory()->create();
    $vendor = User::factory()->vendor()->create();
    $file = UploadedFile::fake()->create('support-note.txt', 20, 'text/plain');

    $this->actingAs($customer)
        ->post(route('customer.conversations.start'), [
            'vendor_id' => $vendor->id,
            'body' => 'Please check this file.',
            'attachment' => $file,
        ])
        ->assertRedirect();

    $conversation = Conversation::query()
        ->where('buyer_id', $customer->id)
        ->where('vendor_id', $vendor->id)
        ->first();

    expect($conversation)->not->toBeNull();

    $message = $conversation->messages()->latest('id')->first();

    expect($message)->not->toBeNull()
        ->and($message->attachment_path)->not->toBeNull();

    Storage::disk('public')->assertExists((string) $message->attachment_path);
});

test('vendor can reply with attachment', function () {
    Storage::fake('public');

    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $customer = User::factory()->create();

    $conversation = Conversation::query()->create([
        'buyer_id' => $customer->id,
        'vendor_id' => $vendor->id,
        'last_message_at' => now(),
    ]);

    $this->actingAs($vendor)
        ->post(route('vendor.conversations.reply', $conversation), [
            'body' => 'Sharing your invoice copy.',
            'attachment' => UploadedFile::fake()->create('invoice.pdf', 120, 'application/pdf'),
        ])
        ->assertRedirect(route('vendor.conversations.show', $conversation));

    $message = $conversation->messages()->latest('id')->first();

    expect($message)->not->toBeNull()
        ->and($message->attachment_path)->not->toBeNull();

    Storage::disk('public')->assertExists((string) $message->attachment_path);
});
