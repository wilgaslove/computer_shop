<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Support\SiteContent;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactReplyMail extends Mailable
{
    public function __construct(
        public ContactMessage $contactMessage,
        public string $replyBody,
    ) {
    }

    public function envelope(): Envelope
    {
        $shop = SiteContent::get('shop');

        return new Envelope(
            subject: 'Re: ' . $this->contactMessage->subject,
            // Le client répond directement à l'adresse de la boutique.
            replyTo: [new Address($shop['email'], $shop['brand'])],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-reply',
            with: ['brand' => SiteContent::get('shop')['brand']],
        );
    }
}
