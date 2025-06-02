<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppointmentMail extends Mailable
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
                ':hyperlink' => $hyperlink ?? '',
                ':vcardname' => $details['vcardName'] ?? '',
                ':vcardurl' => $details['vcardUrl'] ?? '',
                ':appointmentdate' => $details['appointmentDate'] ?? '',
                ':appointmenttime' => $details['appointmentTime'] ?? '',
                ':googlecalendarurl' => $details['googleCalendarUrl'] ?? '',
                '%3Agooglecalendarurl' => $details['googleCalendarUrl'] ?? '',
                ':status' => $details['status'] ?? '',
                ':customername' => $details['customerName'] ?? '',
                ':previousdomain' => $details['previousDomain'] ?? '',
                ':currentdomain' => $details['currentDomain'] ?? '',
                ':receivername' => $details['receiverName'] ?? '',
                ':receiveremail' => $details['receiverEmail'] ?? '',
                ':receiverphone' => $details['receiverPhone'] ?? '',
                ':receivermessage' => $details['receiverMessage'] ?? '',
                ':planname' => $details['planName'] ?? '',
                ':plancode' => $details['planCode'] ?? '',
                ':planprice' => $details['planPrice'] ?? '',
                ':expirydate' => $details['expiryDate'] ?? '',
                ':registeredname' => $details['registeredName'] ?? '',
                ':registeredemail' => $details['registeredEmail'] ?? '',
                ':orderid' => $details['orderid'] ?? '',
                ':cardname' => $details['cardname'] ?? '',
                ':cardprice' => $details['cardprice'] ?? '',
                ':paymentstatus' => $details['paymentstatus'] ?? '',
                ':deliverystatus' => $details['deliverystatus'] ?? '',
                ':quantity' => $details['quantity'] ?? '',
                ':trackingnumber' => $details['trackingnumber'] ?? '',
                ':courierpartner' => $details['courierpartner'] ?? '',
                ':orderpageurl' => $details['orderpageurl'] ?? '',
                '%3Aorderpageurl' => $details['orderpageurl'] ?? '',
                ':totalprice' => $details['totalprice'] ?? '',
                ':supportemail' => $details['supportemail'] ?? '',
                ':supportphone' => $details['supportphone'] ?? '',
                ':customeremail' => $details['customeremail'] ?? '',
                ':actionlink' => $details['actionlink'] ?? '',
                '%3Aactionlink' => $details['actionlink'] ?? '',
            ])),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment',
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
