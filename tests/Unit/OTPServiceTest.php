<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\OTPService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class OTPServiceTest extends TestCase
{
    protected $otpService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test configuration
        Config::set('services.smartvision.base_url', 'https://api.test.com');
        Config::set('services.smartvision.api_key', 'test_api_key');
        Config::set('services.smartvision.sender_id', 'TEST_SENDER');
        
        $this->otpService = new OTPService();
    }

    /** @test */
    public function it_sends_otp_successfully()
    {
        // Mock successful HTTP response
        Http::fake([
            'api.test.com/send-sms' => Http::response([
                'success' => true,
                'message' => 'SMS sent successfully',
                'message_id' => 'test_message_id',
            ], 200)
        ]);

        $result = $this->otpService->sendOTP('+971501234567');

        $this->assertTrue($result['success']);
        $this->assertEquals('OTP sent successfully', $result['message']);
        $this->assertArrayHasKey('request_id', $result);
        $this->assertEquals(600, $result['expires_in']);

        // Verify OTP was cached
        $cacheKey = "otp_{$result['request_id']}";
        $this->assertNotNull(Cache::get($cacheKey));
    }

    /** @test */
    public function it_handles_sms_api_failure()
    {
        // Mock failed HTTP response
        Http::fake([
            'api.test.com/send-sms' => Http::response([
                'success' => false,
                'message' => 'Invalid API key',
            ], 401)
        ]);

        $result = $this->otpService->sendOTP('+971501234567');

        $this->assertFalse($result['success']);
        $this->assertEquals('Failed to send OTP via SMS service', $result['message']);
        $this->assertArrayHasKey('error', $result);
    }

    /** @test */
    public function it_verifies_otp_successfully()
    {
        $requestId = 'test_request_id';
        $otp = '123456';
        $phone = '+971501234567';

        // Set up cache with OTP data
        Cache::put("otp_{$requestId}", [
            'otp' => $otp,
            'phone' => $phone,
            'created_at' => now(),
            'attempts' => 0,
        ], 600);

        $result = $this->otpService->verifyOTP($requestId, $otp);

        $this->assertTrue($result['success']);
        $this->assertEquals('OTP verified successfully', $result['message']);
        $this->assertEquals($phone, $result['phone']);

        // Verify OTP was removed from cache after successful verification
        $this->assertNull(Cache::get("otp_{$requestId}"));
    }

    /** @test */
    public function it_rejects_invalid_otp()
    {
        $requestId = 'test_request_id';
        $correctOtp = '123456';
        $wrongOtp = '654321';
        $phone = '+971501234567';

        // Set up cache with OTP data
        Cache::put("otp_{$requestId}", [
            'otp' => $correctOtp,
            'phone' => $phone,
            'created_at' => now(),
            'attempts' => 0,
        ], 600);

        $result = $this->otpService->verifyOTP($requestId, $wrongOtp);

        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid OTP. Please try again.', $result['message']);
        $this->assertEquals(2, $result['attempts_remaining']);

        // Verify attempts were incremented
        $otpData = Cache::get("otp_{$requestId}");
        $this->assertEquals(1, $otpData['attempts']);
    }

    /** @test */
    public function it_blocks_after_max_attempts()
    {
        $requestId = 'test_request_id';
        $otp = '123456';
        $wrongOtp = '654321';
        $phone = '+971501234567';

        // Set up cache with max attempts reached
        Cache::put("otp_{$requestId}", [
            'otp' => $otp,
            'phone' => $phone,
            'created_at' => now(),
            'attempts' => 3,
        ], 600);

        $result = $this->otpService->verifyOTP($requestId, $wrongOtp);

        $this->assertFalse($result['success']);
        $this->assertEquals('Maximum verification attempts exceeded. Please request a new OTP.', $result['message']);

        // Verify OTP was removed from cache
        $this->assertNull(Cache::get("otp_{$requestId}"));
    }

    /** @test */
    public function it_handles_expired_otp()
    {
        $requestId = 'test_request_id';
        $otp = '123456';

        $result = $this->otpService->verifyOTP($requestId, $otp);

        $this->assertFalse($result['success']);
        $this->assertEquals('OTP expired or invalid request ID', $result['message']);
    }

    /** @test */
    public function it_enforces_rate_limiting_for_resend()
    {
        $phone = '+971501234567';

        // Set rate limit
        Cache::put("otp_rate_limit_{$phone}", now(), 60);

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
        Cache::forget("otp_rate_limit_{$phone}");

        // Mock successful HTTP response
        Http::fake([
            'api.test.com/send-sms' => Http::response([
                'success' => true,
                'message' => 'SMS sent successfully',
            ], 200)
        ]);

        $result = $this->otpService->resendOTP($phone);

        $this->assertTrue($result['success']);
        $this->assertEquals('OTP sent successfully', $result['message']);
    }

    /** @test */
    public function it_returns_otp_status()
    {
        $requestId = 'test_request_id';
        $phone = '+971501234567';
        $createdAt = now();

        // Set up cache with OTP data
        Cache::put("otp_{$requestId}", [
            'otp' => '123456',
            'phone' => $phone,
            'created_at' => $createdAt,
            'attempts' => 1,
        ], 600);

        $result = $this->otpService->getOTPStatus($requestId);

        $this->assertTrue($result['success']);
        $this->assertEquals('active', $result['status']);
        $this->assertEquals($phone, $result['phone']);
        $this->assertEquals(1, $result['attempts']);
        $this->assertEquals(3, $result['max_attempts']);
        $this->assertArrayHasKey('expires_in', $result);
    }

    /** @test */
    public function it_returns_expired_status_for_missing_otp()
    {
        $requestId = 'non_existent_request_id';

        $result = $this->otpService->getOTPStatus($requestId);

        $this->assertFalse($result['success']);
        $this->assertEquals('OTP not found or expired', $result['message']);
        $this->assertEquals('expired', $result['status']);
    }

    /** @test */
    public function it_generates_unique_request_ids()
    {
        // Mock successful HTTP responses
        Http::fake([
            'api.test.com/send-sms' => Http::response([
                'success' => true,
                'message' => 'SMS sent successfully',
            ], 200)
        ]);

        $result1 = $this->otpService->sendOTP('+971501234567');
        $result2 = $this->otpService->sendOTP('+971501234568');

        $this->assertNotEquals($result1['request_id'], $result2['request_id']);
    }

    /** @test */
    public function it_generates_six_digit_otp()
    {
        // Mock successful HTTP response
        Http::fake([
            'api.test.com/send-sms' => Http::response([
                'success' => true,
                'message' => 'SMS sent successfully',
            ], 200)
        ]);

        $result = $this->otpService->sendOTP('+971501234567');
        $requestId = $result['request_id'];
        
        $otpData = Cache::get("otp_{$requestId}");
        
        $this->assertEquals(6, strlen($otpData['otp']));
        $this->assertIsNumeric($otpData['otp']);
    }
}
