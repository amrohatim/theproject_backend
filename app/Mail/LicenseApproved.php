<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class LicenseApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $userId;
    public $userEmail;
    public $userName;
    public $licenseType;
    public $dashboardUrl;
    public $adminMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $licenseType, ?string $adminMessage = null)
    {
        $this->userId = $user->id;
        $this->userEmail = $user->email;
        $this->userName = $user->name;
        $this->licenseType = $licenseType; // 'vendor', 'merchant', or 'provider'
        $this->adminMessage = $adminMessage;
        $this->dashboardUrl = $this->getDashboardUrl();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->licenseType) {
            'vendor' => 'Your glowlabs Vendor Registration Has Been Approved!',
            'merchant' => 'Your glowlabs Merchant Registration Has Been Approved!',
            'provider' => 'Your glowlabs Provider Registration Has Been Approved!',
            default => 'Your glowlabs Registration Has Been Approved!'
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
            view: 'emails.license-approved',
            with: [
                'user' => $user,
                'licenseType' => $this->licenseType,
                'dashboardUrl' => $this->dashboardUrl,
                'adminMessage' => $this->adminMessage,
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
    public function getMessageContent(string $language = 'en'): array
    {
        $templatePath = resource_path('views/messages_when_approval.md');
        $content = file_get_contents($templatePath);
        
        // Parse the content based on license type and language
        $pattern = "/\*\*{$this->licenseType} message when approved:{$language}\*\*(.*?)(?=\*\*|$)/is";
        preg_match($pattern, $content, $matches);
        
        if (!empty($matches[1])) {
            $messageContent = trim($matches[1]);
            
            // Extract subject and body
            if (preg_match('/Subject:\s*(.+?)(?:\n\n|\n(?=[A-Z]))/s', $messageContent, $subjectMatch)) {
                $subject = trim($subjectMatch[1]);
                $body = trim(str_replace($subjectMatch[0], '', $messageContent));
            } else {
                $subject = $this->envelope()->subject;
                $body = $messageContent;
            }
            
            // Replace placeholders
            $userName = $this->getUserName();
            $body = str_replace(
                ['[Provider Name]', '[Vendor Name]', '[Merchant Name]', '[اسم المزود ]', '[اسم البائع ]', '[اسم التاجر ]'],
                $userName,
                $body
            );
            
            return [
                'subject' => $subject,
                'body' => $body
            ];
        }
        
        // Fallback to default content
        return [
            'subject' => $this->envelope()->subject,
            'body' => $this->getDefaultMessage()
        ];
    }

    /**
     * Get user name based on license type
     */
    private function getUserName(): string
    {
        return $this->userName ?? 'Valued User';
    }

    /**
     * Get default message if template parsing fails
     */
    private function getDefaultMessage(): string
    {
        $type = ucfirst($this->licenseType);
        return "Hello {$this->getUserName()},\n\nWe are thrilled to inform you that your glowlabs {$this->licenseType} registration has been approved! You are now officially part of the glowlabs marketplace.\n\nYou can access your dashboard and manage your account at: {$this->dashboardUrl}\n\nBest regards,\nglowlabs Team";
    }
}
