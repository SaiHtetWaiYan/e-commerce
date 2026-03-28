<?php

namespace App\Mail;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorReturnNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ReturnRequest $returnRequest,
        public string $vendorName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Return Request - Order '.$this->returnRequest->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-return-notification',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
