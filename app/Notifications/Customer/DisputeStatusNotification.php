<?php

namespace App\Notifications\Customer;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Dispute $dispute,
        public string $statusMessage,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Dispute #{$this->dispute->id} — Status Updated")
            ->greeting("Hi {$notifiable->name},")
            ->line($this->statusMessage)
            ->line("**Subject:** {$this->dispute->subject}")
            ->when($this->dispute->resolution, fn (MailMessage $mail) => $mail->line("**Resolution:** {$this->dispute->resolution}"))
            ->action('View Dispute', route('customer.orders.index'))
            ->line('If you have further questions, please contact our support team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'dispute_status',
            'dispute_id' => $this->dispute->id,
            'message' => $this->statusMessage,
            'url' => route('customer.orders.index'),
        ];
    }
}
