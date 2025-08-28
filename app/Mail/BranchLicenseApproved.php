<?php

namespace App\Mail;

use App\Models\BranchLicense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BranchLicenseApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $license;
    public $adminMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(BranchLicense $license, ?string $adminMessage = null)
    {
        $this->license = $license;
        $this->adminMessage = $adminMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Branch License Approved - ' . $this->license->branch->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.branch-license-approved',
            with: [
                'license' => $this->license,
                'branch' => $this->license->branch,
                'vendor' => $this->license->branch->user,
                'company' => $this->license->branch->company,
                'adminMessage' => $this->adminMessage,
                'dashboardUrl' => route('vendor.dashboard'),
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
