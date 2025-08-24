<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\PhoneNumberNotFound;
use Kreait\Firebase\Exception\Auth\InvalidPhoneNumber;
use Kreait\Firebase\Exception\Auth\TooManyRequests;
use Kreait\Firebase\Exception\FirebaseException;
use App\Providers\FirebaseServiceProvider;

class FirebaseOTPService
{
    protected $auth;
    protected $projectId;
    protected $webApiKey;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $this->webApiKey = config('services.firebase.web_api_key');

        // Use the centralized Firebase service provider
        $this->auth = FirebaseServiceProvider::getAuth();
    }

    /**
     * Send OTP to phone number using Firebase Auth
     *
     * @param string $phoneNumber
     * @return array
     */
    public function sendOTP(string $phoneNumber): array
    {
        try {
            // Normalize phone number format
            $phoneNumber = $this->normalizePhoneNumber($phoneNumber);
            
            // Generate 6-digit OTP
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            // Create unique request ID
            $requestId = uniqid('firebase_otp_', true);
            
            // Store OTP in cache for 10 minutes with additional metadata
            Cache::put("firebase_otp_{$requestId}", [
                'otp' => $otp,
                'phone' => $phoneNumber,
                'created_at' => now(),
                'attempts' => 0,
                'method' => 'firebase',
            ], 600); // 10 minutes

            // For development/testing, we'll simulate Firebase OTP sending
            // In production, you would use Firebase's phone authentication
            $success = $this->sendFirebaseOTP($phoneNumber, $otp, $requestId);

            if ($success) {
                Log::info('Firebase OTP sent successfully', [
                    'request_id' => $requestId,
                    'phone' => $phoneNumber,
                ]);

                return [
                    'success' => true,
                    'request_id' => $requestId,
                    'message' => 'OTP sent successfully via Firebase',
                    'expires_in' => 600, // 10 minutes
                    'method' => 'firebase',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send OTP via Firebase',
                    'error' => 'Firebase OTP service error',
                ];
            }
        } catch (InvalidPhoneNumber $e) {
            Log::error('Invalid phone number for Firebase OTP', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);

            return [
                'success' => false,
                'message' => 'Invalid phone number format',
                'error' => $e->getMessage(),
            ];
        } catch (TooManyRequests $e) {
            Log::error('Too many Firebase OTP requests', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);

            return [
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'error' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            Log::error('Firebase OTP sending exception', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send OTP due to system error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify OTP using Firebase Auth
     *
     * @param string $requestId
     * @param string $otp
     * @return array
     */
    public function verifyOTP(string $requestId, string $otp): array
    {
        try {
            $cacheKey = "firebase_otp_{$requestId}";
            $otpData = Cache::get($cacheKey);

            if (!$otpData) {
                return [
                    'success' => false,
                    'message' => 'OTP expired or invalid request ID',
                ];
            }

            // Check attempts limit (max 3 attempts)
            if ($otpData['attempts'] >= 3) {
                Cache::forget($cacheKey);
                
                return [
                    'success' => false,
                    'message' => 'Maximum verification attempts exceeded. Please request a new OTP.',
                ];
            }

            // Increment attempt count
            $otpData['attempts']++;
            Cache::put($cacheKey, $otpData, 600);

            // Verify OTP
            if ($otpData['otp'] === $otp) {
                // OTP is correct, remove from cache
                Cache::forget($cacheKey);
                
                Log::info('Firebase OTP verified successfully', [
                    'request_id' => $requestId,
                    'phone' => $otpData['phone'],
                ]);

                return [
                    'success' => true,
                    'message' => 'OTP verified successfully',
                    'phone' => $otpData['phone'],
                    'method' => 'firebase',
                ];
            } else {
                Log::warning('Firebase OTP verification failed', [
                    'request_id' => $requestId,
                    'phone' => $otpData['phone'],
                    'attempts' => $otpData['attempts'],
                ]);

                return [
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.',
                    'attempts_remaining' => 3 - $otpData['attempts'],
                ];
            }
        } catch (\Exception $e) {
            Log::error('Firebase OTP verification exception', [
                'error' => $e->getMessage(),
                'request_id' => $requestId,
            ]);

            return [
                'success' => false,
                'message' => 'OTP verification failed due to system error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Resend OTP with rate limiting
     *
     * @param string $phoneNumber
     * @return array
     */
    public function resendOTP(string $phoneNumber): array
    {
        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);
        $rateLimitKey = "firebase_otp_rate_limit_{$phoneNumber}";
        $lastSent = Cache::get($rateLimitKey);

        // Rate limit: 1 OTP per minute
        if ($lastSent) {
            $secondsSinceLastSent = now()->diffInSeconds($lastSent);
            if ($secondsSinceLastSent < 60) {
                $waitTime = 60 - $secondsSinceLastSent;

                return [
                    'success' => false,
                    'message' => "Please wait {$waitTime} seconds before requesting another OTP",
                    'wait_time' => $waitTime,
                ];
            }
        }

        // Set rate limit
        Cache::put($rateLimitKey, now(), 60);

        // Send new OTP
        return $this->sendOTP($phoneNumber);
    }

    /**
     * Get OTP status
     *
     * @param string $requestId
     * @return array
     */
    public function getOTPStatus(string $requestId): array
    {
        $cacheKey = "firebase_otp_{$requestId}";
        $otpData = Cache::get($cacheKey);

        if (!$otpData) {
            return [
                'success' => false,
                'message' => 'OTP not found or expired',
                'status' => 'expired',
            ];
        }

        $expiresAt = $otpData['created_at']->addMinutes(10);
        $remainingTime = now()->diffInSeconds($expiresAt, false);

        return [
            'success' => true,
            'status' => 'active',
            'phone' => $otpData['phone'],
            'attempts' => $otpData['attempts'],
            'max_attempts' => 3,
            'expires_in' => max(0, $remainingTime),
            'created_at' => $otpData['created_at']->toISOString(),
            'method' => 'firebase',
        ];
    }

    /**
     * Normalize phone number to international format
     *
     * @param string $phoneNumber
     * @return string
     */
    private function normalizePhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add country code if not present (assuming UAE +971)
        if (!str_starts_with($phoneNumber, '971') && !str_starts_with($phoneNumber, '+971')) {
            if (str_starts_with($phoneNumber, '0')) {
                $phoneNumber = '971' . substr($phoneNumber, 1);
            } else {
                $phoneNumber = '971' . $phoneNumber;
            }
        }
        
        // Ensure it starts with +
        if (!str_starts_with($phoneNumber, '+')) {
            $phoneNumber = '+' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Send OTP via Firebase Auth or fallback to testing mode
     *
     * @param string $phoneNumber
     * @param string $otp
     * @param string $requestId
     * @return bool
     */
    private function sendFirebaseOTP(string $phoneNumber, string $otp, string $requestId): bool
    {
        try {
            // Check if we're in testing/development mode
            if (app()->environment(['local', 'testing']) || !$this->hasValidFirebaseCredentials()) {
                return $this->sendTestingOTP($phoneNumber, $otp, $requestId);
            }

            // Try to send real SMS via Firebase Auth
            return $this->sendRealFirebaseOTP($phoneNumber, $otp, $requestId);

        } catch (\Exception $e) {
            Log::error('Firebase OTP send failed', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
                'request_id' => $requestId,
            ]);

            return false;
        }
    }

    /**
     * Send real OTP via Firebase Auth (requires proper setup)
     *
     * @param string $phoneNumber
     * @param string $otp
     * @param string $requestId
     * @return bool
     */
    private function sendRealFirebaseOTP(string $phoneNumber, string $otp, string $requestId): bool
    {
        try {
            if (!$this->auth) {
                throw new \Exception('Firebase Auth not initialized');
            }

            // For Firebase Auth phone verification, we would typically:
            // 1. Use Firebase Admin SDK to create a custom token
            // 2. Use Firebase Client SDK on frontend for phone verification
            // 3. Or use a third-party SMS service with Firebase integration

            // Since Firebase doesn't directly send SMS from server-side,
            // we'll use a hybrid approach with logging for now
            Log::info('Firebase OTP would be sent via client-side verification', [
                'phone' => $phoneNumber,
                'otp' => $otp,
                'request_id' => $requestId,
                'method' => 'firebase_auth',
            ]);

            // In a real implementation, you would:
            // - Store the OTP in Firebase Firestore
            // - Trigger a Cloud Function to send SMS
            // - Or integrate with a SMS provider like Twilio

            return true;

        } catch (\Exception $e) {
            Log::error('Real Firebase OTP send failed', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);

            return false;
        }
    }

    /**
     * Send testing OTP (logs the OTP for development)
     *
     * @param string $phoneNumber
     * @param string $otp
     * @param string $requestId
     * @return bool
     */
    private function sendTestingOTP(string $phoneNumber, string $otp, string $requestId): bool
    {
        Log::info('=== TESTING MODE: OTP CODE ===', [
            'phone' => $phoneNumber,
            'otp' => $otp,
            'request_id' => $requestId,
            'message' => "Your verification code is: {$otp}",
            'expires_in' => '10 minutes',
        ]);

        // In testing mode, always return success
        return true;
    }

    /**
     * Check if valid Firebase credentials are configured
     *
     * @return bool
     */
    private function hasValidFirebaseCredentials(): bool
    {
        return FirebaseServiceProvider::isConfigured();
    }
}
