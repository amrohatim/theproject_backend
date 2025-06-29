<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnhancedRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that vendor registration page loads with enhanced UI
     */
    public function test_vendor_registration_page_loads_with_enhanced_ui()
    {
        $response = $this->get('/register/vendor');
        
        $response->assertStatus(200);
        
        // Check for enhanced CSS classes
        $response->assertSee('enhanced-registration-bg');
        $response->assertSee('enhanced-form-container');
        $response->assertSee('enhanced-form-title');
        $response->assertSee('enhanced-form-input');
        $response->assertSee('enhanced-password-toggle');
        
        // Check for password validation script
        $response->assertSee('password-validation.js');
        
        // Check for enhanced styling
        $response->assertSee('enhanced-registration.css');
    }

    /**
     * Test that provider registration page loads with enhanced UI
     */
    public function test_provider_registration_page_loads_with_enhanced_ui()
    {
        $response = $this->get('/register/provider');
        
        $response->assertStatus(200);
        
        // Check for enhanced CSS classes
        $response->assertSee('enhanced-registration-bg');
        $response->assertSee('enhanced-form-container');
        $response->assertSee('enhanced-form-title');
        $response->assertSee('enhanced-form-input');
        $response->assertSee('enhanced-password-toggle');
        
        // Check for password validation script
        $response->assertSee('password-validation.js');
        
        // Check for enhanced styling
        $response->assertSee('enhanced-registration.css');
        
        // Check for provider-specific features
        $response->assertSee('delivery_capability');
        $response->assertSee('Company Logo');
    }

    /**
     * Test vendor registration with enhanced password validation
     */
    public function test_vendor_registration_with_enhanced_password_validation()
    {
        // Test with weak password
        $weakPasswordData = [
            'name' => 'Test Vendor',
            'email' => 'vendor@test.com',
            'phone' => '+971501234567',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'terms' => true
        ];

        $response = $this->postJson('/api/vendor/register/validate-info', $weakPasswordData);
        
        // Should fail validation due to weak password
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);

        // Test with strong password
        $strongPasswordData = [
            'name' => 'Test Vendor',
            'email' => 'vendor@test.com',
            'phone' => '+971501234567',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'terms' => true
        ];

        $response = $this->postJson('/api/vendor/register/validate-info', $strongPasswordData);
        
        // Should pass validation with strong password
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test provider registration with enhanced password validation
     */
    public function test_provider_registration_with_enhanced_password_validation()
    {
        // Test with weak password
        $weakPasswordData = [
            'name' => 'Test Provider',
            'email' => 'provider@test.com',
            'phone' => '+971501234567',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'delivery_capability' => 'pickup_only',
            'terms' => true
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $weakPasswordData);
        
        // Should fail validation due to weak password
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);

        // Test with strong password
        $strongPasswordData = [
            'name' => 'Test Provider',
            'email' => 'provider@test.com',
            'phone' => '+971501234567',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'delivery_capability' => 'pickup_only',
            'terms' => true
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $strongPasswordData);
        
        // Should pass validation with strong password
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test password confirmation validation
     */
    public function test_password_confirmation_validation()
    {
        $mismatchedPasswordData = [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'phone' => '+971501234567',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'DifferentPass123!',
            'terms' => true
        ];

        // Test vendor registration
        $response = $this->postJson('/api/vendor/register/validate-info', $mismatchedPasswordData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);

        // Test provider registration
        $mismatchedPasswordData['delivery_capability'] = 'pickup_only';
        $response = $this->postJson('/api/provider/register/validate-info', $mismatchedPasswordData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * Test responsive design elements
     */
    public function test_responsive_design_elements()
    {
        $vendorResponse = $this->get('/register/vendor');
        $providerResponse = $this->get('/register/provider');
        
        // Check for responsive classes
        $vendorResponse->assertSee('md:grid-cols-2');
        $vendorResponse->assertSee('sm:px-6');
        $vendorResponse->assertSee('lg:px-8');
        
        $providerResponse->assertSee('md:grid-cols-2');
        $providerResponse->assertSee('sm:px-6');
        $providerResponse->assertSee('lg:px-8');
    }

    /**
     * Test enhanced form validation messages
     */
    public function test_enhanced_form_validation_messages()
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => 'invalid-phone',
            'password' => '',
            'password_confirmation' => '',
            'terms' => false
        ];

        $response = $this->postJson('/api/vendor/register/validate-info', $invalidData);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'email',
            'phone',
            'password'
        ]);
    }

    /**
     * Test enhanced UI components are present
     */
    public function test_enhanced_ui_components_present()
    {
        $vendorResponse = $this->get('/register/vendor');
        $providerResponse = $this->get('/register/provider');
        
        // Check for enhanced step indicators
        $vendorResponse->assertSee('enhanced-step-indicator');
        $vendorResponse->assertSee('enhanced-step-number');
        $vendorResponse->assertSee('enhanced-step-label');
        
        $providerResponse->assertSee('enhanced-step-indicator');
        $providerResponse->assertSee('enhanced-step-number');
        $providerResponse->assertSee('enhanced-step-label');
        
        // Check for enhanced buttons
        $vendorResponse->assertSee('enhanced-btn');
        $vendorResponse->assertSee('enhanced-btn-primary');
        
        $providerResponse->assertSee('enhanced-btn');
        $providerResponse->assertSee('enhanced-btn-primary');
        
        // Check for enhanced input groups
        $vendorResponse->assertSee('enhanced-input-group');
        $vendorResponse->assertSee('enhanced-input-icon');
        
        $providerResponse->assertSee('enhanced-input-group');
        $providerResponse->assertSee('enhanced-input-icon');
    }

    /**
     * Test provider-specific enhanced features
     */
    public function test_provider_specific_enhanced_features()
    {
        $response = $this->get('/register/provider');
        
        // Check for enhanced delivery options
        $response->assertSee('Supply & Delivery Options');
        $response->assertSee('pickup_only');
        $response->assertSee('delivery_available');
        $response->assertSee('both');
        
        // Check for enhanced logo upload
        $response->assertSee('Company Logo');
        $response->assertSee('logo-upload');
        $response->assertSee('logo-preview');
        
        // Check for enhanced styling on provider-specific elements
        $response->assertSee('bg-purple-50');
        $response->assertSee('border-purple-200');
    }
}
