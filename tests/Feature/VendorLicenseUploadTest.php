<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VendorLicenseUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_upload_license_with_user_id()
    {
        // Create a vendor user
        $user = User::factory()->create([
            'user_type' => 'vendor',
            'email' => 'vendor@test.com'
        ]);

        // Create a company for the user
        Company::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Company'
        ]);

        // Create a fake PDF file
        $file = UploadedFile::fake()->create('license.pdf', 1000, 'application/pdf');

        $response = $this->postJson('/api/vendor-registration/license', [
            'user_id' => $user->id,
            'license_file' => $file,
            'license_start_date' => now()->format('Y-m-d'),
            'license_expiry_date' => now()->addYear()->format('Y-m-d'),
            'notes' => 'Test license upload'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $user = User::factory()->create(['user_type' => 'vendor']);

        $response = $this->postJson('/api/vendor-registration/license', [
            'user_id' => $user->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'license_file',
                    'license_start_date',
                    'license_expiry_date'
                ]);
    }

    /** @test */
    public function it_validates_file_type()
    {
        $user = User::factory()->create(['user_type' => 'vendor']);
        $file = UploadedFile::fake()->create('document.txt', 1000, 'text/plain');

        $response = $this->postJson('/api/vendor-registration/license', [
            'user_id' => $user->id,
            'license_file' => $file,
            'license_start_date' => now()->format('Y-m-d'),
            'license_expiry_date' => now()->addYear()->format('Y-m-d')
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['license_file']);
    }

    /** @test */
    public function it_validates_date_logic()
    {
        $user = User::factory()->create(['user_type' => 'vendor']);
        $file = UploadedFile::fake()->create('license.pdf', 1000, 'application/pdf');

        $response = $this->postJson('/api/vendor-registration/license', [
            'user_id' => $user->id,
            'license_file' => $file,
            'license_start_date' => now()->addDay()->format('Y-m-d'),
            'license_expiry_date' => now()->format('Y-m-d') // Expiry before start
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['license_expiry_date']);
    }

    /** @test */
    public function it_handles_invalid_user()
    {
        $file = UploadedFile::fake()->create('license.pdf', 1000, 'application/pdf');

        $response = $this->postJson('/api/vendor-registration/license', [
            'user_id' => 999999, // Non-existent user
            'license_file' => $file,
            'license_start_date' => now()->format('Y-m-d'),
            'license_expiry_date' => now()->addYear()->format('Y-m-d')
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid vendor user. Please complete the registration process first.'
                ]);
    }

    /** @test */
    public function it_handles_session_based_upload()
    {
        // Simulate session-based registration
        $user = User::factory()->create([
            'user_type' => 'vendor',
            'email' => 'vendor@test.com'
        ]);

        // Set session data
        session(['vendor_license_upload' => ['user_id' => $user->id]]);

        $file = UploadedFile::fake()->create('license.pdf', 1000, 'application/pdf');

        $response = $this->postJson('/api/vendor-registration/license', [
            'license_file' => $file,
            'license_start_date' => now()->format('Y-m-d'),
            'license_expiry_date' => now()->addYear()->format('Y-m-d')
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }
}
