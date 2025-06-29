<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationUITest extends TestCase
{
    /**
     * Test that vendor registration page loads with enhanced UI elements
     */
    public function test_vendor_registration_page_has_enhanced_ui()
    {
        $response = $this->get('/register/vendor');
        
        $response->assertStatus(200);
        
        // Check for enhanced CSS includes (Vite generates hashed filenames)
        $response->assertSee('enhanced-registration');
        $response->assertSee('password-validation');
        
        // Check for enhanced background
        $response->assertSee('enhanced-registration-bg');
        
        // Check for enhanced form container
        $response->assertSee('enhanced-form-container');
        
        // Check for enhanced form elements
        $response->assertSee('enhanced-form-title');
        $response->assertSee('enhanced-form-subtitle');
        $response->assertSee('enhanced-form-input');
        $response->assertSee('enhanced-input-group');
        $response->assertSee('enhanced-input-icon');
        
        // Check for enhanced password features
        $response->assertSee('enhanced-password-toggle');
        $response->assertSee('enhanced-password-group');
        
        // Check for enhanced buttons
        $response->assertSee('enhanced-btn');
        $response->assertSee('enhanced-btn-primary');
        
        // Check for enhanced step indicators
        $response->assertSee('enhanced-step-indicator');
        $response->assertSee('enhanced-step-number');
        $response->assertSee('enhanced-step-label');
        
        // Check for Font Awesome icons
        $response->assertSee('fas fa-user');
        $response->assertSee('fas fa-envelope');
        $response->assertSee('fas fa-phone');
        $response->assertSee('fas fa-lock');
        $response->assertSee('fas fa-eye');
    }

    /**
     * Test that provider registration page loads with enhanced UI elements
     */
    public function test_provider_registration_page_has_enhanced_ui()
    {
        $response = $this->get('/register/provider');
        
        $response->assertStatus(200);
        
        // Check for enhanced CSS includes (Vite generates hashed filenames)
        $response->assertSee('enhanced-registration');
        $response->assertSee('password-validation');
        
        // Check for enhanced background
        $response->assertSee('enhanced-registration-bg');
        
        // Check for enhanced form container
        $response->assertSee('enhanced-form-container');
        
        // Check for enhanced form elements
        $response->assertSee('enhanced-form-title');
        $response->assertSee('enhanced-form-subtitle');
        $response->assertSee('enhanced-form-input');
        $response->assertSee('enhanced-input-group');
        $response->assertSee('enhanced-input-icon');
        
        // Check for enhanced password features
        $response->assertSee('enhanced-password-toggle');
        $response->assertSee('enhanced-password-group');
        
        // Check for enhanced buttons
        $response->assertSee('enhanced-btn');
        $response->assertSee('enhanced-btn-primary');
        
        // Check for provider-specific enhanced features
        $response->assertSee('Supply & Delivery Options', false);
        $response->assertSee('Company Logo');
        $response->assertSee('bg-purple-50');
        $response->assertSee('border-purple-200');
        
        // Check for Font Awesome icons
        $response->assertSee('fas fa-building');
        $response->assertSee('fas fa-envelope');
        $response->assertSee('fas fa-phone');
        $response->assertSee('fas fa-lock');
        $response->assertSee('fas fa-eye');
        $response->assertSee('fas fa-cloud-upload-alt');
    }

    /**
     * Test that both pages have responsive design classes
     */
    public function test_registration_pages_have_responsive_design()
    {
        $vendorResponse = $this->get('/register/vendor');
        $providerResponse = $this->get('/register/provider');
        
        // Check for responsive grid classes
        $vendorResponse->assertSee('md:grid-cols-2');
        $vendorResponse->assertSee('sm:px-6');
        $vendorResponse->assertSee('lg:px-8');
        
        $providerResponse->assertSee('md:grid-cols-2');
        $providerResponse->assertSee('sm:px-6');
        $providerResponse->assertSee('lg:px-8');
        
        // Check for responsive spacing
        $vendorResponse->assertSee('space-y-8');
        $providerResponse->assertSee('space-y-8');
    }

    /**
     * Test that password validation JavaScript is properly initialized
     */
    public function test_password_validation_javascript_initialization()
    {
        $vendorResponse = $this->get('/register/vendor');
        $providerResponse = $this->get('/register/provider');
        
        // Check for password validation initialization
        $vendorResponse->assertSee('initializeEnhancedPasswordValidation');
        $vendorResponse->assertSee('vendorPasswordValidator');
        
        $providerResponse->assertSee('initializeEnhancedPasswordValidation');
        $providerResponse->assertSee('providerPasswordValidator');
        
        // Check for password validation options
        $vendorResponse->assertSee('requireUppercase: true');
        $vendorResponse->assertSee('requireLowercase: true');
        $vendorResponse->assertSee('requireNumbers: true');
        $vendorResponse->assertSee('requireSpecialChars: true');
        
        $providerResponse->assertSee('requireUppercase: true');
        $providerResponse->assertSee('requireLowercase: true');
        $providerResponse->assertSee('requireNumbers: true');
        $providerResponse->assertSee('requireSpecialChars: true');
    }

    /**
     * Test that enhanced error handling is implemented
     */
    public function test_enhanced_error_handling()
    {
        $vendorResponse = $this->get('/register/vendor');
        $providerResponse = $this->get('/register/provider');
        
        // Check for enhanced error message containers
        $vendorResponse->assertSee('enhanced-error-message');
        $providerResponse->assertSee('enhanced-error-message');
        
        // Check for error handling in JavaScript
        $vendorResponse->assertSee('showErrors');
        $vendorResponse->assertSee('fas fa-exclamation-circle');
        
        $providerResponse->assertSee('showErrors');
        $providerResponse->assertSee('fas fa-exclamation-circle');
    }

    /**
     * Test that enhanced styling is consistent between pages
     */
    public function test_consistent_enhanced_styling()
    {
        $vendorResponse = $this->get('/register/vendor');
        $providerResponse = $this->get('/register/provider');
        
        // Common enhanced classes should be present in both
        $commonClasses = [
            'enhanced-registration-bg',
            'enhanced-form-container',
            'enhanced-form-header',
            'enhanced-form-title',
            'enhanced-form-subtitle',
            'enhanced-form-group',
            'enhanced-form-label',
            'enhanced-form-input',
            'enhanced-input-group',
            'enhanced-input-icon',
            'enhanced-password-group',
            'enhanced-password-toggle',
            'enhanced-btn',
            'enhanced-btn-primary',
            'enhanced-error-message'
        ];
        
        foreach ($commonClasses as $class) {
            $vendorResponse->assertSee($class);
            $providerResponse->assertSee($class);
        }
    }

    /**
     * Test that modern header design is implemented
     */
    public function test_modern_header_design()
    {
        $vendorResponse = $this->get('/register/vendor');
        $providerResponse = $this->get('/register/provider');
        
        // Check for enhanced header styling
        $vendorResponse->assertSee('bg-white/90');
        $vendorResponse->assertSee('backdrop-blur-md');
        $vendorResponse->assertSee('shadow-lg');
        $vendorResponse->assertSee('bg-gradient-to-br from-blue-500 to-purple-600');
        
        $providerResponse->assertSee('bg-white/90');
        $providerResponse->assertSee('backdrop-blur-md');
        $providerResponse->assertSee('shadow-lg');
        $providerResponse->assertSee('bg-gradient-to-br from-purple-500 to-blue-600');
        
        // Check for gradient text
        $vendorResponse->assertSee('bg-gradient-to-r from-blue-600 to-purple-600');
        $providerResponse->assertSee('bg-gradient-to-r from-purple-600 to-blue-600');
    }

    /**
     * Test that enhanced terms and conditions styling is present
     */
    public function test_enhanced_terms_styling()
    {
        $vendorResponse = $this->get('/register/vendor');
        $providerResponse = $this->get('/register/provider');
        
        // Check for enhanced terms container
        $vendorResponse->assertSee('bg-blue-50');
        $vendorResponse->assertSee('border-blue-200');
        $vendorResponse->assertSee('rounded-xl');
        
        $providerResponse->assertSee('bg-purple-50');
        $providerResponse->assertSee('border-purple-200');
        $providerResponse->assertSee('rounded-xl');
        
        // Check for enhanced links
        $vendorResponse->assertSee('font-medium underline');
        $providerResponse->assertSee('font-medium underline');
    }
}
