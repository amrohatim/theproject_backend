<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProviderRegistrationValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test required field validation.
     */
    public function test_required_fields_validation()
    {
        $response = $this->postJson('/api/provider/register/validate-info', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'name',
                    'email',
                    'phone',
                    'password',
                    'business_name'
                ]);
    }

    /**
     * Test field length validation.
     */
    public function test_field_length_validation()
    {
        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'A', // Too short
            'email' => str_repeat('a', 250) . '@example.com', // Too long
            'phone' => str_repeat('1', 25), // Too long
            'password' => '123', // Too short
            'password_confirmation' => '123',
            'business_name' => 'B', // Too short
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'name',
                    'email',
                    'phone',
                    'password',
                    'business_name'
                ]);
    }

    /**
     * Test email format validation.
     */
    public function test_email_format_validation()
    {
        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'Test Company',
            'email' => 'invalid-email',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test password confirmation validation.
     */
    public function test_password_confirmation_validation()
    {
        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
            'business_name' => 'Test Business',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test business name uniqueness validation.
     */
    public function test_business_name_uniqueness_validation()
    {
        // Create existing provider with business name
        $existingUser = User::factory()->create(['role' => 'provider']);
        Provider::factory()->create([
            'user_id' => $existingUser->id,
            'business_name' => 'Existing Business'
        ]);

        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Existing Business',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => ['business_name']
                ]);
    }

    /**
     * Test email registration status validation - verified user.
     */
    public function test_email_verified_user_validation()
    {
        User::factory()->create([
            'email' => 'verified@example.com',
            'registration_step' => 'verified'
        ]);

        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'Test Company',
            'email' => 'verified@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => ['email']
                ]);
    }

    /**
     * Test email registration status validation - license completed user.
     */
    public function test_email_license_completed_user_validation()
    {
        User::factory()->create([
            'email' => 'license@example.com',
            'registration_step' => 'license_completed'
        ]);

        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'Test Company',
            'email' => 'license@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => ['email']
                ]);
    }

    /**
     * Test phone registration status validation - verified user.
     */
    public function test_phone_verified_user_validation()
    {
        User::factory()->create([
            'phone' => '+971501234567',
            'registration_step' => 'verified'
        ]);

        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => ['phone']
                ]);
    }

    /**
     * Test UAE phone number format validation.
     */
    public function test_uae_phone_format_validation()
    {
        $invalidPhones = [
            '123456789',      // Not UAE format
            '+1234567890',    // Wrong country code
            '+97150123',      // Too short
            '+9715012345678', // Too long
        ];

        foreach ($invalidPhones as $phone) {
            $response = $this->postJson('/api/provider/register/validate-info', [
                'name' => 'Test Company',
                'email' => 'test@example.com',
                'phone' => $phone,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'business_name' => 'Test Business',
            ]);

            $response->assertStatus(422)
                    ->assertJsonStructure([
                        'success',
                        'message',
                        'errors' => ['phone']
                    ]);
        }
    }

    /**
     * Test valid UAE phone number formats.
     */
    public function test_valid_uae_phone_formats()
    {
        $validPhones = [
            '+971501234567',
            '971501234567',
            '0501234567',
        ];

        foreach ($validPhones as $phone) {
            $response = $this->postJson('/api/provider/register/validate-info', [
                'name' => 'Test Company',
                'email' => 'test' . rand(1000, 9999) . '@example.com',
                'phone' => $phone,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'business_name' => 'Test Business ' . rand(1000, 9999),
            ]);

            $response->assertStatus(201)
                    ->assertJsonStructure([
                        'success',
                        'message'
                    ]);
        }
    }

    /**
     * Test logo file validation.
     */
    public function test_logo_file_validation()
    {
        // Test invalid file type
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
            'logo' => $invalidFile,
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['logo']);

        // Test file too large
        $largeFile = UploadedFile::fake()->image('logo.jpg')->size(3000); // 3MB

        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'Test Company',
            'email' => 'test2@example.com',
            'phone' => '+971501234568',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business 2',
            'logo' => $largeFile,
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['logo']);
    }

    /**
     * Test successful registration with all valid data.
     */
    public function test_successful_registration()
    {
        $logo = UploadedFile::fake()->image('logo.jpg', 100, 100)->size(500);

        $response = $this->postJson('/api/provider/register/validate-info', [
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
            'business_type' => 'Technology',
            'description' => 'A test business description',
            'logo' => $logo,
            'delivery_capability' => true,
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message'
                ]);
    }
}
