<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TempEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $name;
    public $verificationCode;
    public $userType;

    /**
     * Create a new message instance.
     */
    public function __construct(string $email, string $name, string $verificationCode, string $userType = 'user')
    {
        $this->email = $email;
        $this->name = $name;
        $this->verificationCode = $verificationCode;
        $this->userType = $userType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->userType) {
            'vendor' => 'Verify Your Vendor Account - glowlabs',
            'provider' => 'Verify Your Provider Account - glowlabs',
            'merchant' => 'Verify Your Merchant Account - glowlabs',
            default => 'Verify Your Email Address - glowlabs'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Create a temporary user object for the template
        $tempUser = (object) [
            'id' => 0,
            'name' => $this->name,
            'email' => $this->email,
        ];

        return new Content(
            view: 'emails.temp-email-verification',
            with: [
                'user' => $tempUser,
                'verificationCode' => $this->verificationCode,
                'userType' => $this->userType,
            ],
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
