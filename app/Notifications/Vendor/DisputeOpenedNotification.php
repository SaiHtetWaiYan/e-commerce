<?php

namespace App\Notifications\Vendor;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeOpenedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Dispute $dispute) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Dispute Filed — #{$this->dispute->id}")
            ->greeting("Hi {$notifiable->name},")
            ->line("A dispute has been filed regarding order #{$this->dispute->order_id}.")
            ->line("**Subject:** {$this->dispute->subject}")
            ->line("**Description:** {$this->dispute->description}")
            ->action('View Orders', route('vendor.orders.index'))
            ->line('Please respond promptly to resolve this issue.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'dispute_opened',
            'dispute_id' => $this->dispute->id,
            'order_id' => $this->dispute->order_id,
            'message' => "A dispute has been filed for order #{$this->dispute->order_id}: {$this->dispute->subject}",
            'url' => route('vendor.orders.index'),
        ];
    }
}
