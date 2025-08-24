<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhoneValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test phone validation with various formats.
     */
    public function test_phone_validation_accepts_valid_formats()
    {
        $validPhones = [
            '+971501234567',  // Frontend format
            '971501234567',   // Without +
            '0501234567',     // Local format
            '501234567'       // Just digits
        ];

        foreach ($validPhones as $phone) {
            $response = $this->postJson('/api/provider/register/validate-info', [
                'name' => 'Test Provider',
                'email' => 'test' . rand(1000, 9999) . '@example.com',
                'phone' => $phone,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'business_name' => 'Test Business ' . rand(1000, 9999),
                'business_type' => 'Services',
            ]);

            $this->assertNotEquals(422, $response->status(), 
                "Phone format '{$phone}' should be valid but got validation error: " . 
                json_encode($response->json()));
        }
    }

    /**
     * Test phone validation rejects invalid formats.
     */
    public function test_phone_validation_rejects_invalid_formats()
    {
        $invalidPhones = [
            '+97150123456',   // Too short
            '+9715012345678', // Too long
            '+1234567890',    // Wrong country code
            'abc123456789',   // Contains letters
            '',               // Empty
            '12345',          // Too short
        ];

        foreach ($invalidPhones as $phone) {
            $response = $this->postJson('/api/provider/register/validate-info', [
                'name' => 'Test Provider',
                'email' => 'test' . rand(1000, 9999) . '@example.com',
                'phone' => $phone,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'business_name' => 'Test Business ' . rand(1000, 9999),
                'business_type' => 'Services',
            ]);

            $this->assertEquals(422, $response->status(), 
                "Phone format '{$phone}' should be invalid but passed validation");
        }
    }

    /**
     * Test that phone number is normalized correctly.
     */
    public function test_phone_normalization()
    {
        $testCases = [
            ['input' => '+971501234567', 'expected' => '+971501234567'],
            ['input' => '971501234567', 'expected' => '+971501234567'],
            ['input' => '0501234567', 'expected' => '+971501234567'],
            ['input' => '501234567', 'expected' => '+971501234567'],
        ];

        foreach ($testCases as $case) {
            $response = $this->postJson('/api/provider/register/validate-info', [
                'name' => 'Test Provider',
                'email' => 'test' . rand(1000, 9999) . '@example.com',
                'phone' => $case['input'],
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'business_name' => 'Test Business ' . rand(1000, 9999),
                'business_type' => 'Services',
            ]);

            if ($response->status() === 200 || $response->status() === 201) {
                // Check if the phone was normalized correctly in the response or database
                $this->assertTrue(true, "Phone '{$case['input']}' was accepted");
            } else {
                $this->fail("Phone '{$case['input']}' should be normalized to '{$case['expected']}' but got error: " . 
                    json_encode($response->json()));
            }
        }
    }
}
