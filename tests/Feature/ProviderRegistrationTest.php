<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use App\Models\User;
use App\Models\Provider;

class ProviderRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_validates_provider_info_successfully()
    {
        $logo = UploadedFile::fake()->image('logo.jpg');

        $providerData = [
            'name' => 'Jane Provider',
            'email' => 'jane@provider.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'logo' => $logo,
            'stock_locations' => [
                [
                    'name' => 'Main Warehouse',
                    'address' => '123 Storage St, Dubai',
                    'latitude' => 25.2048,
                    'longitude' => 55.2708,
                ]
            ],
            'delivery_capability' => true,
            'delivery_fee_by_emirate' => [
                [
                    'emirate' => 'Dubai',
                    'fee' => 25.50,
                ],
                [
                    'emirate' => 'Abu Dhabi',
                    'fee' => 35.00,
                ]
            ],
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $providerData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Provider information validated successfully',
                    'next_step' => 'otp_verification',
                ]);

        // Check session data
        $this->assertNotNull(session('provider_registration'));
        $this->assertEquals(1, session('provider_registration.step'));
        
        // Verify logo was uploaded
        Storage::disk('public')->assertExists('providers/' . $logo->hashName());
    }

    /** @test */
    public function it_rejects_duplicate_provider_info()
    {
        // Create existing user
        User::factory()->create([
            'email' => 'existing@provider.com',
            'phone' => '+971501234567',
            'name' => 'Existing Provider',
        ]);

        $providerData = [
            'name' => 'Existing Provider',
            'email' => 'existing@provider.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'delivery_capability' => true,
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $providerData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'phone']);
    }

    /** @test */
    public function it_validates_stock_locations_properly()
    {
        $providerData = [
            'name' => 'Jane Provider',
            'email' => 'jane@provider.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'delivery_capability' => true,
            'stock_locations' => [
                [
                    'name' => 'Main Warehouse',
                    'address' => '123 Storage St, Dubai',
                    'latitude' => 91, // Invalid latitude
                    'longitude' => 55.2708,
                ]
            ],
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $providerData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['stock_locations.0.latitude']);
    }

    /** @test */
    public function it_sends_otp_successfully()
    {
        // Set up session with provider info
        session([
            'provider_registration' => [
                'step' => 1,
                'provider_info' => [
                    'name' => 'Jane Provider',
                    'email' => 'jane@provider.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                    'delivery_capability' => true,
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

        $response = $this->postJson('/api/provider/register/send-otp');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'OTP sent successfully to your phone',
                    'next_step' => 'otp_verification',
                ]);

        $this->assertEquals(2, session('provider_registration.step'));
    }

    /** @test */
    public function it_verifies_otp_successfully()
    {
        // Set up session with OTP request
        session([
            'provider_registration' => [
                'step' => 2,
                'provider_info' => [
                    'name' => 'Jane Provider',
                    'email' => 'jane@provider.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                    'delivery_capability' => true,
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

        $response = $this->postJson('/api/provider/register/verify-otp', [
            'otp' => '123456'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Phone number verified successfully',
                    'next_step' => 'email_verification',
                ]);

        $this->assertEquals(3, session('provider_registration.step'));
        $this->assertTrue(session('provider_registration.phone_verified'));
    }

    /** @test */
    public function it_sends_email_verification_successfully()
    {
        // Set up session with phone verified
        session([
            'provider_registration' => [
                'step' => 3,
                'provider_info' => [
                    'name' => 'Jane Provider',
                    'email' => 'jane@provider.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                    'delivery_capability' => true,
                ],
                'phone_verified' => true,
            ]
        ]);

        $response = $this->postJson('/api/provider/register/send-email-verification');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Email verification sent successfully',
                    'next_step' => 'license_upload',
                ]);

        $this->assertEquals(4, session('provider_registration.step'));
        $this->assertNotNull(session('provider_registration.email_verification_token'));
    }

    /** @test */
    public function it_verifies_email_successfully()
    {
        $token = 'test_verification_token';
        
        // Set up session with email verification token
        session([
            'provider_registration' => [
                'step' => 4,
                'provider_info' => [
                    'name' => 'Jane Provider',
                    'email' => 'jane@provider.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                    'delivery_capability' => true,
                ],
                'phone_verified' => true,
                'email_verification_token' => $token,
            ]
        ]);

        $response = $this->postJson('/api/provider/register/verify-email', [
            'token' => $token
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Email verified successfully',
                    'next_step' => 'license_upload',
                ]);

        $this->assertEquals(5, session('provider_registration.step'));
        $this->assertTrue(session('provider_registration.email_verified'));
    }

    /** @test */
    public function it_uploads_license_successfully()
    {
        // Set up session with email verified
        session([
            'provider_registration' => [
                'step' => 5,
                'provider_info' => [
                    'name' => 'Jane Provider',
                    'email' => 'jane@provider.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                    'delivery_capability' => true,
                ],
                'phone_verified' => true,
                'email_verified' => true,
            ]
        ]);

        $license = UploadedFile::fake()->create('license.pdf', 1000, 'application/pdf');

        $licenseData = [
            'license_file' => $license,
            'license_duration_years' => 3,
        ];

        $response = $this->postJson('/api/provider/register/upload-license', $licenseData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'License uploaded successfully',
                    'next_step' => 'complete_registration',
                ]);

        $this->assertEquals(6, session('provider_registration.step'));
        $this->assertNotNull(session('provider_registration.license_info'));
        Storage::disk('public')->assertExists('licenses/' . $license->hashName());
    }

    /** @test */
    public function it_completes_registration_successfully()
    {
        // Set up complete session data
        session([
            'provider_registration' => [
                'step' => 6,
                'provider_info' => [
                    'name' => 'Jane Provider',
                    'email' => 'jane@provider.com',
                    'phone' => '+971501234567',
                    'password' => 'password123',
                    'logo' => 'providers/logo.jpg',
                    'stock_locations' => [
                        [
                            'name' => 'Main Warehouse',
                            'address' => '123 Storage St, Dubai',
                            'latitude' => 25.2048,
                            'longitude' => 55.2708,
                        ]
                    ],
                    'delivery_capability' => true,
                    'delivery_fee_by_emirate' => [
                        [
                            'emirate' => 'Dubai',
                            'fee' => 25.50,
                        ]
                    ],
                ],
                'phone_verified' => true,
                'email_verified' => true,
                'license_info' => [
                    'file_path' => 'licenses/license.pdf',
                    'start_date' => now()->format('Y-m-d'),
                    'end_date' => now()->addYears(3)->format('Y-m-d'),
                    'duration_years' => 3,
                    'status' => 'active',
                ],
            ]
        ]);

        $response = $this->postJson('/api/provider/register/complete');

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Provider registration completed successfully! Your account is pending approval.',
                ]);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Provider',
            'email' => 'jane@provider.com',
            'phone' => '+971501234567',
            'role' => 'provider',
            'status' => 'active',
        ]);

        // Verify provider was created
        $this->assertDatabaseHas('providers', [
            'business_name' => 'Jane Provider',
            'company_name' => 'Jane Provider',
            'logo' => 'providers/logo.jpg',
            'status' => 'pending',
            'is_verified' => false,
            'delivery_capability' => true,
        ]);

        // Verify session was cleared
        $this->assertNull(session('provider_registration'));

        // Verify response contains token
        $response->assertJsonStructure([
            'success',
            'message',
            'user',
            'provider',
            'token',
            'license_info',
            'note',
        ]);
    }

    /** @test */
    public function it_returns_registration_status()
    {
        // Set up session with partial progress
        session([
            'provider_registration' => [
                'step' => 3,
                'phone_verified' => true,
                'email_verified' => false,
            ]
        ]);

        $response = $this->getJson('/api/provider/register/status');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'current_step' => 3,
                    'completed_steps' => [
                        'provider_info' => true,
                        'otp_sent' => true,
                        'phone_verified' => true,
                        'email_verified' => false,
                        'license_uploaded' => false,
                    ],
                ]);
    }

    /** @test */
    public function it_handles_invalid_session_states()
    {
        // Test without session
        $response = $this->postJson('/api/provider/register/send-otp');
        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid registration session. Please start over.',
                ]);

        // Test with wrong step
        session([
            'provider_registration' => [
                'step' => 5,
                'phone_verified' => false,
            ]
        ]);

        $response = $this->postJson('/api/provider/register/verify-otp', ['otp' => '123456']);
        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid registration session. Please start over.',
                ]);
    }

    /** @test */
    public function it_validates_delivery_fees_properly()
    {
        $providerData = [
            'name' => 'Jane Provider',
            'email' => 'jane@provider.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'delivery_capability' => true,
            'delivery_fee_by_emirate' => [
                [
                    'emirate' => 'Dubai',
                    'fee' => -10, // Invalid negative fee
                ]
            ],
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $providerData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['delivery_fee_by_emirate.0.fee']);
    }
}
