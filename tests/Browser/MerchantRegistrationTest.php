<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Support\Facades\Storage;

class MerchantRegistrationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test complete merchant registration flow with Google Maps integration.
     */
    public function test_complete_merchant_registration_flow()
    {
        $this->browse(function (Browser $browser) {
            // Step 1: Navigate to registration choice page
            $browser->visit('/register')
                    ->assertSee('Choose Your Registration Type')
                    ->click('@merchant-registration-link')
                    ->assertPathIs('/register/merchant');

            // Step 2: Fill merchant registration form with Google Maps location
            $browser->type('name', 'Test Merchant Store')
                    ->type('email', 'testmerchant@example.com')
                    ->type('phone', '+971501234567')
                    ->type('password', 'TestPassword123')
                    ->type('password_confirmation', 'TestPassword123');

            // Test Google Maps integration
            $browser->click('#location-search')
                    ->waitFor('#map-container', 5)
                    ->assertVisible('#google-map')
                    ->type('#location-search', 'Dubai Mall, Dubai')
                    ->pause(2000); // Wait for autocomplete

            // Simulate clicking on map (we'll use JavaScript to set coordinates)
            $browser->script([
                'document.getElementById("store_location_lat").value = "25.1972";',
                'document.getElementById("store_location_lng").value = "55.2796";',
                'document.getElementById("store_location_address").value = "Dubai Mall, Financial Centre Road, Dubai";',
                'document.getElementById("selected-location-container").style.display = "block";'
            ]);

            // Upload required files
            $browser->attach('uae_id_front', storage_path('app/testing/sample_id_front.jpg'))
                    ->attach('uae_id_back', storage_path('app/testing/sample_id_back.jpg'));

            // Enable delivery capability
            $browser->check('delivery_capability')
                    ->waitFor('#delivery-fees', 2)
                    ->type('delivery_fees[within_city]', '15')
                    ->type('delivery_fees[outside_city]', '25');

            // Submit form
            $browser->click('#submitBtn')
                    ->waitForLocation('/merchant/email/verify/temp/*', 10)
                    ->assertSee('Email Verification Required');
        });
    }

    /**
     * Test Google Maps location selection and clearing.
     */
    public function test_google_maps_location_selection()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/merchant')
                    ->click('#location-search')
                    ->waitFor('#map-container', 5)
                    ->assertVisible('#google-map');

            // Test location search
            $browser->type('#location-search', 'Burj Khalifa, Dubai')
                    ->pause(2000);

            // Simulate location selection
            $browser->script([
                'document.getElementById("store_location_lat").value = "25.1972";',
                'document.getElementById("store_location_lng").value = "55.2796";',
                'document.getElementById("store_location_address").value = "Burj Khalifa, Dubai";',
                'document.getElementById("selected-location-container").style.display = "block";'
            ]);

            $browser->assertVisible('#selected-location-container')
                    ->assertInputValue('store_location_address', 'Burj Khalifa, Dubai')
                    ->assertInputValue('store_location_lat', '25.1972')
                    ->assertInputValue('store_location_lng', '55.2796');

            // Test clearing location
            $browser->click('.clear-location-btn')
                    ->assertInputValue('store_location_address', '')
                    ->assertInputValue('store_location_lat', '')
                    ->assertInputValue('store_location_lng', '')
                    ->assertNotVisible('#selected-location-container');
        });
    }

    /**
     * Test form validation with missing required fields.
     */
    public function test_merchant_registration_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/merchant')
                    ->click('#submitBtn')
                    ->waitFor('.error-message', 2)
                    ->assertSee('This field is required');

            // Test email validation
            $browser->type('email', 'invalid-email')
                    ->click('#submitBtn')
                    ->waitFor('.error-message', 2)
                    ->assertSee('Please enter a valid email address');

            // Test password confirmation
            $browser->type('email', 'test@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'different')
                    ->click('#submitBtn')
                    ->waitFor('.error-message', 2)
                    ->assertSee('Password confirmation does not match');
        });
    }

    /**
     * Test that vendor and provider registration flows remain unaffected.
     */
    public function test_other_registration_flows_unaffected()
    {
        $this->browse(function (Browser $browser) {
            // Test vendor registration
            $browser->visit('/register')
                    ->click('@vendor-registration-link')
                    ->assertPathIs('/register/vendor')
                    ->assertSee('Vendor Registration');

            // Test provider registration
            $browser->visit('/register')
                    ->click('@provider-registration-link')
                    ->assertPathIs('/register/provider')
                    ->assertSee('Provider Registration');

            // Verify registration choice page works
            $browser->visit('/register')
                    ->assertSee('Choose Your Registration Type')
                    ->assertVisible('@vendor-registration-link')
                    ->assertVisible('@provider-registration-link')
                    ->assertVisible('@merchant-registration-link');
        });
    }

    /**
     * Test Google Maps fallback when API fails.
     */
    public function test_google_maps_fallback()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/merchant');

            // Simulate Google Maps API failure
            $browser->script(['window.gm_authFailure();']);

            $browser->waitFor('#map-container', 2)
                    ->assertSee('Google Maps is currently unavailable')
                    ->assertSee('You can still enter your address manually');

            // Test manual address entry
            $browser->type('#location-search', 'Manual Address Entry Test')
                    ->assertInputValue('store_location_address', 'Manual Address Entry Test');
        });
    }

    /**
     * Set up test files before running tests.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test image files
        $this->createTestFiles();
    }

    /**
     * Create sample test files for upload testing.
     */
    private function createTestFiles()
    {
        Storage::makeDirectory('testing');
        
        // Create sample image files (1x1 pixel images)
        $sampleImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        
        Storage::put('testing/sample_id_front.jpg', $sampleImage);
        Storage::put('testing/sample_id_back.jpg', $sampleImage);
        Storage::put('testing/sample_logo.jpg', $sampleImage);
    }

    /**
     * Clean up test files after running tests.
     */
    protected function tearDown(): void
    {
        Storage::deleteDirectory('testing');
        parent::tearDown();
    }
}
