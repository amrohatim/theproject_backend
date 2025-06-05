<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Company;

class BranchImageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a fake disk for testing
        Storage::fake('public');

        // Create a user
        $this->user = User::factory()->create([
            'role' => 'vendor',
        ]);

        // Create a company
        $this->company = Company::factory()->create([
            'user_id' => $this->user->id,
            'logo' => 'companies/company-logo.jpg',
        ]);
    }

    /** @test */
    public function a_branch_can_have_its_own_image()
    {
        $this->actingAs($this->user);

        // Create a fake image
        $file = UploadedFile::fake()->image('branch-image.jpg', 400, 400);

        // Submit the form to create a branch with an image
        $response = $this->post(route('vendor.branches.store'), [
            'name' => 'Test Branch',
            'company_id' => $this->company->id,
            'address' => '123 Test Street',
            'status' => 'active',
            'branch_image' => $file,
            'use_company_image' => false,
        ]);

        // Assert the branch was created
        $this->assertDatabaseHas('branches', [
            'name' => 'Test Branch',
            'company_id' => $this->company->id,
        ]);

        // Get the created branch
        $branch = Branch::where('name', 'Test Branch')->first();

        // Assert the branch has an image
        $this->assertNotNull($branch->branch_image);

        // Assert the image was stored
        Storage::disk('public')->assertExists($branch->branch_image);
    }

    /** @test */
    public function a_branch_can_use_company_image()
    {
        $this->actingAs($this->user);

        // Submit the form to create a branch with use_company_image set to true
        $response = $this->post(route('vendor.branches.store'), [
            'name' => 'Company Image Branch',
            'company_id' => $this->company->id,
            'address' => '456 Test Avenue',
            'status' => 'active',
            'use_company_image' => true,
        ]);

        // Assert the branch was created
        $this->assertDatabaseHas('branches', [
            'name' => 'Company Image Branch',
            'company_id' => $this->company->id,
            'use_company_image' => true,
        ]);

        // Get the created branch
        $branch = Branch::where('name', 'Company Image Branch')->first();

        // Assert the branch is using the company image
        $this->assertTrue($branch->use_company_image);
        $this->assertNull($branch->branch_image);
    }

    /** @test */
    public function a_branch_image_can_be_updated()
    {
        $this->actingAs($this->user);

        // Create a branch with an image
        $branch = Branch::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'branch_image' => 'branches/old-image.jpg',
            'use_company_image' => false,
        ]);

        // Create a fake image for update
        $file = UploadedFile::fake()->image('new-branch-image.jpg', 400, 400);

        // Submit the form to update the branch with a new image
        $response = $this->put(route('vendor.branches.update', $branch->id), [
            'name' => $branch->name,
            'company_id' => $branch->company_id,
            'address' => $branch->address,
            'status' => $branch->status,
            'branch_image' => $file,
            'use_company_image' => false,
        ]);

        // Refresh the branch from the database
        $branch->refresh();

        // Assert the branch image was updated
        $this->assertNotNull($branch->branch_image);
        $this->assertNotEquals('branches/old-image.jpg', $branch->branch_image);

        // Assert the new image was stored
        Storage::disk('public')->assertExists($branch->branch_image);
    }

    /** @test */
    public function switching_to_company_image_removes_branch_image()
    {
        $this->actingAs($this->user);

        // Create a branch with its own image
        $branch = Branch::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'branch_image' => 'branches/branch-image.jpg',
            'use_company_image' => false,
        ]);

        // Create the file in the fake storage
        Storage::disk('public')->put('branches/branch-image.jpg', 'test content');

        // Submit the form to update the branch to use company image
        $response = $this->put(route('vendor.branches.update', $branch->id), [
            'name' => $branch->name,
            'company_id' => $branch->company_id,
            'address' => $branch->address,
            'status' => $branch->status,
            'use_company_image' => true,
        ]);

        // Refresh the branch from the database
        $branch->refresh();

        // Assert the branch is now using the company image
        $this->assertTrue($branch->use_company_image);
        $this->assertNull($branch->branch_image);

        // Assert the old image was deleted
        Storage::disk('public')->assertMissing('branches/branch-image.jpg');
    }
}
