<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class TwoFactorOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __(strtr($this->details['emailSubject'], [
                ':appname' => config('app.name'),               
                ':receivername' => $details['receiverName'] ?? '',
                ':receiveremail' => $details['receiverEmail'] ?? '',
                ':otpnumber' => $details['otpNumber'] ?? '',
            ])),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.two-factor-otp',
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
