<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RegistrationApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $userType;
    public $dashboardUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->userType = $user->role;
        $this->dashboardUrl = $this->getDashboardUrl();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->userType === 'vendor'
            ? 'Your Vendor Registration Has Been Approved! - Dala3Chic'
            : 'Your Provider Registration Has Been Approved! - Dala3Chic';

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
            view: 'emails.registration-approved',
            with: [
                'user' => $this->user,
                'userType' => $this->userType,
                'dashboardUrl' => $this->dashboardUrl,
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

    /**
     * Get the dashboard URL based on user type
     */
    private function getDashboardUrl(): string
    {
        return match($this->userType) {
            'vendor' => route('vendor.dashboard'),
            'provider' => route('provider.dashboard'),
            default => route('home')
        };
    }
}