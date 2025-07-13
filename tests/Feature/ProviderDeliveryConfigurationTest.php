<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Provider;

class ProviderDeliveryConfigurationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_saves_delivery_capability_and_fees_during_registration()
    {
        $logo = UploadedFile::fake()->image('logo.jpg');

        $providerData = [
            'name' => 'Test Provider',
            'email' => 'test@provider.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
            'business_type' => 'Electronics',
            'description' => 'Test business description',
            'logo' => $logo,
            'delivery_capability' => true,
            'delivery_fee_by_emirate' => json_encode([
                'abu_dhabi' => 25.50,
                'dubai' => 20.00,
                'sharjah' => 30.00,
                'ajman' => 35.00,
                'uaq' => 40.00,
                'rak' => 45.00,
                'fujairah' => 50.00,
            ]),
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $providerData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                ]);

        // Verify the registration token is returned
        $this->assertArrayHasKey('registration_token', $response->json());
    }

    /** @test */
    public function it_validates_negative_delivery_fees()
    {
        $providerData = [
            'name' => 'Test Provider',
            'email' => 'test@provider.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
            'business_type' => 'Electronics',
            'delivery_capability' => true,
            'delivery_fee_by_emirate' => json_encode([
                'dubai' => -10.00, // Invalid negative fee
            ]),
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $providerData);

        $response->assertStatus(422);
        $this->assertStringContainsString('positive number', $response->json('message'));
    }

    /** @test */
    public function it_validates_invalid_emirate_names()
    {
        $providerData = [
            'name' => 'Test Provider',
            'email' => 'test@provider.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
            'business_type' => 'Electronics',
            'delivery_capability' => true,
            'delivery_fee_by_emirate' => json_encode([
                'invalid_emirate' => 25.00, // Invalid emirate name
            ]),
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $providerData);

        $response->assertStatus(422);
        $this->assertStringContainsString('Invalid emirate', $response->json('message'));
    }

    /** @test */
    public function it_allows_registration_without_delivery_capability()
    {
        $providerData = [
            'name' => 'Test Provider',
            'email' => 'test@provider.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
            'business_type' => 'Electronics',
            'delivery_capability' => false,
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $providerData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                ]);
    }

    /** @test */
    public function it_handles_malformed_delivery_fees_json()
    {
        $providerData = [
            'name' => 'Test Provider',
            'email' => 'test@provider.com',
            'phone' => '+971501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Test Business',
            'business_type' => 'Electronics',
            'delivery_capability' => true,
            'delivery_fee_by_emirate' => 'invalid json string',
        ];

        $response = $this->postJson('/api/provider/register/validate-info', $providerData);

        // Should still pass validation but delivery_fees will be null
        $response->assertStatus(201);
    }

    /** @test */
    public function provider_model_can_retrieve_delivery_fees_by_emirate()
    {
        $user = User::factory()->create();
        
        $provider = Provider::create([
            'user_id' => $user->id,
            'business_name' => 'Test Business',
            'business_type' => 'Electronics',
            'delivery_capability' => true,
            'delivery_fees' => [
                'abu_dhabi' => 25.50,
                'dubai' => 20.00,
                'sharjah' => 30.00,
            ],
            'status' => 'pending',
            'is_verified' => false,
        ]);

        // Test getting delivery fee for specific emirate
        $this->assertEquals(25.50, $provider->getDeliveryFeeForEmirate('abu_dhabi'));
        $this->assertEquals(20.00, $provider->getDeliveryFeeForEmirate('dubai'));
        $this->assertEquals(30.00, $provider->getDeliveryFeeForEmirate('sharjah'));
        $this->assertNull($provider->getDeliveryFeeForEmirate('ajman'));

        // Test delivery capability check
        $this->assertTrue($provider->offersDelivery());
    }

    /** @test */
    public function provider_without_delivery_capability_returns_null_fees()
    {
        $user = User::factory()->create();
        
        $provider = Provider::create([
            'user_id' => $user->id,
            'business_name' => 'Test Business',
            'business_type' => 'Electronics',
            'delivery_capability' => false,
            'status' => 'pending',
            'is_verified' => false,
        ]);

        // Should return null for any emirate when delivery is not offered
        $this->assertNull($provider->getDeliveryFeeForEmirate('dubai'));
        $this->assertFalse($provider->offersDelivery());
    }

    /** @test */
    public function delivery_fees_are_properly_cast_as_array()
    {
        $user = User::factory()->create();
        
        $deliveryFees = [
            'abu_dhabi' => 25.50,
            'dubai' => 20.00,
            'sharjah' => 30.00,
        ];

        $provider = Provider::create([
            'user_id' => $user->id,
            'business_name' => 'Test Business',
            'business_type' => 'Electronics',
            'delivery_capability' => true,
            'delivery_fees' => $deliveryFees,
            'status' => 'pending',
            'is_verified' => false,
        ]);

        // Refresh from database to test casting
        $provider->refresh();

        $this->assertIsArray($provider->delivery_fees);
        $this->assertEquals($deliveryFees, $provider->delivery_fees);
    }
}
