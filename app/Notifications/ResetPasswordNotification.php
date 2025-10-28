<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $name = $notifiable->name ?: 'there';
        $mailMessage = (new MailMessage())
            ->subject(__('Reset Your Password'))
            ->greeting(__('Hello :name,', ['name' => $name]))
            ->line(__('We received a request to reset the password for your account.'))
            ->action(__('Reset Password'), $this->resetUrl($notifiable))
            ->line(__('This link will expire in :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60)]))
            ->line(__('If you did not request a password reset, no further action is required.'));

        return $mailMessage;
    }

    /**
     * Get the password reset URL for the given notifiable.
     */
    protected function resetUrl($notifiable): string
    {
        $baseUrl = rtrim((string) env('FRONTEND_PASSWORD_RESET_URL', config('app.url') . '/reset-password'), '/');
        $query = http_build_query([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return $baseUrl . '?' . $query;
    }
}
