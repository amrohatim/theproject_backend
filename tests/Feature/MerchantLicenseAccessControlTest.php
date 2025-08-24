<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Merchant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MerchantLicenseAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a merchant user for testing
        $this->user = User::factory()->create([
            'role' => 'merchant',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'registration_step' => 'verified',
        ]);

        $this->merchant = Merchant::factory()->create([
            'user_id' => $this->user->id,
            'business_name' => 'Test Business',
            'is_verified' => true,
        ]);
    }

    /** @test */
    public function merchant_with_active_status_can_access_dashboard()
    {
        $this->merchant->update([
            'status' => 'active',
            'license_status' => 'verified',
            'license_verified' => true,
            'license_expiry_date' => now()->addYear(),
        ]);

        $response = $this->actingAs($this->user)->get(route('merchant.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('merchant.dashboard');
    }

    /** @test */
    public function merchant_with_checking_license_is_redirected_to_status_page()
    {
        $this->merchant->update([
            'status' => 'pending',
            'license_status' => 'checking',
            'license_verified' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('merchant.dashboard'));

        $response->assertRedirect(route('merchant.license.status', ['status' => 'checking']));
        $response->assertSessionHas('license_status', 'checking');
    }

    /** @test */
    public function merchant_with_rejected_license_is_redirected_to_status_page()
    {
        $this->merchant->update([
            'status' => 'pending',
            'license_status' => 'rejected',
            'license_verified' => false,
            'license_rejection_reason' => 'Invalid document format',
        ]);

        $response = $this->actingAs($this->user)->get(route('merchant.dashboard'));

        $response->assertRedirect(route('merchant.license.status', ['status' => 'rejected']));
        $response->assertSessionHas('license_status', 'rejected');
    }

    /** @test */
    public function merchant_with_expired_license_is_redirected_to_status_page()
    {
        $this->merchant->update([
            'status' => 'pending',
            'license_status' => 'expired',
            'license_verified' => false,
            'license_expiry_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user)->get(route('merchant.dashboard'));

        $response->assertRedirect(route('merchant.license.status', ['status' => 'expired']));
        $response->assertSessionHas('license_status', 'expired');
    }

    /** @test */
    public function license_status_page_displays_correct_content_for_checking_status()
    {
        $this->merchant->update([
            'license_status' => 'checking',
        ]);

        $response = $this->actingAs($this->user)->get(route('merchant.license.status', ['status' => 'checking']));

        $response->assertStatus(200);
        $response->assertViewIs('merchant.license.status');
        $response->assertSee('License Under Review');
        $response->assertSee('Your license is currently under review');
    }

    /** @test */
    public function license_status_page_displays_correct_content_for_rejected_status()
    {
        $this->merchant->update([
            'license_status' => 'rejected',
            'license_rejection_reason' => 'Invalid document format',
        ]);

        $response = $this->actingAs($this->user)->get(route('merchant.license.status', ['status' => 'rejected']));

        $response->assertStatus(200);
        $response->assertViewIs('merchant.license.status');
        $response->assertSee('License Rejected');
        $response->assertSee('Invalid document format');
        $response->assertSee('Upload New License');
    }

    /** @test */
    public function license_status_page_displays_correct_content_for_expired_status()
    {
        $this->merchant->update([
            'license_status' => 'expired',
            'license_expiry_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user)->get(route('merchant.license.status', ['status' => 'expired']));

        $response->assertStatus(200);
        $response->assertViewIs('merchant.license.status');
        $response->assertSee('License Expired');
        $response->assertSee('Renew License');
    }
}
