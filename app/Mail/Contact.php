<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    private $name;
    private $mail;
    private $contact;
    private $message;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $postarr)
    {

        $this->name = $postarr['send_name'];
        $this->mail = $postarr['send_email'];
        $this->message = $postarr['send_message'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'お問合せ有難うございます',
            from: new Address('farm360.info@gmail.com', 'farm360°'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            text: 'contact.mailcontent',
            // view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
