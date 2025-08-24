<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Provider;

class ValidationApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test business name validation API - available name.
     */
    public function test_business_name_validation_available()
    {
        $response = $this->postJson('/api/validate/business-name', [
            'business_name' => 'Available Business Name'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'available' => true,
                    'message' => 'Business name is available'
                ]);
    }

    /**
     * Test business name validation API - taken name.
     */
    public function test_business_name_validation_taken()
    {
        // Create existing provider with business name
        $user = User::factory()->create(['role' => 'provider']);
        Provider::factory()->create([
            'user_id' => $user->id,
            'business_name' => 'Taken Business Name'
        ]);

        $response = $this->postJson('/api/validate/business-name', [
            'business_name' => 'Taken Business Name'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'available' => false,
                    'message' => 'Business name is already taken'
                ]);
    }

    /**
     * Test business name validation API - missing parameter.
     */
    public function test_business_name_validation_missing_parameter()
    {
        $response = $this->postJson('/api/validate/business-name', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['business_name']);
    }

    /**
     * Test email status validation API - available email.
     */
    public function test_email_status_validation_available()
    {
        $response = $this->postJson('/api/validate/email-status', [
            'email' => 'available@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'available' => true,
                    'message' => 'Email is available'
                ]);
    }

    /**
     * Test email status validation API - verified user.
     */
    public function test_email_status_validation_verified_user()
    {
        User::factory()->create([
            'email' => 'verified@example.com',
            'registration_step' => 'verified'
        ]);

        $response = $this->postJson('/api/validate/email-status', [
            'email' => 'verified@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'available' => false,
                    'message' => 'You have a registered company with this email you cannot create two accounts with the same email'
                ]);
    }

    /**
     * Test email status validation API - license completed user.
     */
    public function test_email_status_validation_license_completed_user()
    {
        User::factory()->create([
            'email' => 'license@example.com',
            'registration_step' => 'license_completed'
        ]);

        $response = $this->postJson('/api/validate/email-status', [
            'email' => 'license@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'available' => false,
                    'message' => 'You have a submit company information wait for admin approval you will receive an email or a call from our support team , Thank you for your patience.'
                ]);
    }

    /**
     * Test email status validation API - pending user (should be available).
     */
    public function test_email_status_validation_pending_user()
    {
        User::factory()->create([
            'email' => 'pending@example.com',
            'registration_step' => 'pending'
        ]);

        $response = $this->postJson('/api/validate/email-status', [
            'email' => 'pending@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'available' => true,
                    'message' => 'Email is available'
                ]);
    }

    /**
     * Test phone status validation API - available phone.
     */
    public function test_phone_status_validation_available()
    {
        $response = $this->postJson('/api/validate/phone-status', [
            'phone' => '+971501234567'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'available' => true,
                    'message' => 'Phone number is available'
                ]);
    }

    /**
     * Test phone status validation API - verified user.
     */
    public function test_phone_status_validation_verified_user()
    {
        User::factory()->create([
            'phone' => '+971501234567',
            'registration_step' => 'verified'
        ]);

        $response = $this->postJson('/api/validate/phone-status', [
            'phone' => '+971501234567'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'available' => false,
                    'message' => 'You have a registered company with this phone you cannot create two accounts with the same phone'
                ]);
    }

    /**
     * Test phone status validation API - pending user (should be available).
     */
    public function test_phone_status_validation_pending_user()
    {
        User::factory()->create([
            'phone' => '+971501234567',
            'registration_step' => 'pending'
        ]);

        $response = $this->postJson('/api/validate/phone-status', [
            'phone' => '+971501234567'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'available' => true,
                    'message' => 'Phone number is available'
                ]);
    }

    /**
     * Test phone normalization in validation.
     */
    public function test_phone_normalization_in_validation()
    {
        // Create user with normalized phone
        User::factory()->create([
            'phone' => '+971501234567',
            'registration_step' => 'verified'
        ]);

        // Test different formats of the same phone number
        $phoneFormats = [
            '+971501234567',
            '971501234567',
            '0501234567'
        ];

        foreach ($phoneFormats as $phone) {
            $response = $this->postJson('/api/validate/phone-status', [
                'phone' => $phone
            ]);

            $response->assertStatus(200)
                    ->assertJson([
                        'available' => false,
                        'message' => 'You have a registered company with this phone you cannot create two accounts with the same phone'
                    ]);
        }
    }

    /**
     * Test validation API with invalid email format.
     */
    public function test_email_validation_invalid_format()
    {
        $response = $this->postJson('/api/validate/email-status', [
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test validation API with missing parameters.
     */
    public function test_validation_apis_missing_parameters()
    {
        // Test business name validation
        $response = $this->postJson('/api/validate/business-name', []);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['business_name']);

        // Test email validation
        $response = $this->postJson('/api/validate/email-status', []);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);

        // Test phone validation
        $response = $this->postJson('/api/validate/phone-status', []);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['phone']);
    }

    /**
     * Test validation API with too long parameters.
     */
    public function test_validation_apis_parameter_length()
    {
        // Test business name too long
        $response = $this->postJson('/api/validate/business-name', [
            'business_name' => str_repeat('a', 256)
        ]);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['business_name']);

        // Test email too long
        $response = $this->postJson('/api/validate/email-status', [
            'email' => str_repeat('a', 250) . '@example.com'
        ]);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);

        // Test phone too long
        $response = $this->postJson('/api/validate/phone-status', [
            'phone' => str_repeat('1', 25)
        ]);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['phone']);
    }
}
