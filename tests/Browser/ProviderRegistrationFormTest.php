<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Provider;

class ProviderRegistrationFormTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test provider registration form loads correctly.
     */
    public function test_provider_registration_form_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    ->assertSee('Join as Provider')
                    ->assertSee('Supply products to Dala3Chic marketplace')
                    ->assertPresent('#providerRegistrationForm')
                    ->assertPresent('#name')
                    ->assertPresent('#business_name')
                    ->assertPresent('#email')
                    ->assertPresent('#phone')
                    ->assertPresent('#password')
                    ->assertPresent('#password_confirmation')
                    ->assertPresent('#terms');
        });
    }

    /**
     * Test required field validation on form submission.
     */
    public function test_required_field_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    ->click('#submit-btn')
                    ->waitFor('#validationErrorModal', 5)
                    ->assertSee('Validation Errors')
                    ->assertSee('Company/Supplier Name')
                    ->assertSee('Business Name')
                    ->assertSee('Email Address')
                    ->assertSee('Phone Number')
                    ->assertSee('Password')
                    ->click('.modal-close')
                    ->waitUntilMissing('#validationErrorModal');
        });
    }

    /**
     * Test real-time field validation.
     */
    public function test_real_time_field_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    // Test name field validation
                    ->type('#name', 'A')
                    ->click('#email') // Trigger blur event
                    ->waitFor('#name-error', 2)
                    ->assertSee('Company/supplier name must be at least 2 characters')
                    
                    // Fix name field
                    ->clear('#name')
                    ->type('#name', 'Valid Company Name')
                    ->click('#email')
                    ->waitUntilMissing('#name-error .fas')
                    
                    // Test email validation
                    ->type('#email', 'invalid-email')
                    ->click('#phone')
                    ->waitFor('#email-error', 2)
                    ->assertSee('Please enter a valid email address')
                    
                    // Fix email field
                    ->clear('#email')
                    ->type('#email', 'valid@example.com')
                    ->click('#phone')
                    ->waitUntilMissing('#email-error .fas')
                    
                    // Test phone validation
                    ->type('#phone', '123')
                    ->click('#password')
                    ->waitFor('#phone-error', 2)
                    ->assertSee('Please enter a valid UAE phone number')
                    
                    // Fix phone field
                    ->clear('#phone')
                    ->type('#phone', '+971501234567')
                    ->click('#password')
                    ->waitUntilMissing('#phone-error .fas');
        });
    }

    /**
     * Test password strength validation.
     */
    public function test_password_strength_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    // Test weak password
                    ->type('#password', '123')
                    ->click('#password_confirmation')
                    ->waitFor('#password-error', 2)
                    ->assertSee('Password must be at least 8 characters')
                    
                    // Test stronger password
                    ->clear('#password')
                    ->type('#password', 'password123')
                    ->click('#password_confirmation')
                    ->waitUntilMissing('#password-error .fas')
                    
                    // Test password confirmation mismatch
                    ->type('#password_confirmation', 'different')
                    ->click('#name')
                    ->waitFor('#password_confirmation-error', 2)
                    ->assertSee('Password confirmation does not match')
                    
                    // Fix password confirmation
                    ->clear('#password_confirmation')
                    ->type('#password_confirmation', 'password123')
                    ->click('#name')
                    ->waitUntilMissing('#password_confirmation-error .fas');
        });
    }

    /**
     * Test business name uniqueness validation.
     */
    public function test_business_name_uniqueness_validation()
    {
        // Create existing provider with business name
        $user = User::factory()->create(['role' => 'provider']);
        Provider::factory()->create([
            'user_id' => $user->id,
            'business_name' => 'Existing Business'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    ->type('#business_name', 'Existing Business')
                    ->click('#email')
                    ->waitFor('#business_name-error', 3)
                    ->assertSee('Business name is already taken');
        });
    }

    /**
     * Test delivery capability selection validation.
     */
    public function test_delivery_capability_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    // Fill required fields
                    ->type('#name', 'Test Company')
                    ->type('#business_name', 'Test Business')
                    ->type('#email', 'test@example.com')
                    ->type('#phone', '+971501234567')
                    ->type('#password', 'password123')
                    ->type('#password_confirmation', 'password123')
                    ->check('#terms')
                    
                    // Uncheck all delivery options
                    ->uncheck('#pickup_only')
                    ->uncheck('#delivery_available')
                    ->uncheck('#both_options')
                    
                    ->click('#submit-btn')
                    ->waitFor('#validationErrorModal', 5)
                    ->assertSee('Please select a delivery option')
                    ->click('.modal-close')
                    ->waitUntilMissing('#validationErrorModal')
                    
                    // Select delivery option
                    ->check('#pickup_only');
        });
    }

    /**
     * Test terms and conditions validation.
     */
    public function test_terms_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    // Fill required fields
                    ->type('#name', 'Test Company')
                    ->type('#business_name', 'Test Business')
                    ->type('#email', 'test@example.com')
                    ->type('#phone', '+971501234567')
                    ->type('#password', 'password123')
                    ->type('#password_confirmation', 'password123')
                    ->check('#pickup_only')
                    
                    // Don't check terms
                    ->uncheck('#terms')
                    
                    ->click('#submit-btn')
                    ->waitFor('#validationErrorModal', 5)
                    ->assertSee('Please agree to the Terms of Service and Privacy Policy')
                    ->click('.modal-close')
                    ->waitUntilMissing('#validationErrorModal')
                    
                    // Check terms
                    ->check('#terms');
        });
    }

    /**
     * Test successful form submission.
     */
    public function test_successful_form_submission()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    // Fill all required fields with valid data
                    ->type('#name', 'Test Company')
                    ->type('#business_name', 'Test Business')
                    ->type('#email', 'test@example.com')
                    ->type('#phone', '+971501234567')
                    ->type('#password', 'password123')
                    ->type('#password_confirmation', 'password123')
                    ->check('#pickup_only')
                    ->check('#terms')
                    
                    ->click('#submit-btn')
                    ->waitFor('#successModal', 10)
                    ->assertSee('Registration Successful')
                    ->assertSee('Your provider registration has been submitted successfully');
        });
    }

    /**
     * Test modal accessibility features.
     */
    public function test_modal_accessibility()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    ->click('#submit-btn')
                    ->waitFor('#validationErrorModal', 5)
                    
                    // Test modal has proper ARIA attributes
                    ->assertAttribute('#validationErrorModal', 'role', 'dialog')
                    ->assertAttribute('#validationErrorList', 'role', 'alert')
                    
                    // Test escape key closes modal
                    ->keys('body', '{escape}')
                    ->waitUntilMissing('#validationErrorModal')
                    
                    // Test clicking outside closes modal
                    ->click('#submit-btn')
                    ->waitFor('#validationErrorModal', 5)
                    ->click('.modal-overlay')
                    ->waitUntilMissing('#validationErrorModal');
        });
    }

    /**
     * Test form accessibility features.
     */
    public function test_form_accessibility()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/provider')
                    // Test form has proper labels and ARIA attributes
                    ->assertAttribute('#name', 'aria-describedby', 'name-error')
                    ->assertAttribute('#email', 'aria-describedby', 'email-error')
                    ->assertAttribute('#phone', 'aria-describedby', 'phone-error')
                    ->assertAttribute('#password', 'aria-describedby', 'password-error password-requirements')
                    ->assertAttribute('#password_confirmation', 'aria-describedby', 'password_confirmation-error')
                    ->assertAttribute('#terms', 'aria-required', 'true')
                    
                    // Test error messages have proper ARIA attributes
                    ->assertAttribute('#name-error', 'role', 'alert')
                    ->assertAttribute('#email-error', 'role', 'alert')
                    ->assertAttribute('#phone-error', 'role', 'alert');
        });
    }
}
