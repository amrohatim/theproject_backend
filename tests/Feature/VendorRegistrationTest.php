<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;

class VendorRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_validates_vendor_info_successfully()
    {
        $vendorData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/vendor/register/validate-info', $vendorData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Vendor information validated successfully',
                    'next_step' => 'otp_verification',
                ]);

        // Check session data
        $this->assertNotNull(session('vendor_registration'));
        $this->assertEquals(1, session('vendor_registration.step'));
    }

    /** @test */
    public function it_rejects_duplicate_vendor_info()
    {
        // Create existing user
        User::factory()->create([
            'email' => 'existing@example.com',
            'phone' => '+971501234567',
            'name' => 'Existing User',
        ]);

        $vendorData = [
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/vendor/register/validate-info', $vendorData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'phone']);
    }

    /** @test */
    public function it_sends_otp_successfully()
    {
        // Set up session with vendor info
        session([
            'vendor_registration' => [
                'step' => 1,
                'vendor_info' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                ]
            ]
        ]);

        // Mock Firebase OTP service response
        $this->mock(\App\Services\FirebaseOTPService::class, function ($mock) {
            $mock->shouldReceive('sendOTP')
                 ->once()
                 ->with('+971501234567')
                 ->andReturn([
                     'success' => true,
                     'request_id' => 'test_request_id',
                     'message' => 'OTP sent successfully via Firebase',
                     'expires_in' => 600,
                     'method' => 'firebase',
                 ]);
        });

        $response = $this->postJson('/api/vendor/register/send-otp');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'OTP sent successfully to your phone',
                    'next_step' => 'otp_verification',
                ]);

        $this->assertEquals(2, session('vendor_registration.step'));
    }

    /** @test */
    public function it_verifies_otp_successfully()
    {
        // Set up session with OTP request
        session([
            'vendor_registration' => [
                'step' => 2,
                'vendor_info' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                ],
                'otp_request_id' => 'test_request_id',
            ]
        ]);

        // Mock Firebase OTP service response
        $this->mock(\App\Services\FirebaseOTPService::class, function ($mock) {
            $mock->shouldReceive('verifyOTP')
                 ->once()
                 ->with('test_request_id', '123456')
                 ->andReturn([
                     'success' => true,
                     'message' => 'OTP verified successfully',
                     'phone' => '+971501234567',
                     'method' => 'firebase',
                 ]);
        });

        $response = $this->postJson('/api/vendor/register/verify-otp', [
            'otp' => '123456'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Phone number verified successfully',
                    'next_step' => 'email_verification',
                ]);

        $this->assertEquals(3, session('vendor_registration.step'));
        $this->assertTrue(session('vendor_registration.phone_verified'));
    }

    /** @test */
    public function it_sends_email_verification_successfully()
    {
        // Set up session with phone verified
        session([
            'vendor_registration' => [
                'step' => 3,
                'vendor_info' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                ],
                'phone_verified' => true,
            ]
        ]);

        $response = $this->postJson('/api/vendor/register/send-email-verification');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Email verification sent successfully',
                    'next_step' => 'company_info',
                ]);

        $this->assertEquals(4, session('vendor_registration.step'));
        $this->assertNotNull(session('vendor_registration.email_verification_token'));
    }

    /** @test */
    public function it_verifies_email_successfully()
    {
        $token = 'test_verification_token';
        
        // Set up session with email verification token
        session([
            'vendor_registration' => [
                'step' => 4,
                'vendor_info' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                ],
                'phone_verified' => true,
                'email_verification_token' => $token,
            ]
        ]);

        $response = $this->postJson('/api/vendor/register/verify-email', [
            'token' => $token
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Email verified successfully',
                    'next_step' => 'company_info',
                ]);

        $this->assertEquals(5, session('vendor_registration.step'));
        $this->assertTrue(session('vendor_registration.email_verified'));
    }

    /** @test */
    public function it_stores_company_info_successfully()
    {
        // Set up session with email verified
        session([
            'vendor_registration' => [
                'step' => 5,
                'vendor_info' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                ],
                'phone_verified' => true,
                'email_verified' => true,
            ]
        ]);

        $logo = UploadedFile::fake()->image('logo.jpg');

        $companyData = [
            'company_name' => 'Test Company',
            'contact_number_1' => '+971509876543',
            'contact_number_2' => '+971501111111',
            'company_email' => 'company@example.com',
            'emirate' => 'Dubai',
            'city' => 'Dubai',
            'street' => '123 Test Street',
            'delivery_capability' => true,
            'logo' => $logo,
            'description' => 'Test company description',
        ];

        $response = $this->postJson('/api/vendor/register/company-info', $companyData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Company information saved successfully',
                    'next_step' => 'license_upload',
                ]);

        $this->assertEquals(6, session('vendor_registration.step'));
        $this->assertNotNull(session('vendor_registration.company_info'));
        Storage::disk('public')->assertExists('companies/' . $logo->hashName());
    }

    /** @test */
    public function it_uploads_license_successfully()
    {
        // Set up session with company info
        session([
            'vendor_registration' => [
                'step' => 6,
                'vendor_info' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                ],
                'phone_verified' => true,
                'email_verified' => true,
                'company_info' => [
                    'name' => 'Test Company',
                    'phone' => '+971509876543',
                    'email' => 'company@example.com',
                ],
            ]
        ]);

        $license = UploadedFile::fake()->create('license.pdf', 1000, 'application/pdf');

        $licenseData = [
            'license_file' => $license,
            'license_duration_years' => 2,
        ];

        $response = $this->postJson('/api/vendor/register/upload-license', $licenseData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'License uploaded successfully',
                    'next_step' => 'complete_registration',
                ]);

        $this->assertEquals(7, session('vendor_registration.step'));
        $this->assertNotNull(session('vendor_registration.license_info'));
        Storage::disk('public')->assertExists('licenses/' . $license->hashName());
    }

    /** @test */
    public function it_completes_registration_successfully()
    {
        // Set up complete session data
        session([
            'vendor_registration' => [
                'step' => 7,
                'vendor_info' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                ],
                'phone_verified' => true,
                'email_verified' => true,
                'company_info' => [
                    'name' => 'Test Company',
                    'phone' => '+971509876543',
                    'email' => 'company@example.com',
                    'address' => '123 Test Street',
                    'city' => 'Dubai',
                    'state' => 'Dubai',
                    'can_deliver' => true,
                    'logo' => 'companies/logo.jpg',
                    'description' => 'Test company',
                ],
                'license_info' => [
                    'file_path' => 'licenses/license.pdf',
                    'start_date' => now()->format('Y-m-d'),
                    'end_date' => now()->addYears(2)->format('Y-m-d'),
                    'duration_years' => 2,
                    'status' => 'active',
                ],
            ]
        ]);

        $response = $this->postJson('/api/vendor/register/complete');

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Vendor registration completed successfully!',
                ]);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+971501234567',
            'role' => 'vendor',
            'status' => 'active',
        ]);

        // Verify company was created
        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'phone' => '+971509876543',
            'email' => 'company@example.com',
            'status' => 'active',
        ]);

        // Verify session was cleared
        $this->assertNull(session('vendor_registration'));

        // Verify response contains token
        $response->assertJsonStructure([
            'success',
            'message',
            'user',
            'company',
            'token',
            'license_info',
        ]);
    }

    /** @test */
    public function it_returns_registration_status()
    {
        // Set up session with partial progress
        session([
            'vendor_registration' => [
                'step' => 3,
                'phone_verified' => true,
                'email_verified' => false,
            ]
        ]);

        $response = $this->getJson('/api/vendor/register/status');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'current_step' => 3,
                    'completed_steps' => [
                        'vendor_info' => true,
                        'otp_sent' => true,
                        'phone_verified' => true,
                        'email_verified' => false,
                        'company_info' => false,
                        'license_uploaded' => false,
                    ],
                ]);
    }

    /** @test */
    public function it_handles_invalid_session_states()
    {
        // Test without session
        $response = $this->postJson('/api/vendor/register/send-otp');
        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid registration session. Please start over.',
                ]);

        // Test with wrong step
        session([
            'vendor_registration' => [
                'step' => 5,
                'phone_verified' => false,
            ]
        ]);

        $response = $this->postJson('/api/vendor/register/verify-otp', ['otp' => '123456']);
        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid registration session. Please start over.',
                ]);
    }
}
