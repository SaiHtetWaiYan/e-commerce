<?php

namespace App\Notifications\Customer;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('How was your order? Leave a review!')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your order #{$this->order->order_number} has been delivered. We'd love to hear your feedback!")
            ->line('Share your experience to help other shoppers make better decisions.')
            ->action('Write a Review', route('customer.reviews.index'))
            ->line('Thank you for shopping with us!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review_request',
            'order_id' => $this->order->id,
            'message' => "Your order #{$this->order->order_number} has been delivered. Share your review!",
            'url' => route('customer.reviews.index'),
        ];
    }
}
