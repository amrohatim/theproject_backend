<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Merchant;
use App\Services\LicenseManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LicenseUploadServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $merchant;
    protected $user;
    protected $licenseService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'role' => 'merchant',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'registration_step' => 'verified',
            'status' => 'active',
        ]);

        // Create a test merchant
        $this->merchant = Merchant::factory()->create([
            'user_id' => $this->user->id,
            'business_name' => 'Test Business',
            'is_verified' => true,
            'status' => 'active',
        ]);

        $this->licenseService = new LicenseManagementService();

        // Fake the storage
        Storage::fake('public');
    }

    /** @test */
    public function it_can_upload_license_file_successfully()
    {
        // Create a fake PDF file
        $file = UploadedFile::fake()->create('license.pdf', 1000, 'application/pdf');
        $expiryDate = '2025-12-31';

        // Test the upload
        $result = $this->licenseService->uploadLicense($this->merchant, $file, $expiryDate);

        // Assert the result
        $this->assertTrue($result);

        // Refresh the merchant to get updated data
        $this->merchant->refresh();

        // Assert the merchant was updated
        $this->assertNotNull($this->merchant->license_file);
        $this->assertEquals($expiryDate, $this->merchant->license_expiry_date->format('Y-m-d'));
        $this->assertEquals('checking', $this->merchant->license_status);
        $this->assertFalse($this->merchant->license_verified);
        $this->assertNotNull($this->merchant->license_uploaded_at);

        // Assert the file was stored
        $this->assertTrue(Storage::disk('public')->exists($this->merchant->license_file));
    }

    /** @test */
    public function it_replaces_old_license_file_when_uploading_new_one()
    {
        // Upload first license
        $oldFile = UploadedFile::fake()->create('old_license.pdf', 1000, 'application/pdf');
        $this->licenseService->uploadLicense($this->merchant, $oldFile, '2025-12-31');
        
        $this->merchant->refresh();
        $oldLicensePath = $this->merchant->license_file;

        // Upload new license
        $newFile = UploadedFile::fake()->create('new_license.pdf', 1000, 'application/pdf');
        $result = $this->licenseService->uploadLicense($this->merchant, $newFile, '2026-12-31');

        $this->assertTrue($result);
        $this->merchant->refresh();

        // Assert old file was deleted and new file was stored
        $this->assertFalse(Storage::disk('public')->exists($oldLicensePath));
        $this->assertTrue(Storage::disk('public')->exists($this->merchant->license_file));
        $this->assertNotEquals($oldLicensePath, $this->merchant->license_file);
    }

    /** @test */
    public function it_handles_upload_errors_gracefully()
    {
        // Create a mock file that will cause an error
        $file = $this->createMock(UploadedFile::class);
        $file->method('storeAs')->willThrowException(new \Exception('Storage error'));

        $result = $this->licenseService->uploadLicense($this->merchant, $file, '2025-12-31');

        $this->assertFalse($result);
        
        // Merchant should not be updated
        $this->merchant->refresh();
        $this->assertNull($this->merchant->license_file);
    }

    /** @test */
    public function it_generates_unique_filenames()
    {
        $file1 = UploadedFile::fake()->create('license1.pdf', 1000, 'application/pdf');
        $file2 = UploadedFile::fake()->create('license2.pdf', 1000, 'application/pdf');

        $this->licenseService->uploadLicense($this->merchant, $file1, '2025-12-31');
        $this->merchant->refresh();
        $firstPath = $this->merchant->license_file;

        // Wait a second to ensure different timestamp
        sleep(1);

        $this->licenseService->uploadLicense($this->merchant, $file2, '2025-12-31');
        $this->merchant->refresh();
        $secondPath = $this->merchant->license_file;

        $this->assertNotEquals($firstPath, $secondPath);
    }
}
