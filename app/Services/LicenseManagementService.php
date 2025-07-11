<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LicenseManagementService
{
    /**
     * Check if a merchant can perform license-required actions.
     */
    public function canPerformLicenseRequiredActions(Merchant $merchant): bool
    {
        return $merchant->hasValidLicense();
    }

    /**
     * Get license status summary for dashboard.
     */
    public function getLicenseStatusSummary(): array
    {
        return [
            'pending_review' => Merchant::where('license_status', 'checking')->count(),
            'approved' => Merchant::where('license_status', 'verified')->count(),
            'rejected' => Merchant::where('license_status', 'rejected')->count(),
            'expired' => Merchant::where('license_status', 'expired')->count(),
            'total_with_licenses' => Merchant::whereNotNull('license_file')->count(),
        ];
    }

    /**
     * Get merchants with licenses expiring soon.
     */
    public function getMerchantsWithExpiringLicenses(int $daysAhead = 30): \Illuminate\Database\Eloquent\Collection
    {
        $futureDate = Carbon::today()->addDays($daysAhead);
        
        return Merchant::where('license_verified', true)
            ->where('license_status', 'verified')
            ->whereBetween('license_expiry_date', [Carbon::today(), $futureDate])
            ->with('user')
            ->orderBy('license_expiry_date', 'asc')
            ->get();
    }

    /**
     * Get merchants with expired licenses.
     */
    public function getMerchantsWithExpiredLicenses(): \Illuminate\Database\Eloquent\Collection
    {
        return Merchant::where('license_verified', true)
            ->where('license_status', 'verified')
            ->where('license_expiry_date', '<', Carbon::today())
            ->with('user')
            ->orderBy('license_expiry_date', 'desc')
            ->get();
    }

    /**
     * Process license expiration for a merchant.
     */
    public function processLicenseExpiration(Merchant $merchant): bool
    {
        try {
            if (!$merchant->isLicenseExpired()) {
                return false; // License is not expired
            }

            $merchant->update([
                'license_verified' => false,
                'license_status' => 'expired',
                'is_verified' => false,
            ]);

            Log::info("License expired for merchant: {$merchant->business_name} (ID: {$merchant->id})");
            
            // TODO: Send notification to merchant about expired license
            // $this->sendLicenseExpirationNotification($merchant);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to process license expiration for merchant {$merchant->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve a merchant license.
     */
    public function approveLicense(Merchant $merchant, User $approvedBy, ?string $message = null): bool
    {
        try {
            $merchant->update([
                'license_status' => 'verified',
                'license_verified' => true,
                'license_rejection_reason' => null,
                'license_approved_at' => now(),
                'license_approved_by' => $approvedBy->id,
                'is_verified' => true,
            ]);

            Log::info("License approved for merchant: {$merchant->business_name} (ID: {$merchant->id}) by admin: {$approvedBy->name}");
            
            // TODO: Send approval notification to merchant
            // $this->sendLicenseApprovalNotification($merchant, $message);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to approve license for merchant {$merchant->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject a merchant license.
     */
    public function rejectLicense(Merchant $merchant, User $rejectedBy, string $reason): bool
    {
        try {
            $merchant->update([
                'license_status' => 'rejected',
                'license_verified' => false,
                'license_rejection_reason' => $reason,
                'license_approved_at' => null,
                'license_approved_by' => null,
                'is_verified' => false,
            ]);

            Log::info("License rejected for merchant: {$merchant->business_name} (ID: {$merchant->id}) by admin: {$rejectedBy->name}");
            
            // TODO: Send rejection notification to merchant
            // $this->sendLicenseRejectionNotification($merchant, $reason);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to reject license for merchant {$merchant->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload and process a new license file.
     */
    public function uploadLicense(Merchant $merchant, $file, string $expiryDate): bool
    {
        \Log::info('=== LicenseManagementService::uploadLicense STARTED ===');
        \Log::info('Merchant ID: ' . $merchant->id);
        \Log::info('Merchant business name: ' . $merchant->business_name);
        \Log::info('File info: ', [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'error' => $file->getError(),
            'is_valid' => $file->isValid(),
        ]);
        \Log::info('Expiry date: ' . $expiryDate);

        try {
            // Delete old license file if exists
            \Log::info('Checking for existing license file...');
            if ($merchant->license_file && Storage::exists('public/' . $merchant->license_file)) {
                \Log::info('Deleting old license file: ' . $merchant->license_file);
                Storage::delete('public/' . $merchant->license_file);
            } else {
                \Log::info('No existing license file to delete');
            }

            // Generate unique filename
            $filename = 'license_' . $merchant->id . '_' . time() . '.pdf';
            \Log::info('Generated filename: ' . $filename);

            // Ensure the merchant-licenses directory exists and is writable
            $targetDir = storage_path('app/public/merchant-licenses');
            if (!is_dir($targetDir)) {
                \Log::info('Creating merchant-licenses directory...');
                if (!mkdir($targetDir, 0755, true)) {
                    throw new \Exception('Failed to create merchant-licenses directory');
                }
            }

            if (!is_writable($targetDir)) {
                \Log::error('merchant-licenses directory is not writable');
                throw new \Exception('merchant-licenses directory is not writable');
            }

            // Store the new license file
            \Log::info('Storing file to merchant-licenses directory...');
            \Log::info('File details before storage:', [
                'filename' => $filename,
                'file_size' => $file->getSize(),
                'file_valid' => $file->isValid(),
                'file_error' => $file->getError(),
                'temp_path' => $file->getPathname(),
            ]);

            $licensePath = $file->storeAs('merchant-licenses', $filename, 'public');
            \Log::info('File stored at path: ' . ($licensePath ?: 'EMPTY/FALSE'));
            \Log::info('License path type: ' . gettype($licensePath));
            \Log::info('License path length: ' . strlen($licensePath ?: ''));

            // Check if storage failed
            if (!$licensePath || empty($licensePath)) {
                \Log::error('File storage failed - storeAs returned empty/false');
                \Log::error('Storage disk info:', [
                    'disk' => 'public',
                    'root' => storage_path('app/public'),
                    'target_dir' => storage_path('app/public/merchant-licenses'),
                    'target_file' => storage_path('app/public/merchant-licenses/' . $filename),
                    'dir_exists' => is_dir(storage_path('app/public/merchant-licenses')),
                    'dir_writable' => is_writable(storage_path('app/public/merchant-licenses')),
                ]);
                throw new \Exception('Failed to store license file - storage operation returned empty path');
            }

            // Verify file was stored
            $fullPath = storage_path('app/public/' . $licensePath);
            \Log::info('Full file path: ' . $fullPath);
            \Log::info('File exists after storage: ' . (file_exists($fullPath) ? 'YES' : 'NO'));
            if (file_exists($fullPath)) {
                \Log::info('Stored file size: ' . filesize($fullPath) . ' bytes');
            } else {
                \Log::error('File does not exist after storage operation');
                throw new \Exception('File was not found after storage operation');
            }

            // Update merchant license information
            \Log::info('Updating merchant record...');
            $updateData = [
                'license_file' => $licensePath,
                'license_expiry_date' => $expiryDate,
                'license_status' => 'checking',
                'license_verified' => false,
                'license_rejection_reason' => null,
                'license_uploaded_at' => now(),
                'license_approved_at' => null,
                'license_approved_by' => null,
            ];
            \Log::info('Update data: ', $updateData);

            $updateResult = $merchant->update($updateData);
            \Log::info('Merchant update result: ' . ($updateResult ? 'SUCCESS' : 'FAILED'));

            // Verify the update
            $merchant->refresh();
            \Log::info('Merchant after update: ', [
                'license_file' => $merchant->license_file,
                'license_expiry_date' => $merchant->license_expiry_date,
                'license_status' => $merchant->license_status,
                'license_uploaded_at' => $merchant->license_uploaded_at,
            ]);

            \Log::info("License uploaded successfully for merchant: {$merchant->business_name} (ID: {$merchant->id})");

            // TODO: Send notification to admin about pending license review
            // $this->sendLicenseUploadNotification($merchant);

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to upload license for merchant {$merchant->id}: " . $e->getMessage());
            \Log::error('Exception details: ', [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get license validation errors for a merchant.
     */
    public function getLicenseValidationErrors(Merchant $merchant): array
    {
        $errors = [];

        if (!$merchant->license_file) {
            $errors[] = 'No license file uploaded';
        }

        if (!$merchant->license_expiry_date) {
            $errors[] = 'No license expiry date set';
        } elseif ($merchant->isLicenseExpired()) {
            $errors[] = 'License has expired';
        }

        if (!$merchant->license_verified) {
            $errors[] = 'License is not verified';
        }

        if ($merchant->license_status === 'rejected') {
            $errors[] = 'License was rejected: ' . $merchant->license_rejection_reason;
        }

        return $errors;
    }

    /**
     * Get license action message for UI display.
     */
    public function getLicenseActionMessage(Merchant $merchant): string
    {
        return match($merchant->license_status) {
            'checking' => 'Your license is currently under review. Please wait for admin approval before adding products or services.',
            'expired' => 'Your license has expired. Please upload a new license to continue adding products or services.',
            'rejected' => 'Your license was rejected. Please upload a new license to continue adding products or services.',
            'verified' => $merchant->isLicenseExpired() 
                ? 'Your license has expired. Please upload a new license to continue adding products or services.'
                : 'Your license is valid and verified.',
            default => 'Your license is outdated. Please upgrade your license to add products or services.'
        };
    }

    /**
     * Bulk approve multiple licenses.
     */
    public function bulkApproveLicenses(array $merchantIds, User $approvedBy, ?string $message = null): array
    {
        $results = [
            'approved' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $merchants = Merchant::whereIn('id', $merchantIds)
            ->where('license_status', 'checking')
            ->get();

        foreach ($merchants as $merchant) {
            if ($this->approveLicense($merchant, $approvedBy, $message)) {
                $results['approved']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "Failed to approve license for {$merchant->business_name}";
            }
        }

        return $results;
    }

    /**
     * Clean up old license files that are no longer referenced.
     */
    public function cleanupOrphanedLicenseFiles(): int
    {
        $cleanedCount = 0;
        
        try {
            // Get all license files in storage
            $storageFiles = Storage::files('public/merchant-licenses');
            
            // Get all license file paths from database
            $dbFiles = Merchant::whereNotNull('license_file')
                ->pluck('license_file')
                ->map(function ($path) {
                    return 'public/' . $path;
                })
                ->toArray();

            // Find orphaned files
            $orphanedFiles = array_diff($storageFiles, $dbFiles);

            // Delete orphaned files
            foreach ($orphanedFiles as $file) {
                if (Storage::delete($file)) {
                    $cleanedCount++;
                }
            }

            Log::info("Cleaned up {$cleanedCount} orphaned license files");
        } catch (\Exception $e) {
            Log::error("Failed to cleanup orphaned license files: " . $e->getMessage());
        }

        return $cleanedCount;
    }
}
