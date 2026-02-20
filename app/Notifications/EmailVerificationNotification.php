<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $verificationUrl;
    protected $type;
    protected $metadata;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $verificationUrl, string $type = 'registration', array $metadata = [])
    {
        $this->verificationUrl = $verificationUrl;
        $this->type = $type;
        $this->metadata = $metadata;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'glowlabs');

        // Customize message based on type
        switch ($this->type) {
            case 'vendor_registration':
                return $this->getVendorRegistrationMail($appName);
            case 'provider_registration':
                return $this->getProviderRegistrationMail($appName);
            default:
                return $this->getDefaultRegistrationMail($appName);
        }
    }

    /**
     * Get vendor registration email
     */
    private function getVendorRegistrationMail(string $appName): MailMessage
    {
        return (new MailMessage)
            ->subject("Verify Your Email - {$appName} Vendor Registration")
            ->greeting('Welcome to ' . $appName . '!')
            ->line('Thank you for starting your vendor registration with us.')
            ->line('To complete your registration, please verify your email address by clicking the button below.')
            ->action('Verify Email Address', $this->verificationUrl)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Best regards, The ' . $appName . ' Team');
    }

    /**
     * Get provider registration email
     */
    private function getProviderRegistrationMail(string $appName): MailMessage
    {
        return (new MailMessage)
            ->subject("Verify Your Email - {$appName} Provider Registration")
            ->greeting('Welcome to ' . $appName . '!')
            ->line('Thank you for starting your provider registration with us.')
            ->line('To complete your registration, please verify your email address by clicking the button below.')
            ->action('Verify Email Address', $this->verificationUrl)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Best regards, The ' . $appName . ' Team');
    }

    /**
     * Get default registration email
     */
    private function getDefaultRegistrationMail(string $appName): MailMessage
    {
        return (new MailMessage)
            ->subject("Verify Your Email Address - {$appName}")
            ->greeting('Hello!')
            ->line('Please verify your email address by clicking the button below.')
            ->action('Verify Email Address', $this->verificationUrl)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not request this verification, no further action is required.')
            ->salutation('Best regards, The ' . $appName . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'verification_url' => $this->verificationUrl,
            'type' => $this->type,
            'metadata' => $this->metadata,
        ];
    }
}
