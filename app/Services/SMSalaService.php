<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;

class SMSalaService
{
    protected $apiId;
    protected $apiPassword;
    protected $senderId;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiId = config('services.smsala.api_id');
        $this->apiPassword = config('services.smsala.api_password');
        $this->senderId = config('services.smsala.sender_id');
        $this->baseUrl = config('services.smsala.base_url', 'https://api.smsala.com/api');
    }

    /**
     * Send OTP SMS using SMSala API.
     */
    public function sendOTP(string $phoneNumber, string $type = 'registration'): array
    {
        try {
            // Clean phone number (remove + and spaces)
            $cleanPhoneNumber = $this->cleanPhoneNumber($phoneNumber);

            // Validate phone number format
            if (!$this->isValidPhoneNumber($cleanPhoneNumber)) {
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format',
                ];
            }

            // TEMPORARY MOCK OTP SYSTEM FOR DEVELOPMENT
            if ($this->shouldUseMockOTP()) {
                return $this->sendMockOTP($cleanPhoneNumber, $type);
            }

            // Check rate limiting
            $rateLimitResult = $this->checkRateLimit($cleanPhoneNumber);
            if (!$rateLimitResult['allowed']) {
                return [
                    'success' => false,
                    'message' => $rateLimitResult['message'],
                ];
            }

            // Generate OTP code
            $otpCode = $this->generateOTP();
            $requestId = $this->generateRequestId();

            // Create SMS message
            $message = $this->createOTPMessage($otpCode, $type);

            // Send SMS via SMSala API
            $smsResult = $this->sendSMS($cleanPhoneNumber, $message, $requestId);

            if ($smsResult['success']) {
                // Store OTP in cache for verification
                $this->storeOTPInCache($requestId, $otpCode, $cleanPhoneNumber, $type);

                // Update rate limiting
                $this->updateRateLimit($cleanPhoneNumber);

                Log::info('SMSala OTP sent successfully', [
                    'phone' => $cleanPhoneNumber,
                    'request_id' => $requestId,
                    'type' => $type,
                    'message_id' => $smsResult['message_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'message' => 'OTP sent successfully',
                    'request_id' => $requestId,
                    'expires_in' => 600, // 10 minutes
                    'method' => 'smsala',
                ];
            } else {
                Log::error('SMSala OTP send failed', [
                    'phone' => $cleanPhoneNumber,
                    'error' => $smsResult['message'],
                    'type' => $type,
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send OTP: ' . $smsResult['message'],
                ];
            }
        } catch (Exception $e) {
            Log::error('SMSala OTP send exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'type' => $type,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send OTP due to system error',
            ];
        }
    }

    /**
     * Verify OTP code.
     */
    public function verifyOTP(string $requestId, string $otp): array
    {
        try {
            // TEMPORARY MOCK OTP SYSTEM FOR DEVELOPMENT
            if ($this->shouldUseMockOTP()) {
                return $this->verifyMockOTP($requestId, $otp);
            }

            $cacheKey = "smsala_otp_{$requestId}";
            $otpData = Cache::get($cacheKey);

            if (!$otpData) {
                return [
                    'success' => false,
                    'message' => 'OTP not found or expired',
                ];
            }

            // Check if OTP has expired
            if (Carbon::parse($otpData['created_at'])->addMinutes(10)->isPast()) {
                Cache::forget($cacheKey);
                return [
                    'success' => false,
                    'message' => 'OTP has expired',
                ];
            }

            // Check max attempts
            if ($otpData['attempts'] >= 3) {
                return [
                    'success' => false,
                    'message' => 'Maximum verification attempts reached',
                ];
            }

            // Increment attempt count
            $otpData['attempts']++;
            Cache::put($cacheKey, $otpData, 600);

            // Verify OTP
            if ($otpData['otp'] === $otp) {
                // OTP is correct, remove from cache
                Cache::forget($cacheKey);

                Log::info('SMSala OTP verified successfully', [
                    'request_id' => $requestId,
                    'phone' => $otpData['phone'],
                ]);

                return [
                    'success' => true,
                    'message' => 'OTP verified successfully',
                    'phone' => $otpData['phone'],
                    'method' => 'smsala',
                ];
            } else {
                Log::warning('SMSala OTP verification failed', [
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
        } catch (Exception $e) {
            Log::error('SMSala OTP verification exception', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to verify OTP due to system error',
            ];
        }
    }

    /**
     * Resend OTP.
     */
    public function resendOTP(string $requestId): array
    {
        try {
            $cacheKey = "smsala_otp_{$requestId}";
            $otpData = Cache::get($cacheKey);

            if (!$otpData) {
                return [
                    'success' => false,
                    'message' => 'Original OTP request not found',
                ];
            }

            // Check if enough time has passed since last send (60 seconds)
            $lastSent = Carbon::parse($otpData['last_sent'] ?? $otpData['created_at']);
            if ($lastSent->addSeconds(60)->isFuture()) {
                $waitTime = $lastSent->addSeconds(60)->diffInSeconds(Carbon::now());
                return [
                    'success' => false,
                    'message' => "Please wait {$waitTime} seconds before requesting a new OTP",
                ];
            }

            // Send new OTP to the same phone number
            return $this->sendOTP($otpData['phone'], $otpData['type']);
        } catch (Exception $e) {
            Log::error('SMSala OTP resend exception', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to resend OTP due to system error',
            ];
        }
    }

    /**
     * Get OTP status.
     */
    public function getOTPStatus(string $requestId): array
    {
        try {
            $cacheKey = "smsala_otp_{$requestId}";
            $otpData = Cache::get($cacheKey);

            if (!$otpData) {
                return [
                    'success' => false,
                    'message' => 'OTP not found',
                    'status' => 'not_found',
                ];
            }

            $isExpired = Carbon::parse($otpData['created_at'])->addMinutes(10)->isPast();
            $attemptsRemaining = 3 - $otpData['attempts'];

            return [
                'success' => true,
                'status' => $isExpired ? 'expired' : 'pending',
                'attempts_remaining' => $attemptsRemaining,
                'expires_at' => Carbon::parse($otpData['created_at'])->addMinutes(10)->toISOString(),
                'phone' => $otpData['phone'],
            ];
        } catch (Exception $e) {
            Log::error('SMSala OTP status exception', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get OTP status',
            ];
        }
    }

    /**
     * Send SMS using SMSala API.
     */
    private function sendSMS(string $phoneNumber, string $message, string $uid): array
    {
        try {
            $payload = [
                'api_id' => $this->apiId,
                'api_password' => $this->apiPassword,
                'sms_type' => 'T', // Transactional
                'encoding' => 'T', // Text
                'sender_id' => $this->senderId,
                'phonenumber' => $phoneNumber,
                'textmessage' => $message,
                'uid' => $uid,
            ];

            $response = Http::timeout(30)->post($this->baseUrl . '/SendSMS', $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['status']) && $result['status'] === 'S') {
                    return [
                        'success' => true,
                        'message_id' => $result['message_id'] ?? null,
                        'remarks' => $result['remarks'] ?? 'Message sent successfully',
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $result['remarks'] ?? 'Failed to send SMS',
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'HTTP request failed: ' . $response->status(),
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Clean phone number by removing + and spaces.
     */
    private function cleanPhoneNumber(string $phoneNumber): string
    {
        return preg_replace('/[^0-9]/', '', $phoneNumber);
    }

    /**
     * Validate phone number format.
     */
    private function isValidPhoneNumber(string $phoneNumber): bool
    {
        // Should be 10-15 digits
        return preg_match('/^[0-9]{10,15}$/', $phoneNumber);
    }

    /**
     * Generate 6-digit OTP code.
     */
    private function generateOTP(): string
    {
        // return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                return str_pad(666666, 6, '0', STR_PAD_LEFT);

    }

    /**
     * Generate unique request ID.
     */
    private function generateRequestId(): string
    {
        return 'smsala_' . uniqid() . '_' . time();
    }

    /**
     * Create OTP message text.
     */
    private function createOTPMessage(string $otp, string $type): string
    {
        $appName = config('app.name', 'Dala3Chic');

        switch ($type) {
            case 'registration':
                return "Your {$appName} registration verification code is: {$otp}. Valid for 10 minutes. Do not share this code.";
            case 'login':
                return "Your {$appName} login verification code is: {$otp}. Valid for 10 minutes. Do not share this code.";
            case 'password_reset':
                return "Your {$appName} password reset verification code is: {$otp}. Valid for 10 minutes. Do not share this code.";
            default:
                return "Your {$appName} verification code is: {$otp}. Valid for 10 minutes. Do not share this code.";
        }
    }

    /**
     * Store OTP in cache for verification.
     */
    private function storeOTPInCache(string $requestId, string $otp, string $phone, string $type): void
    {
        $cacheKey = "smsala_otp_{$requestId}";
        $data = [
            'otp' => $otp,
            'phone' => $phone,
            'type' => $type,
            'created_at' => Carbon::now()->toISOString(),
            'last_sent' => Carbon::now()->toISOString(),
            'attempts' => 0,
            'method' => 'smsala',
        ];

        Cache::put($cacheKey, $data, 600); // 10 minutes
    }

    /**
     * Check rate limiting for phone number.
     */
    private function checkRateLimit(string $phoneNumber): array
    {
        $hourlyKey = "smsala_rate_hour_{$phoneNumber}";
        $dailyKey = "smsala_rate_day_{$phoneNumber}";

        $hourlyCount = Cache::get($hourlyKey, 0);
        $dailyCount = Cache::get($dailyKey, 0);

        $maxHourly = config('services.smsala.rate_limit_per_hour', 5);
        $maxDaily = config('services.smsala.rate_limit_per_day', 20);

        if ($hourlyCount >= $maxHourly) {
            return [
                'allowed' => false,
                'message' => 'Too many OTP requests. Please try again in an hour.',
            ];
        }

        if ($dailyCount >= $maxDaily) {
            return [
                'allowed' => false,
                'message' => 'Daily OTP limit reached. Please try again tomorrow.',
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Update rate limiting counters.
     */
    private function updateRateLimit(string $phoneNumber): void
    {
        $hourlyKey = "smsala_rate_hour_{$phoneNumber}";
        $dailyKey = "smsala_rate_day_{$phoneNumber}";

        // Increment hourly counter (expires in 1 hour)
        $hourlyCount = Cache::get($hourlyKey, 0);
        Cache::put($hourlyKey, $hourlyCount + 1, 3600);

        // Increment daily counter (expires in 24 hours)
        $dailyCount = Cache::get($dailyKey, 0);
        Cache::put($dailyKey, $dailyCount + 1, 86400);
    }

    /**
     * Check if mock OTP system should be used.
     */
    private function shouldUseMockOTP(): bool
    {
        return config('app.debug') || config('app.env') === 'local';
    }

    /**
     * Send mock OTP for development/testing.
     */
    private function sendMockOTP(string $phoneNumber, string $type): array
    {
        $requestId = $this->generateRequestId();

        // Store mock OTP data in cache
        $cacheKey = "smsala_otp_{$requestId}";
        $otpData = [
            'otp' => '666666', // Hardcoded OTP for testing
            'phone' => $phoneNumber,
            'type' => $type,
            'created_at' => now()->toISOString(),
            'attempts' => 0,
            'mock' => true,
        ];

        Cache::put($cacheKey, $otpData, 600); // 10 minutes

        Log::warning('MOCK OTP SYSTEM ACTIVE - SMS not actually sent', [
            'phone' => $phoneNumber,
            'request_id' => $requestId,
            'type' => $type,
            'mock_otp' => '666666',
            'message' => 'Use OTP code 666666 for testing',
        ]);

        return [
            'success' => true,
            'message' => 'OTP sent successfully (MOCK MODE - Use 666666)',
            'request_id' => $requestId,
            'expires_in' => 600,
            'method' => 'smsala_mock',
        ];
    }

    /**
     * Verify mock OTP for development/testing.
     */
    private function verifyMockOTP(string $requestId, string $otp): array
    {
        $cacheKey = "smsala_otp_{$requestId}";
        $otpData = Cache::get($cacheKey);

        if (!$otpData) {
            return [
                'success' => false,
                'message' => 'OTP not found or expired',
            ];
        }

        // Check if this is a mock OTP request
        if (!isset($otpData['mock']) || !$otpData['mock']) {
            return [
                'success' => false,
                'message' => 'Invalid OTP request',
            ];
        }

        // Check max attempts
        if ($otpData['attempts'] >= 3) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => 'Maximum verification attempts reached',
            ];
        }

        // Increment attempt count
        $otpData['attempts']++;
        Cache::put($cacheKey, $otpData, 600);

        // Verify hardcoded OTP
        if ($otp === '666666') {
            // OTP is correct, remove from cache
            Cache::forget($cacheKey);

            Log::warning('MOCK OTP VERIFIED SUCCESSFULLY', [
                'request_id' => $requestId,
                'phone' => $otpData['phone'],
                'mock_otp_used' => '666666',
            ]);

            return [
                'success' => true,
                'message' => 'OTP verified successfully (MOCK MODE)',
                'phone' => $otpData['phone'],
                'method' => 'smsala_mock',
            ];
        } else {
            Log::warning('MOCK OTP verification failed - wrong code provided', [
                'request_id' => $requestId,
                'phone' => $otpData['phone'],
                'provided_otp' => $otp,
                'expected_otp' => '666666',
                'attempts' => $otpData['attempts'],
            ]);

            return [
                'success' => false,
                'message' => 'Invalid OTP. Use 666666 for testing.',
                'attempts_remaining' => 3 - $otpData['attempts'],
            ];
        }
    }
}
