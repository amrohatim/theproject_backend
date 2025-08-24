<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderRegistrationFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the provider registration form returns JSON response instead of HTML error page.
     * This test verifies the fix for the JavaScript error: "Unexpected token '<', "<!DOCTYPE "... is not valid JSON"
     */
    public function test_provider_registration_form_returns_json_response()
    {
        // Test data for provider registration
        $providerData = [
            'name' => 'Test Provider',
            'email' => 'test@provider.com',
            'phone' => '501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
        ];

        // Submit the form with proper headers that match the frontend implementation
        $response = $this->postJson('/api/provider/register/validate-info', $providerData, [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        // The response should be JSON (not HTML), regardless of success or failure
        $this->assertJson($response->getContent());
        
        // The response should not contain HTML DOCTYPE declaration
        $this->assertStringNotContainsString('<!DOCTYPE', $response->getContent());
        
        // The response should have a proper JSON structure
        $responseData = $response->json();
        $this->assertIsArray($responseData);
        
        // Should have either success or error structure
        $this->assertTrue(
            isset($responseData['success']) || 
            isset($responseData['errors']) || 
            isset($responseData['message'])
        );
    }

    /**
     * Test that validation errors are returned as JSON with proper structure.
     */
    public function test_provider_registration_validation_errors_return_json()
    {
        // Submit form with missing required fields to trigger validation errors
        $response = $this->postJson('/api/provider/register/validate-info', [], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        // Should return 422 status for validation errors
        $response->assertStatus(422);
        
        // Should return JSON response
        $this->assertJson($response->getContent());
        
        // Should have validation errors structure
        $response->assertJsonStructure([
            'errors' => [
                'name',
                'email',
                'phone',
                'password',
                'business_name'
            ]
        ]);
    }

    /**
     * Test that server errors are handled gracefully and return JSON.
     */
    public function test_provider_registration_server_errors_return_json()
    {
        // Test with invalid data that might cause server errors
        $invalidData = [
            'name' => 'Test Provider',
            'email' => 'invalid-email-format',
            'phone' => 'invalid-phone',
            'password' => '123', // Too short
            'password_confirmation' => 'different-password',
            'business_name' => 'Test Business',
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $invalidData, [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        // Should return JSON response even for server errors
        $this->assertJson($response->getContent());
        
        // Should not contain HTML DOCTYPE declaration
        $this->assertStringNotContainsString('<!DOCTYPE', $response->getContent());
    }
}
