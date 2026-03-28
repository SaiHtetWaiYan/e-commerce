<?php

namespace App\Notifications\Vendor;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_review',
            'review_id' => $this->review->id,
            'message' => "A customer left a {$this->review->rating}-star review on your product.",
            'url' => route('vendor.products.index'), // Assuming there's no specific review page for vendor yet
        ];
    }
}
