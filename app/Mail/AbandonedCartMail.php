<?php

namespace App\Mail;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Cart $cart,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You left items in your cart!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.abandoned-cart',
        );
    }
}
