<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FirebaseOTPService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FirebaseOTPServiceTest extends TestCase
{
    protected $otpService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test configuration for Firebase
        Config::set('services.firebase.project_id', 'test-project');
        Config::set('services.firebase.web_api_key', 'test_web_api_key');
        Config::set('services.firebase.private_key', null); // Use without service account for testing
        
        // Mock Firebase service to avoid actual Firebase calls during testing
        $this->otpService = $this->getMockBuilder(FirebaseOTPService::class)
            ->onlyMethods(['sendFirebaseOTP'])
            ->getMock();
    }

    /** @test */
    public function it_sends_otp_successfully()
    {
        // Mock the private sendFirebaseOTP method to return success
        $this->otpService->method('sendFirebaseOTP')->willReturn(true);

        $result = $this->otpService->sendOTP('+971501234567');

        $this->assertTrue($result['success']);
        $this->assertEquals('OTP sent successfully via Firebase', $result['message']);
        $this->assertArrayHasKey('request_id', $result);
        $this->assertEquals(600, $result['expires_in']);
        $this->assertEquals('firebase', $result['method']);

        // Verify OTP was cached with Firebase prefix
        $cacheKey = "firebase_otp_{$result['request_id']}";
        $cachedData = Cache::get($cacheKey);
        $this->assertNotNull($cachedData);
        $this->assertEquals('firebase', $cachedData['method']);
    }

    /** @test */
    public function it_handles_firebase_service_failure()
    {
        // Mock the private sendFirebaseOTP method to return failure
        $this->otpService->method('sendFirebaseOTP')->willReturn(false);

        $result = $this->otpService->sendOTP('+971501234567');

        $this->assertFalse($result['success']);
        $this->assertEquals('Failed to send OTP via Firebase', $result['message']);
        $this->assertArrayHasKey('error', $result);
    }

    /** @test */
    public function it_verifies_otp_successfully()
    {
        $requestId = 'firebase_test_request_id';
        $otp = '123456';
        $phone = '+971501234567';

        // Set up cache with Firebase OTP data
        Cache::put("firebase_otp_{$requestId}", [
            'otp' => $otp,
            'phone' => $phone,
            'created_at' => now(),
            'attempts' => 0,
            'method' => 'firebase',
        ], 600);

        $result = $this->otpService->verifyOTP($requestId, $otp);

        $this->assertTrue($result['success']);
        $this->assertEquals('OTP verified successfully', $result['message']);
        $this->assertEquals($phone, $result['phone']);
        $this->assertEquals('firebase', $result['method']);

        // Verify OTP was removed from cache after successful verification
        $this->assertNull(Cache::get("firebase_otp_{$requestId}"));
    }

    /** @test */
    public function it_rejects_invalid_otp()
    {
        $requestId = 'firebase_test_request_id';
        $correctOtp = '123456';
        $wrongOtp = '654321';
        $phone = '+971501234567';

        // Set up cache with Firebase OTP data
        Cache::put("firebase_otp_{$requestId}", [
            'otp' => $correctOtp,
            'phone' => $phone,
            'created_at' => now(),
            'attempts' => 0,
            'method' => 'firebase',
        ], 600);

        $result = $this->otpService->verifyOTP($requestId, $wrongOtp);

        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid OTP. Please try again.', $result['message']);
        $this->assertEquals(2, $result['attempts_remaining']);

        // Verify attempts were incremented
        $otpData = Cache::get("firebase_otp_{$requestId}");
        $this->assertEquals(1, $otpData['attempts']);
    }

    /** @test */
    public function it_blocks_after_max_attempts()
    {
        $requestId = 'firebase_test_request_id';
        $otp = '123456';
        $wrongOtp = '654321';
        $phone = '+971501234567';

        // Set up cache with max attempts reached
        Cache::put("firebase_otp_{$requestId}", [
            'otp' => $otp,
            'phone' => $phone,
            'created_at' => now(),
            'attempts' => 3,
            'method' => 'firebase',
        ], 600);

        $result = $this->otpService->verifyOTP($requestId, $wrongOtp);

        $this->assertFalse($result['success']);
        $this->assertEquals('Maximum verification attempts exceeded. Please request a new OTP.', $result['message']);

        // Verify OTP was removed from cache
        $this->assertNull(Cache::get("firebase_otp_{$requestId}"));
    }

    /** @test */
    public function it_handles_expired_otp()
    {
        $requestId = 'firebase_test_request_id';
        $otp = '123456';

        $result = $this->otpService->verifyOTP($requestId, $otp);

        $this->assertFalse($result['success']);
        $this->assertEquals('OTP expired or invalid request ID', $result['message']);
    }

    /** @test */
    public function it_enforces_rate_limiting_for_resend()
    {
        $phone = '+971501234567';

        // Set rate limit with Firebase prefix
        Cache::put("firebase_otp_rate_limit_{$phone}", now(), 60);

        $result = $this->otpService->resendOTP($phone);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Please wait', $result['message']);
        $this->assertArrayHasKey('wait_time', $result);
    }

    /** @test */
    public function it_allows_resend_after_rate_limit_expires()
    {
        $phone = '+971501234567';

        // Clear any existing rate limit
        Cache::forget("firebase_otp_rate_limit_{$phone}");

        // Mock successful Firebase OTP sending
        $this->otpService->method('sendFirebaseOTP')->willReturn(true);

        $result = $this->otpService->resendOTP($phone);

        $this->assertTrue($result['success']);
        $this->assertEquals('OTP sent successfully via Firebase', $result['message']);
        $this->assertEquals('firebase', $result['method']);
    }

    /** @test */
    public function it_returns_otp_status()
    {
        $requestId = 'firebase_test_request_id';
        $phone = '+971501234567';
        $createdAt = now();

        // Set up cache with Firebase OTP data
        Cache::put("firebase_otp_{$requestId}", [
            'otp' => '123456',
            'phone' => $phone,
            'created_at' => $createdAt,
            'attempts' => 1,
            'method' => 'firebase',
        ], 600);

        $result = $this->otpService->getOTPStatus($requestId);

        $this->assertTrue($result['success']);
        $this->assertEquals('active', $result['status']);
        $this->assertEquals($phone, $result['phone']);
        $this->assertEquals(1, $result['attempts']);
        $this->assertEquals(3, $result['max_attempts']);
        $this->assertEquals('firebase', $result['method']);
        $this->assertArrayHasKey('expires_in', $result);
    }

    /** @test */
    public function it_returns_expired_status_for_missing_otp()
    {
        $requestId = 'non_existent_firebase_request_id';

        $result = $this->otpService->getOTPStatus($requestId);

        $this->assertFalse($result['success']);
        $this->assertEquals('OTP not found or expired', $result['message']);
        $this->assertEquals('expired', $result['status']);
    }

    /** @test */
    public function it_normalizes_phone_numbers_correctly()
    {
        // Test various phone number formats
        $testCases = [
            '0501234567' => '+971501234567',
            '501234567' => '+971501234567',
            '+971501234567' => '+971501234567',
            '971501234567' => '+971501234567',
        ];

        foreach ($testCases as $input => $expected) {
            // Mock successful Firebase OTP sending
            $this->otpService->method('sendFirebaseOTP')->willReturn(true);
            
            $result = $this->otpService->sendOTP($input);
            
            $this->assertTrue($result['success']);
            
            // Check that the normalized phone number was cached
            $cacheKey = "firebase_otp_{$result['request_id']}";
            $cachedData = Cache::get($cacheKey);
            $this->assertEquals($expected, $cachedData['phone']);
        }
    }

    /** @test */
    public function it_generates_unique_request_ids()
    {
        // Mock successful Firebase OTP sending
        $this->otpService->method('sendFirebaseOTP')->willReturn(true);

        $result1 = $this->otpService->sendOTP('+971501234567');
        $result2 = $this->otpService->sendOTP('+971501234568');

        $this->assertNotEquals($result1['request_id'], $result2['request_id']);
        $this->assertStringStartsWith('firebase_otp_', $result1['request_id']);
        $this->assertStringStartsWith('firebase_otp_', $result2['request_id']);
    }

    /** @test */
    public function it_generates_six_digit_otp()
    {
        // Mock successful Firebase OTP sending
        $this->otpService->method('sendFirebaseOTP')->willReturn(true);

        $result = $this->otpService->sendOTP('+971501234567');
        $requestId = $result['request_id'];
        
        $otpData = Cache::get("firebase_otp_{$requestId}");
        
        $this->assertEquals(6, strlen($otpData['otp']));
        $this->assertIsNumeric($otpData['otp']);
    }
}
