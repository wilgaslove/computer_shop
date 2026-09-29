<?php

namespace App\Mail;

use App\Models\ContactMessage;
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
        return new Envelope(
            subject: 'Re: ' . $this->contactMessage->subject,
            // Le client répond directement à l'adresse de la boutique.
            replyTo: [new Address(config('shop.contact.email'), config('app.name'))],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-reply');
    }
}
