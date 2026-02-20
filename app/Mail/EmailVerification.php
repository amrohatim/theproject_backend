<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EmailVerification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationCode;
    public $userType;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationCode, string $userType = 'user')
    {
        $this->user = $user;
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
        return new Content(
            view: 'emails.email-verification',
            with: [
                'user' => $this->user,
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