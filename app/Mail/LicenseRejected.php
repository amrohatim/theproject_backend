<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class LicenseRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $userId;
    public $userEmail;
    public $userName;
    public $licenseType;
    public $rejectionReason;
    public $dashboardUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $licenseType, string $rejectionReason)
    {
        $this->userId = $user->id;
        $this->userEmail = $user->email;
        $this->userName = $user->name;
        $this->licenseType = $licenseType; // 'vendor', 'merchant', or 'provider'
        $this->rejectionReason = $rejectionReason;
        $this->dashboardUrl = $this->getDashboardUrl();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->licenseType) {
            'vendor' => 'Your glowlabs Vendor Registration Has Been Rejected',
            'merchant' => 'Your glowlabs Merchant Registration Has Been Rejected',
            'provider' => 'Your glowlabs Provider Registration Has Been Rejected',
            default => 'Your glowlabs Registration Has Been Rejected'
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
        // Create a user-like object for the template
        $user = (object) [
            'id' => $this->userId,
            'email' => $this->userEmail,
            'name' => $this->userName,
        ];

        return new Content(
            view: 'emails.license-rejected',
            with: [
                'user' => $user,
                'licenseType' => $this->licenseType,
                'rejectionReason' => $this->rejectionReason,
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
     * Get the dashboard URL based on license type
     */
    private function getDashboardUrl(): string
    {
        return match($this->licenseType) {
            'vendor' => route('vendor.dashboard'),
            'provider' => route('provider.dashboard'),
            'merchant' => route('merchant.dashboard'),
            default => route('home')
        };
    }

    /**
     * Get the message content from the template file
     */
    public function getMessageContent(): array
    {
        $templatePath = resource_path('../message_rejected.md');
        
        if (!file_exists($templatePath)) {
            // Fallback to default content
            return [
                'title' => 'Your glowlabs Registration Has Been Rejected',
                'body' => "Hello {$this->getUserName()},\n\nWe regret to inform you that your glowlabs registration has been rejected. We have reviewed your application and found that it does not meet our requirements. Please review our guidelines and reapply once you have made the necessary improvements.",
                'call_to_action' => 'Please review our guidelines and reapply once you have made the necessary improvements.'
            ];
        }
        
        $content = file_get_contents($templatePath);
        
        // Parse the content
        $title = '';
        $body = '';
        $callToAction = '';
        
        // Extract title
        if (preg_match('/\*\*Title\*\*\s*\n(.+?)(?=\n\*\*|$)/s', $content, $matches)) {
            $title = trim($matches[1]);
        }
        
        // Extract body
        if (preg_match('/\*\*Body\*\*\s*\n(.*?)(?=\n\*\*Reason\*\*)/s', $content, $matches)) {
            $body = trim($matches[1]);
        }
        
        // Extract call to action
        if (preg_match('/\*\*Call to Action\*\*\s*\n(.+?)(?=\n|$)/s', $content, $matches)) {
            $callToAction = trim($matches[1]);
        }
        
        // Replace placeholders
        $userName = $this->getUserName();
        $body = str_replace('[User Name]', $userName, $body);
        
        return [
            'title' => $title,
            'body' => $body,
            'call_to_action' => $callToAction
        ];
    }

    /**
     * Get user name
     */
    private function getUserName(): string
    {
        return $this->userName ?? 'Valued User';
    }
}
