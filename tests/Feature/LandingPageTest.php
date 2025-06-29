<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the landing page loads successfully for non-authenticated users.
     */
    public function test_landing_page_loads_for_guest_users(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('landing');
        $response->assertViewHas(['totalProducts', 'totalVendors', 'totalCustomers', 'satisfactionRate', 'isAuthenticated', 'userRole', 'getStartedUrl']);
        
        // Check that guest users see the login URL
        $response->assertViewHas('isAuthenticated', false);
        $response->assertViewHas('userRole', null);
        $response->assertSee('Get Started');
    }

    /**
     * Test that the landing page shows appropriate content for authenticated admin users.
     */
    public function test_landing_page_for_authenticated_admin(): void
    {
        // Create an admin user using factory
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
        ]);

        // Authenticate as admin
        $this->actingAs($admin);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('isAuthenticated', true);
        $response->assertViewHas('userRole', 'admin');
        $response->assertSee('Go to Admin Dashboard');
    }

    /**
     * Test that the landing page shows appropriate content for authenticated vendor users.
     */
    public function test_landing_page_for_authenticated_vendor(): void
    {
        // Create a vendor user using factory
        $vendor = User::factory()->vendor()->create([
            'email' => 'vendor@example.com',
        ]);

        // Authenticate as vendor
        $this->actingAs($vendor);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('isAuthenticated', true);
        $response->assertViewHas('userRole', 'vendor');
        $response->assertSee('Go to Vendor Dashboard');
    }

    /**
     * Test that the landing page shows appropriate content for authenticated provider users.
     */
    public function test_landing_page_for_authenticated_provider(): void
    {
        // Create a provider user using factory
        $provider = User::factory()->create([
            'role' => 'provider',
            'email' => 'provider@example.com',
        ]);

        // Authenticate as provider
        $this->actingAs($provider);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('isAuthenticated', true);
        $response->assertViewHas('userRole', 'provider');
        $response->assertSee('Go to Provider Dashboard');
    }

    /**
     * Test that the landing page shows appropriate content for authenticated customer users.
     */
    public function test_landing_page_for_authenticated_customer(): void
    {
        // Create a customer user
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer@example.com',
        ]);

        // Authenticate as customer
        $this->actingAs($customer);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('isAuthenticated', true);
        $response->assertViewHas('userRole', 'customer');
        $response->assertSee('Continue Shopping');
    }

    /**
     * Test the getGetStartedUrl method logic for different user types.
     */
    public function test_get_started_url_logic(): void
    {
        $controller = new \App\Http\Controllers\LandingController();
        
        // Use reflection to access the private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('getGetStartedUrl');
        $method->setAccessible(true);

        // Test non-authenticated user
        $url = $method->invoke($controller, false, null);
        $this->assertEquals(route('login'), $url);

        // Test admin user
        $url = $method->invoke($controller, true, 'admin');
        $this->assertEquals(route('admin.dashboard'), $url);

        // Test vendor user
        $url = $method->invoke($controller, true, 'vendor');
        $this->assertEquals(route('vendor.dashboard'), $url);

        // Test provider user
        $url = $method->invoke($controller, true, 'provider');
        $this->assertEquals(route('provider.dashboard'), $url);

        // Test customer user
        $url = $method->invoke($controller, true, 'customer');
        $this->assertEquals(url('/'), $url);
    }
}
