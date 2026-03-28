<?php

use App\Enums\DisputeStatus;
use App\Models\Dispute;
use App\Models\User;
use App\Notifications\Customer\DisputeStatusNotification;
use Illuminate\Support\Facades\Notification;

test('resolving a dispute sends notifications to both parties', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $complainant = User::factory()->create();
    $respondent = User::factory()->vendor()->hasVendorProfile()->create();

    $dispute = Dispute::factory()->create([
        'complainant_id' => $complainant->id,
        'respondent_id' => $respondent->id,
        'status' => DisputeStatus::UnderReview,
    ]);

    $this->actingAs($admin)->patch(route('admin.disputes.resolve', $dispute), [
        'status' => 'resolved',
        'resolution' => 'Refund issued to the customer.',
    ]);

    expect($dispute->fresh()->status)->toBe(DisputeStatus::Resolved);

    Notification::assertSentTo($complainant, DisputeStatusNotification::class);
    Notification::assertSentTo($respondent, DisputeStatusNotification::class);
});

test('closing a dispute sends notifications to both parties', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $complainant = User::factory()->create();
    $respondent = User::factory()->vendor()->hasVendorProfile()->create();

    $dispute = Dispute::factory()->create([
        'complainant_id' => $complainant->id,
        'respondent_id' => $respondent->id,
        'status' => DisputeStatus::Pending,
    ]);

    $this->actingAs($admin)->patch(route('admin.disputes.resolve', $dispute), [
        'status' => 'closed',
        'resolution' => 'Issue was resolved between parties.',
    ]);

    expect($dispute->fresh()->status)->toBe(DisputeStatus::Closed);

    Notification::assertSentTo($complainant, DisputeStatusNotification::class);
    Notification::assertSentTo($respondent, DisputeStatusNotification::class);
});
