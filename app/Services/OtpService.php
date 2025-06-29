<?php

namespace App\Services;

use App\Models\OtpVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class OtpService
{
    public function __construct()
    {
        // Simple OTP service - ready for integration with real SMS provider
    }

    /**
     * Send OTP to phone number.
     */
    public function sendOtp(string $phoneNumber, string $type = 'registration'): array
    {
        try {
            // Clean up expired OTPs for this phone number
            $this->cleanupExpiredOtps($phoneNumber);

            // Check if there's a recent pending OTP
            $existingOtp = OtpVerification::where('phone_number', $phoneNumber)
                ->where('status', 'pending')
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if ($existingOtp) {
                return [
                    'success' => false,
                    'message' => 'OTP already sent. Please wait before requesting a new one.',
                    'retry_after' => Carbon::now()->diffInSeconds($existingOtp->expires_at),
                ];
            }

            // Generate OTP code
            $otpCode = OtpVerification::generateOtpCode();
            $expiresAt = Carbon::now()->addMinutes(5); // 5 minutes expiry

            // Create OTP record
            $otpRecord = OtpVerification::create([
                'phone_number' => $phoneNumber,
                'otp_code' => $otpCode,
                'type' => $type,
                'status' => 'pending',
                'expires_at' => $expiresAt,
                'attempts' => 0,
                'max_attempts' => 3,
            ]);

            // For development/testing: Log the OTP code
            // TODO: Integrate with real SMS provider for production
            Log::info("OTP generated for {$phoneNumber}: {$otpCode}", [
                'otp_id' => $otpRecord->id,
                'type' => $type,
                'expires_at' => $expiresAt,
            ]);

            return [
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp_id' => $otpRecord->id,
                'expires_at' => $expiresAt->toISOString(),
                // For development only - remove in production
                'dev_otp' => config('app.debug') ? $otpCode : null,
            ];
        } catch (Exception $e) {
            Log::error("Error sending OTP to {$phoneNumber}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
            ];
        }
    }

    /**
     * Verify OTP code.
     */
    public function verifyOtp(string $phoneNumber, string $otpCode): array
    {
        try {
            $otpRecord = OtpVerification::where('phone_number', $phoneNumber)
                ->where('otp_code', $otpCode)
                ->where('status', 'pending')
                ->first();

            if (!$otpRecord) {
                return [
                    'success' => false,
                    'message' => 'Invalid OTP code',
                ];
            }

            if ($otpRecord->isExpired()) {
                $otpRecord->markAsExpired();
                return [
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new one.',
                ];
            }

            if ($otpRecord->maxAttemptsReached()) {
                return [
                    'success' => false,
                    'message' => 'Maximum verification attempts reached. Please request a new OTP.',
                ];
            }

            // For now, we only verify against the stored OTP code
            // In production, this could integrate with Cisco DUO for additional verification

            // Mark as verified
            $otpRecord->markAsVerified();

            Log::info("OTP verified successfully for {$phoneNumber}", [
                'otp_id' => $otpRecord->id,
                'type' => $otpRecord->type,
            ]);

            return [
                'success' => true,
                'message' => 'OTP verified successfully',
                'otp_id' => $otpRecord->id,
                'type' => $otpRecord->type,
            ];
        } catch (Exception $e) {
            Log::error("Error verifying OTP for {$phoneNumber}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to verify OTP. Please try again.',
            ];
        }
    }



    /**
     * Clean up expired OTPs.
     */
    private function cleanupExpiredOtps(string $phoneNumber): void
    {
        OtpVerification::where('phone_number', $phoneNumber)
            ->where('expires_at', '<', Carbon::now())
            ->where('status', 'pending')
            ->update(['status' => 'expired']);
    }
}
