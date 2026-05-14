<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $partnerName;
    public $ownerName;
    public $email;
    public $password;

    public function __construct($partnerName, $ownerName, $email, $password)
    {
        $this->partnerName = $partnerName;
        $this->ownerName = $ownerName;
        $this->email = $email;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'دعوة للانضمام إلى منصة كاشلي - كشريك',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner_invitation',
        );
    }
}
