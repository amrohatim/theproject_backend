<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Provider;
use App\Models\ProviderLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProviderLocationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $provider;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user and provider
        $this->user = User::factory()->create([
            'email' => 'provider@test.com',
            'password' => bcrypt('password'),
        ]);
        
        $this->provider = Provider::create([
            'user_id' => $this->user->id,
            'business_name' => 'Test Business',
            'company_name' => 'Test Company',
            'status' => 'active',
            'is_verified' => false
        ]);
    }

    /** @test */
    public function it_creates_new_locations_without_duplicating_existing_ones()
    {
        // Create an existing location
        $existingLocation = ProviderLocation::create([
            'provider_id' => $this->provider->id,
            'label' => 'Existing Location',
            'emirate' => 'Dubai',
            'latitude' => 25.2048,
            'longitude' => 55.2708,
        ]);

        // Prepare request data with existing location (with ID) and new location (without ID)
        $requestData = [
            'locations' => [
                [
                    'id' => $existingLocation->id,
                    'label' => 'Updated Existing Location',
                    'emirate' => 'Dubai',
                    'latitude' => 25.2048,
                    'longitude' => 55.2708,
                ],
                [
                    'id' => null,
                    'label' => 'New Location',
                    'emirate' => 'Abu Dhabi',
                    'latitude' => 24.4539,
                    'longitude' => 54.3773,
                ]
            ]
        ];

        // Act as the authenticated user
        $response = $this->actingAs($this->user)
            ->postJson(route('provider.locations.store'), $requestData);

        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Locations saved successfully'
            ]);

        // Assert that we still have only 2 locations total (no duplicates)
        $this->assertEquals(2, ProviderLocation::where('provider_id', $this->provider->id)->count());

        // Assert the existing location was updated, not duplicated
        $updatedLocation = ProviderLocation::find($existingLocation->id);
        $this->assertEquals('Updated Existing Location', $updatedLocation->label);
        $this->assertEquals('Dubai', $updatedLocation->emirate);

        // Assert the new location was created
        $newLocation = ProviderLocation::where('label', 'New Location')->first();
        $this->assertNotNull($newLocation);
        $this->assertEquals('Abu Dhabi', $newLocation->emirate);
        $this->assertEquals(24.4539, $newLocation->latitude);
        $this->assertEquals(54.3773, $newLocation->longitude);
    }

    /** @test */
    public function it_only_creates_new_locations_when_no_existing_locations()
    {
        // Prepare request data with only new locations (no IDs)
        $requestData = [
            'locations' => [
                [
                    'id' => null,
                    'label' => 'First Location',
                    'emirate' => 'Dubai',
                    'latitude' => 25.2048,
                    'longitude' => 55.2708,
                ],
                [
                    'id' => null,
                    'label' => 'Second Location',
                    'emirate' => 'Sharjah',
                    'latitude' => 25.3463,
                    'longitude' => 55.4209,
                ]
            ]
        ];

        // Act as the authenticated user
        $response = $this->actingAs($this->user)
            ->postJson(route('provider.locations.store'), $requestData);

        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Locations saved successfully'
            ]);

        // Assert that exactly 2 locations were created
        $this->assertEquals(2, ProviderLocation::where('provider_id', $this->provider->id)->count());

        // Assert both locations exist with correct data
        $this->assertDatabaseHas('provider_locations', [
            'provider_id' => $this->provider->id,
            'label' => 'First Location',
            'emirate' => 'Dubai'
        ]);

        $this->assertDatabaseHas('provider_locations', [
            'provider_id' => $this->provider->id,
            'label' => 'Second Location',
            'emirate' => 'Sharjah'
        ]);
    }

    /** @test */
    public function it_validates_location_data_properly()
    {
        // Test with invalid data
        $requestData = [
            'locations' => [
                [
                    'id' => null,
                    'label' => 'Test Location',
                    // Missing required emirate
                    'latitude' => 'invalid_latitude',
                    'longitude' => 55.2708,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('provider.locations.store'), $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['locations.0.emirate', 'locations.0.latitude']);
    }

    /** @test */
    public function it_prevents_updating_locations_from_other_providers()
    {
        // Create another provider and location
        $otherUser = User::factory()->create();
        $otherProvider = Provider::create([
            'user_id' => $otherUser->id,
            'business_name' => 'Other Business',
            'company_name' => 'Other Company',
            'status' => 'active',
            'is_verified' => false
        ]);

        $otherLocation = ProviderLocation::create([
            'provider_id' => $otherProvider->id,
            'label' => 'Other Provider Location',
            'emirate' => 'Dubai',
            'latitude' => 25.2048,
            'longitude' => 55.2708,
        ]);

        // Try to update the other provider's location
        $requestData = [
            'locations' => [
                [
                    'id' => $otherLocation->id,
                    'label' => 'Hacked Location',
                    'emirate' => 'Dubai',
                    'latitude' => 25.2048,
                    'longitude' => 55.2708,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('provider.locations.store'), $requestData);

        // Should still succeed but not update the other provider's location
        $response->assertStatus(200);

        // Assert the other provider's location was not modified
        $otherLocation->refresh();
        $this->assertEquals('Other Provider Location', $otherLocation->label);

        // Assert no location was created for our provider
        $this->assertEquals(0, ProviderLocation::where('provider_id', $this->provider->id)->count());
    }
}
