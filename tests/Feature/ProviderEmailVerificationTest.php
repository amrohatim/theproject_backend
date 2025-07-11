<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use App\Services\RegistrationService;
use App\Services\TemporaryRegistrationService;
use App\Services\EmailVerificationService;

class ProviderEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected $registrationService;
    protected $tempRegistrationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationService = app(RegistrationService::class);
        $this->tempRegistrationService = app(TemporaryRegistrationService::class);
    }

    /** @test */
    public function it_can_verify_email_with_correct_code()
    {
        // Create a test registration
        $userData = [
            'name' => 'Test Provider',
            'email' => 'test@provider.com',
            'phone' => '+971556441299',
            'business_name' => 'Test Business',
            'business_type' => 'Technology',
        ];

        $result = $this->registrationService->startProviderRegistration($userData);
        $this->assertTrue($result['success']);

        $registrationToken = $result['registration_token'];
        
        // Get the verification code from cache
        $cacheKey = "temp_email_verification_{$registrationToken}";
        $verificationCode = Cache::get($cacheKey);
        $this->assertNotNull($verificationCode);

        // Test the API endpoint
        $response = $this->postJson('/api/provider-registration/verify-email', [
            'registration_token' => $registrationToken,
            'verification_code' => $verificationCode,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Email verified successfully. You can now proceed to phone verification.',
                ]);

        // Verify the code is removed after successful verification
        $this->assertNull(Cache::get($cacheKey));
    }

    /** @test */
    public function it_rejects_invalid_verification_code()
    {
        // Create a test registration
        $userData = [
            'name' => 'Test Provider',
            'email' => 'test@provider.com',
            'phone' => '+971556441299',
            'business_name' => 'Test Business',
            'business_type' => 'Technology',
        ];

        $result = $this->registrationService->startProviderRegistration($userData);
        $registrationToken = $result['registration_token'];

        // Test with invalid code
        $response = $this->postJson('/api/provider-registration/verify-email', [
            'registration_token' => $registrationToken,
            'verification_code' => '123456',
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid verification code. Please try again.',
                ]);
    }

    /** @test */
    public function it_rejects_expired_registration_token()
    {
        $response = $this->postJson('/api/provider-registration/verify-email', [
            'registration_token' => 'invalid_token',
            'verification_code' => '123456',
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/provider-registration/verify-email', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['registration_token', 'verification_code']);
    }

    /** @test */
    public function it_validates_verification_code_format()
    {
        $response = $this->postJson('/api/provider-registration/verify-email', [
            'registration_token' => 'test_token',
            'verification_code' => '12345', // Too short
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['verification_code']);

        $response = $this->postJson('/api/provider-registration/verify-email', [
            'registration_token' => 'test_token',
            'verification_code' => '1234567', // Too long
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['verification_code']);
    }
}
