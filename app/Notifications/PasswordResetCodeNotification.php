<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly string $code)
    {
    }

    /**
     * Get the notification's delivery channels.
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
        $name = trim((string) ($notifiable->name ?? ''));
        $greetingName = $name !== '' ? $name : 'there';

        return (new MailMessage())
            ->subject(__('Your Password Reset Code'))
            ->greeting(__('Hello :name,', ['name' => $greetingName]))
            ->line(__('Use the code below to reset your password.'))
            ->line(__('Reset Code: :code', ['code' => $this->code]))
            ->line(__('This code expires in :minutes minutes.', ['minutes' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60)]))
            ->line(__('If you did not request a password reset, you can safely ignore this email.'));
    }
}
