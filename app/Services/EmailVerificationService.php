<?php

namespace App\Services;

use App\Models\User;
use App\Mail\EmailVerification;
use App\Mail\TempEmailVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class EmailVerificationService
{
    /**
     * Send email verification code to user.
     */
    public function sendVerificationEmail(User $user, string $userType = 'user'): array
    {
        try {
            // Generate verification code
            $verificationCode = $this->generateVerificationCode();
            
            // Store verification code in cache with 24 hour expiry
            $cacheKey = "email_verification_{$user->id}";
            Cache::put($cacheKey, $verificationCode, Carbon::now()->addHours(24));
            
            // Send email using Mailgun
            Mail::to($user->email)->send(new EmailVerification($user, $verificationCode, $userType));
            
            // Log for development
            Log::info("Email verification code sent to {$user->email}: {$verificationCode}", [
                'user_id' => $user->id,
                'user_type' => $userType,
                'expires_at' => Carbon::now()->addHours(24),
            ]);
            
            return [
                'success' => true,
                'message' => 'Email verification sent successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send email verification: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send email verification',
            ];
        }
    }
    
    /**
     * Verify email verification code.
     */
    public function verifyEmail(User $user, string $code): array
    {
        try {
            $cacheKey = "email_verification_{$user->id}";
            $storedCode = Cache::get($cacheKey);
            
            if (!$storedCode) {
                return [
                    'success' => false,
                    'message' => 'Verification code has expired',
                ];
            }
            
            if ($code !== $storedCode) {
                return [
                    'success' => false,
                    'message' => 'Invalid verification code',
                ];
            }
            
            // Mark email as verified
            $user->update([
                'email_verified_at' => now(),
                'registration_step' => 'email_verified',
            ]);
            
            // Remove verification code from cache
            Cache::forget($cacheKey);
            
            return [
                'success' => true,
                'message' => 'Email verified successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Email verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to verify email',
            ];
        }
    }
    
    /**
     * Send verification email for temporary registration.
     */
    public function sendVerificationEmailForTempRegistration(
        string $email,
        string $name,
        string $verificationCode,
        string $userType
    ): array {
        try {
            // Send email using the temporary email verification mailable
            Mail::to($email)->send(new TempEmailVerification($email, $name, $verificationCode, $userType));

            // Log for development
            Log::info("Email verification code for temporary registration sent to {$email}: {$verificationCode}", [
                'email' => $email,
                'name' => $name,
                'code' => $verificationCode,
                'user_type' => $userType,
            ]);

            return [
                'success' => true,
                'message' => 'Email verification sent successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send email verification for temp registration: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send email verification',
            ];
        }
    }

    /**
     * Generate a random 6-digit verification code.
     */
    private function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
