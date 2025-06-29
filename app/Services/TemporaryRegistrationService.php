<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TemporaryRegistrationService
{
    /**
     * Store registration data temporarily until email verification.
     */
    public function storeTemporaryRegistration(array $userData, string $userType): string
    {
        // Generate unique registration token
        $registrationToken = $this->generateRegistrationToken();
        
        // Store data in cache with 24 hour expiry
        $cacheKey = "temp_registration_{$registrationToken}";
        $registrationData = [
            'user_data' => $userData,
            'user_type' => $userType,
            'created_at' => now()->toISOString(),
            'expires_at' => now()->addHours(24)->toISOString(),
        ];
        
        Cache::put($cacheKey, $registrationData, Carbon::now()->addHours(24));
        
        Log::info("Temporary registration stored", [
            'token' => $registrationToken,
            'user_type' => $userType,
            'email' => $userData['email'],
            'expires_at' => $registrationData['expires_at'],
        ]);
        
        return $registrationToken;
    }
    
    /**
     * Retrieve temporary registration data.
     */
    public function getTemporaryRegistration(string $registrationToken): ?array
    {
        $cacheKey = "temp_registration_{$registrationToken}";
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            Log::warning("Temporary registration not found or expired", [
                'token' => $registrationToken,
            ]);
            return null;
        }
        
        return $data;
    }
    
    /**
     * Remove temporary registration data after successful completion.
     */
    public function removeTemporaryRegistration(string $registrationToken): void
    {
        $cacheKey = "temp_registration_{$registrationToken}";
        Cache::forget($cacheKey);
        
        Log::info("Temporary registration removed", [
            'token' => $registrationToken,
        ]);
    }
    
    /**
     * Store email verification code for temporary registration.
     */
    public function storeEmailVerificationCode(string $registrationToken, string $verificationCode): void
    {
        $cacheKey = "temp_email_verification_{$registrationToken}";
        Cache::put($cacheKey, $verificationCode, Carbon::now()->addHours(24));
        
        Log::info("Email verification code stored for temporary registration", [
            'token' => $registrationToken,
            'code' => $verificationCode,
        ]);
    }
    
    /**
     * Verify email verification code for temporary registration.
     */
    public function verifyEmailCode(string $registrationToken, string $code): bool
    {
        $cacheKey = "temp_email_verification_{$registrationToken}";
        $storedCode = Cache::get($cacheKey);
        
        if (!$storedCode) {
            Log::warning("Email verification code not found or expired", [
                'token' => $registrationToken,
            ]);
            return false;
        }
        
        $isValid = $code === $storedCode;
        
        if ($isValid) {
            // Remove verification code after successful verification
            Cache::forget($cacheKey);
            Log::info("Email verification successful for temporary registration", [
                'token' => $registrationToken,
            ]);
        } else {
            Log::warning("Invalid email verification code", [
                'token' => $registrationToken,
                'provided_code' => $code,
            ]);
        }
        
        return $isValid;
    }
    
    /**
     * Generate a unique registration token.
     */
    private function generateRegistrationToken(): string
    {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Check if temporary registration exists and is valid.
     */
    public function isValidRegistrationToken(string $registrationToken): bool
    {
        $data = $this->getTemporaryRegistration($registrationToken);
        return $data !== null;
    }
    
    /**
     * Get registration data by email (for resending verification).
     */
    public function getRegistrationByEmail(string $email): ?array
    {
        // This is a simple implementation - in production you might want to use a database table
        // for better querying capabilities
        $allKeys = Cache::getRedis()->keys('*temp_registration_*');
        
        foreach ($allKeys as $key) {
            $data = Cache::get(str_replace(config('cache.prefix') . ':', '', $key));
            if ($data && isset($data['user_data']['email']) && $data['user_data']['email'] === $email) {
                return $data;
            }
        }
        
        return null;
    }
}
