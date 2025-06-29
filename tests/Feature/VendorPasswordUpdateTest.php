<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VendorPasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that vendor can update password with correct current password.
     */
    public function test_vendor_can_update_password_with_correct_current_password()
    {
        // Create a vendor user
        $vendor = User::factory()->create([
            'role' => 'vendor',
            'password' => Hash::make('oldpassword123'),
        ]);

        // Act as the vendor
        $this->actingAs($vendor);

        // Attempt to update password
        $response = $this->put(route('vendor.settings.security.update'), [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Assert redirect to security page with success message
        $response->assertRedirect(route('vendor.settings.security'));
        $response->assertSessionHas('success', 'Password updated successfully.');

        // Assert password was actually changed
        $vendor->refresh();
        $this->assertTrue(Hash::check('newpassword123', $vendor->password));
    }

    /**
     * Test that vendor cannot update password with incorrect current password.
     */
    public function test_vendor_cannot_update_password_with_incorrect_current_password()
    {
        // Create a vendor user
        $vendor = User::factory()->create([
            'role' => 'vendor',
            'password' => Hash::make('oldpassword123'),
        ]);

        // Act as the vendor
        $this->actingAs($vendor);

        // Attempt to update password with wrong current password
        $response = $this->put(route('vendor.settings.security.update'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Assert redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors(['current_password' => 'The current password is incorrect.']);

        // Assert password was not changed
        $vendor->refresh();
        $this->assertTrue(Hash::check('oldpassword123', $vendor->password));
    }

    /**
     * Test that password confirmation is required.
     */
    public function test_password_confirmation_is_required()
    {
        // Create a vendor user
        $vendor = User::factory()->create([
            'role' => 'vendor',
            'password' => Hash::make('oldpassword123'),
        ]);

        // Act as the vendor
        $this->actingAs($vendor);

        // Attempt to update password without confirmation
        $response = $this->put(route('vendor.settings.security.update'), [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            // Missing password_confirmation
        ]);

        // Assert validation error
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test that minimum password length is enforced.
     */
    public function test_minimum_password_length_is_enforced()
    {
        // Create a vendor user
        $vendor = User::factory()->create([
            'role' => 'vendor',
            'password' => Hash::make('oldpassword123'),
        ]);

        // Act as the vendor
        $this->actingAs($vendor);

        // Attempt to update password with short password
        $response = $this->put(route('vendor.settings.security.update'), [
            'current_password' => 'oldpassword123',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        // Assert validation error
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test that non-vendor users cannot access the route.
     */
    public function test_non_vendor_users_cannot_update_password()
    {
        // Create a customer user
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        // Act as the customer
        $this->actingAs($customer);

        // Attempt to update password
        $response = $this->put(route('vendor.settings.security.update'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Assert redirect (vendor middleware should block this)
        $response->assertRedirect('/');
    }
}
