<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VendorBranchCoordinatesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $vendor;
    private $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a vendor user
        $this->vendor = User::factory()->create([
            'role' => 'vendor',
            'email' => 'vendor@test.com',
        ]);

        // Create a company for the vendor
        $this->company = Company::factory()->create([
            'user_id' => $this->vendor->id,
            'name' => 'Test Company',
        ]);
    }

    /** @test */
    public function vendor_can_create_branch_with_coordinates()
    {
        $this->actingAs($this->vendor);

        $branchData = [
            'name' => 'Test Branch',
            'company_id' => $this->company->id,
            'address' => 'Test Address, Dubai, UAE',
            'lat' => 25.2048,
            'lng' => 55.2708,
            'emirate' => 'Dubai',
            'phone' => '+971501234567',
            'email' => 'branch@test.com',
            'description' => 'Test branch description',
            'status' => 'active',
            'use_company_image' => true,
        ];

        $response = $this->post(route('vendor.branches.store'), $branchData);

        $response->assertRedirect(route('vendor.branches.index'));
        $response->assertSessionHas('success', 'Branch created successfully');

        // Verify the branch was created with correct coordinates
        $this->assertDatabaseHas('branches', [
            'name' => 'Test Branch',
            'lat' => 25.2048,
            'lng' => 55.2708,
            'address' => 'Test Address, Dubai, UAE',
            'company_id' => $this->company->id,
        ]);

        // Get the created branch and verify coordinates are stored correctly
        $branch = Branch::where('name', 'Test Branch')->first();
        $this->assertNotNull($branch);
        $this->assertEquals(25.2048, (float) $branch->lat);
        $this->assertEquals(55.2708, (float) $branch->lng);
    }

    /** @test */
    public function vendor_can_update_branch_coordinates()
    {
        $this->actingAs($this->vendor);

        // Create a branch first
        $branch = Branch::factory()->create([
            'user_id' => $this->vendor->id,
            'company_id' => $this->company->id,
            'name' => 'Original Branch',
            'lat' => 25.0000,
            'lng' => 55.0000,
        ]);

        $updateData = [
            'name' => 'Updated Branch',
            'company_id' => $this->company->id,
            'address' => 'Updated Address, Abu Dhabi, UAE',
            'lat' => 24.4539,
            'lng' => 54.3773,
            'emirate' => 'Abu Dhabi',
            'phone' => '+971501234567',
            'email' => 'updated@test.com',
            'description' => 'Updated description',
            'status' => 'active',
            'use_company_image' => true,
        ];

        $response = $this->put(route('vendor.branches.update', $branch->id), $updateData);

        $response->assertRedirect(route('vendor.branches.show', $branch->id));
        $response->assertSessionHas('success', 'Branch updated successfully');

        // Verify the branch was updated with correct coordinates
        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Updated Branch',
            'lat' => 24.4539,
            'lng' => 54.3773,
            'address' => 'Updated Address, Abu Dhabi, UAE',
        ]);

        // Refresh the branch model and verify coordinates
        $branch->refresh();
        $this->assertEquals(24.4539, (float) $branch->lat);
        $this->assertEquals(54.3773, (float) $branch->lng);
    }

    /** @test */
    public function branch_creation_fails_without_coordinates()
    {
        $this->actingAs($this->vendor);

        $branchData = [
            'name' => 'Test Branch',
            'company_id' => $this->company->id,
            'address' => 'Test Address, Dubai, UAE',
            // Missing lat and lng
            'emirate' => 'Dubai',
            'status' => 'active',
        ];

        $response = $this->post(route('vendor.branches.store'), $branchData);

        $response->assertSessionHasErrors(['lat', 'lng']);
        
        // Verify no branch was created
        $this->assertDatabaseMissing('branches', [
            'name' => 'Test Branch',
        ]);
    }

    /** @test */
    public function branch_creation_fails_with_invalid_coordinates()
    {
        $this->actingAs($this->vendor);

        $branchData = [
            'name' => 'Test Branch',
            'company_id' => $this->company->id,
            'address' => 'Test Address, Dubai, UAE',
            'lat' => 91.0, // Invalid latitude (must be between -90 and 90)
            'lng' => 181.0, // Invalid longitude (must be between -180 and 180)
            'emirate' => 'Dubai',
            'status' => 'active',
        ];

        $response = $this->post(route('vendor.branches.store'), $branchData);

        $response->assertSessionHasErrors(['lat', 'lng']);
        
        // Verify no branch was created
        $this->assertDatabaseMissing('branches', [
            'name' => 'Test Branch',
        ]);
    }
}
