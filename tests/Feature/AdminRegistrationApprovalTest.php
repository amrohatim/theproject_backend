<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Provider;
use App\Models\RegistrationApproval;

class AdminRegistrationApprovalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $pendingVendor;
    protected $pendingProvider;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create pending vendor with registration approval
        $this->pendingVendor = User::factory()->create([
            'role' => 'vendor',
            'status' => 'pending',
        ]);

        Company::factory()->create([
            'user_id' => $this->pendingVendor->id,
            'status' => 'pending',
        ]);

        RegistrationApproval::create([
            'user_id' => $this->pendingVendor->id,
            'user_type' => 'vendor',
            'status' => 'pending',
            'registration_data' => [
                'vendor_info' => [
                    'name' => $this->pendingVendor->name,
                    'email' => $this->pendingVendor->email,
                    'phone' => $this->pendingVendor->phone,
                ],
                'company_info' => [
                    'company_name' => 'Test Company',
                    'description' => 'Test company description',
                ],
            ],
            'license_file_path' => 'licenses/test-license.pdf',
        ]);

        // Create pending provider with registration approval
        $this->pendingProvider = User::factory()->create([
            'role' => 'provider',
            'status' => 'pending',
        ]);

        Provider::factory()->create([
            'user_id' => $this->pendingProvider->id,
            'status' => 'pending',
        ]);

        RegistrationApproval::create([
            'user_id' => $this->pendingProvider->id,
            'user_type' => 'provider',
            'status' => 'pending',
            'registration_data' => [
                'provider_info' => [
                    'name' => $this->pendingProvider->name,
                    'email' => $this->pendingProvider->email,
                    'phone' => $this->pendingProvider->phone,
                    'delivery_capability' => true,
                    'stock_locations' => [
                        [
                            'name' => 'Main Warehouse',
                            'address' => '123 Test St',
                            'latitude' => 25.2048,
                            'longitude' => 55.2708,
                        ]
                    ],
                ],
            ],
            'license_file_path' => 'licenses/test-provider-license.pdf',
        ]);
    }

    /** @test */
    public function admin_can_view_pending_registrations()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.registrations.index'));

        $response->assertStatus(200);
        $response->assertSee($this->pendingVendor->name);
        $response->assertSee($this->pendingProvider->name);
        $response->assertSee('Pending');
    }

    /** @test */
    public function admin_can_filter_registrations_by_status()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.registrations.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSee($this->pendingVendor->name);
        $response->assertSee($this->pendingProvider->name);
    }

    /** @test */
    public function admin_can_filter_registrations_by_type()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.registrations.index', ['type' => 'vendor']));

        $response->assertStatus(200);
        $response->assertSee($this->pendingVendor->name);
        $response->assertDontSee($this->pendingProvider->name);
    }

    /** @test */
    public function admin_can_view_individual_registration_details()
    {
        $registration = RegistrationApproval::where('user_id', $this->pendingVendor->id)->first();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.registrations.show', $registration->id));

        $response->assertStatus(200);
        $response->assertSee($this->pendingVendor->name);
        $response->assertSee($this->pendingVendor->email);
        $response->assertSee('Test Company');
    }

    /** @test */
    public function admin_can_approve_vendor_registration()
    {
        Mail::fake();

        $registration = RegistrationApproval::where('user_id', $this->pendingVendor->id)->first();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.registrations.approve', $registration->id), [
                'admin_message' => 'Welcome to our marketplace!',
            ]);

        $response->assertRedirect(route('admin.registrations.index'));
        $response->assertSessionHas('success');

        // Check that user status was updated
        $this->pendingVendor->refresh();
        $this->assertEquals('active', $this->pendingVendor->status);

        // Check that company status was updated
        $company = Company::where('user_id', $this->pendingVendor->id)->first();
        $this->assertEquals('active', $company->status);

        // Check that registration approval was updated
        $registration->refresh();
        $this->assertEquals('approved', $registration->status);
        $this->assertEquals('Welcome to our marketplace!', $registration->admin_message);
        $this->assertEquals($this->admin->id, $registration->reviewed_by);
        $this->assertNotNull($registration->reviewed_at);

        // Check that email was sent
        Mail::assertSent(\Illuminate\Mail\Mailable::class);
    }

    /** @test */
    public function admin_can_approve_provider_registration()
    {
        Mail::fake();

        $registration = RegistrationApproval::where('user_id', $this->pendingProvider->id)->first();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.registrations.approve', $registration->id), [
                'admin_message' => 'Welcome to our provider network!',
            ]);

        $response->assertRedirect(route('admin.registrations.index'));
        $response->assertSessionHas('success');

        // Check that user status was updated
        $this->pendingProvider->refresh();
        $this->assertEquals('active', $this->pendingProvider->status);

        // Check that provider status was updated
        $provider = Provider::where('user_id', $this->pendingProvider->id)->first();
        $this->assertEquals('active', $provider->status);
        $this->assertTrue($provider->is_verified);

        // Check that registration approval was updated
        $registration->refresh();
        $this->assertEquals('approved', $registration->status);
        $this->assertEquals('Welcome to our provider network!', $registration->admin_message);
        $this->assertEquals($this->admin->id, $registration->reviewed_by);
        $this->assertNotNull($registration->reviewed_at);

        // Check that email was sent
        Mail::assertSent(\Illuminate\Mail\Mailable::class);
    }

    /** @test */
    public function admin_can_decline_registration()
    {
        Mail::fake();

        $registration = RegistrationApproval::where('user_id', $this->pendingVendor->id)->first();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.registrations.decline', $registration->id), [
                'admin_message' => 'Your documents do not meet our requirements.',
            ]);

        $response->assertRedirect(route('admin.registrations.index'));
        $response->assertSessionHas('success');

        // Check that user status was updated
        $this->pendingVendor->refresh();
        $this->assertEquals('declined', $this->pendingVendor->status);

        // Check that company status was updated
        $company = Company::where('user_id', $this->pendingVendor->id)->first();
        $this->assertEquals('declined', $company->status);

        // Check that registration approval was updated
        $registration->refresh();
        $this->assertEquals('declined', $registration->status);
        $this->assertEquals('Your documents do not meet our requirements.', $registration->admin_message);
        $this->assertEquals($this->admin->id, $registration->reviewed_by);
        $this->assertNotNull($registration->reviewed_at);

        // Check that email was sent
        Mail::assertSent(\Illuminate\Mail\Mailable::class);
    }

    /** @test */
    public function admin_cannot_review_already_reviewed_registration()
    {
        $registration = RegistrationApproval::where('user_id', $this->pendingVendor->id)->first();
        $registration->update([
            'status' => 'approved',
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.registrations.approve', $registration->id), [
                'admin_message' => 'Already reviewed',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function decline_requires_admin_message()
    {
        $registration = RegistrationApproval::where('user_id', $this->pendingVendor->id)->first();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.registrations.decline', $registration->id), [
                'admin_message' => '',
            ]);

        $response->assertSessionHasErrors(['admin_message']);
    }

    /** @test */
    public function admin_can_bulk_approve_registrations()
    {
        Mail::fake();

        $vendorRegistration = RegistrationApproval::where('user_id', $this->pendingVendor->id)->first();
        $providerRegistration = RegistrationApproval::where('user_id', $this->pendingProvider->id)->first();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.registrations.bulk-approve'), [
                'registration_ids' => [$vendorRegistration->id, $providerRegistration->id],
                'admin_message' => 'Bulk approval message',
            ]);

        $response->assertRedirect(route('admin.registrations.index'));
        $response->assertSessionHas('success');

        // Check that both registrations were approved
        $vendorRegistration->refresh();
        $providerRegistration->refresh();

        $this->assertEquals('approved', $vendorRegistration->status);
        $this->assertEquals('approved', $providerRegistration->status);

        // Check that users were activated
        $this->pendingVendor->refresh();
        $this->pendingProvider->refresh();

        $this->assertEquals('active', $this->pendingVendor->status);
        $this->assertEquals('active', $this->pendingProvider->status);

        // Check that emails were sent
        Mail::assertSent(\Illuminate\Mail\Mailable::class, 2);
    }

    /** @test */
    public function non_admin_cannot_access_registration_approval_routes()
    {
        $regularUser = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);

        $registration = RegistrationApproval::where('user_id', $this->pendingVendor->id)->first();

        // Test index route
        $response = $this->actingAs($regularUser)
            ->get(route('admin.registrations.index'));
        $response->assertStatus(403);

        // Test approve route
        $response = $this->actingAs($regularUser)
            ->post(route('admin.registrations.approve', $registration->id));
        $response->assertStatus(403);

        // Test decline route
        $response = $this->actingAs($regularUser)
            ->post(route('admin.registrations.decline', $registration->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function registration_approval_stats_api_works()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.registrations.stats'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'pending',
            'approved',
            'declined',
            'vendors_pending',
            'providers_pending',
        ]);

        $data = $response->json();
        $this->assertEquals(2, $data['pending']);
        $this->assertEquals(1, $data['vendors_pending']);
        $this->assertEquals(1, $data['providers_pending']);
    }
}
