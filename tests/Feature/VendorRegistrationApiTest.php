<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\TempRegistrationService;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Cache;

class VendorRegistrationApiTest extends TestCase
{
    use RefreshDatabase;

    protected $tempRegistrationService;
    protected $emailVerificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempRegistrationService = app(TempRegistrationService::class);
        $this->emailVerificationService = app(EmailVerificationService::class);
    }

    /** @test */
    public function vendor_email_verification_accepts_correct_parameters()
    {
        // Step 1: Create a temporary registration
        $vendorData = [
            'name' => 'Test Vendor',
            'email' => 'test@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/vendor-registration/info', $vendorData);
        $response->assertStatus(200);
        
        $registrationToken = $response->json('registration_token');
        $this->assertNotNull($registrationToken);

        // Step 2: Get the verification code from the temporary storage
        $tempData = $this->tempRegistrationService->getTemporaryRegistration($registrationToken);
        $this->assertNotNull($tempData);

        // Get the verification code that was stored
        $verificationCode = Cache::get("email_verification_code:{$registrationToken}");
        $this->assertNotNull($verificationCode);
        $this->assertEquals(6, strlen($verificationCode));

        // Step 3: Test email verification with correct parameters
        $verificationResponse = $this->postJson('/api/vendor-registration/verify-email', [
            'registration_token' => $registrationToken,
            'verification_code' => $verificationCode,
        ]);

        $verificationResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNotNull($verificationResponse->json('user_id'));
    }

    /** @test */
    public function vendor_email_verification_rejects_invalid_code()
    {
        // Step 1: Create a temporary registration
        $vendorData = [
            'name' => 'Test Vendor',
            'email' => 'test@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/vendor-registration/info', $vendorData);
        $response->assertStatus(200);
        
        $registrationToken = $response->json('registration_token');

        // Step 2: Test with invalid verification code
        $verificationResponse = $this->postJson('/api/vendor-registration/verify-email', [
            'registration_token' => $registrationToken,
            'verification_code' => '000000', // Invalid code
        ]);

        $verificationResponse->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid or expired verification code',
            ]);
    }

    /** @test */
    public function vendor_email_verification_validates_required_fields()
    {
        // Test missing registration_token
        $response = $this->postJson('/api/vendor-registration/verify-email', [
            'verification_code' => '123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['registration_token']);

        // Test missing verification_code
        $response = $this->postJson('/api/vendor-registration/verify-email', [
            'registration_token' => 'some-token',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['verification_code']);

        // Test invalid verification_code format (not 6 digits)
        $response = $this->postJson('/api/vendor-registration/verify-email', [
            'registration_token' => 'some-token',
            'verification_code' => '12345', // Only 5 digits
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['verification_code']);
    }

    /** @test */
    public function vendor_resend_email_verification_works_with_registration_token()
    {
        // Step 1: Create a temporary registration
        $vendorData = [
            'name' => 'Test Vendor',
            'email' => 'test@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/vendor-registration/info', $vendorData);
        $response->assertStatus(200);
        
        $registrationToken = $response->json('registration_token');

        // Step 2: Test resend email verification
        $resendResponse = $this->postJson('/api/vendor-registration/resend-email-verification', [
            'registration_token' => $registrationToken,
        ]);

        $resendResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
