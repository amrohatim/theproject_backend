<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserStatusMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function active_users_can_access_protected_routes()
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(200);
    }

    /** @test */
    public function pending_users_are_redirected_to_pending_page_for_web_requests()
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->get('/vendor/dashboard');

        $response->assertRedirect(route('pending-approval'));
    }

    /** @test */
    public function pending_users_get_json_response_for_api_requests()
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Your account is pending approval. Please wait for admin review.',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function declined_users_are_redirected_to_declined_page_for_web_requests()
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'status' => 'declined',
        ]);

        $response = $this->actingAs($user)
            ->get('/vendor/dashboard');

        $response->assertRedirect(route('registration-declined'));
    }

    /** @test */
    public function declined_users_get_json_response_for_api_requests()
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'status' => 'declined',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Your account registration has been declined. Please contact support.',
            'status' => 'declined',
        ]);
    }

    /** @test */
    public function inactive_users_are_logged_out_for_web_requests()
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($user)
            ->get('/vendor/dashboard');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /** @test */
    public function inactive_users_get_json_response_for_api_requests()
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Your account has been deactivated. Please contact support.',
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function admin_users_bypass_status_checks()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'pending', // Even with pending status
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_users_are_not_affected()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /** @test */
    public function pending_approval_page_only_accessible_to_pending_users()
    {
        // Test with active user
        $activeUser = User::factory()->create([
            'role' => 'vendor',
            'status' => 'active',
        ]);

        $response = $this->actingAs($activeUser)
            ->get(route('pending-approval'));

        $response->assertRedirect('/');

        // Test with pending user
        $pendingUser = User::factory()->create([
            'role' => 'vendor',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($pendingUser)
            ->get(route('pending-approval'));

        $response->assertStatus(200);
        $response->assertSee('Account Pending Approval');
    }

    /** @test */
    public function registration_declined_page_only_accessible_to_declined_users()
    {
        // Test with active user
        $activeUser = User::factory()->create([
            'role' => 'vendor',
            'status' => 'active',
        ]);

        $response = $this->actingAs($activeUser)
            ->get(route('registration-declined'));

        $response->assertRedirect('/');

        // Test with declined user
        $declinedUser = User::factory()->create([
            'role' => 'vendor',
            'status' => 'declined',
        ]);

        $response = $this->actingAs($declinedUser)
            ->get(route('registration-declined'));

        $response->assertStatus(200);
        $response->assertSee('Registration Declined');
    }

    /** @test */
    public function registration_pages_show_user_information()
    {
        $pendingUser = User::factory()->create([
            'name' => 'John Pending Vendor',
            'email' => 'john@pending.com',
            'role' => 'vendor',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($pendingUser)
            ->get(route('pending-approval'));

        $response->assertStatus(200);
        $response->assertSee('John Pending Vendor');
        $response->assertSee('john@pending.com');
        $response->assertSee('Vendor');
    }

    /** @test */
    public function middleware_works_with_different_user_roles()
    {
        // Test vendor
        $pendingVendor = User::factory()->create([
            'role' => 'vendor',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($pendingVendor, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(403);

        // Test provider
        $pendingProvider = User::factory()->create([
            'role' => 'provider',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($pendingProvider, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(403);

        // Test customer (should work normally)
        $activeCustomer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);

        $response = $this->actingAs($activeCustomer, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(200);
    }
}
