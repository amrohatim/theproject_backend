<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\Provider;
use App\Models\Merchant;
use App\Models\License;
use App\Models\VendorLocation;
use App\Services\TemporaryRegistrationService;
use App\Services\EmailVerificationService;
use App\Services\SMSalaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Exception;

class RegistrationService
{
    protected $tempRegistrationService;
    protected $emailVerificationService;
    protected $smsalaService;

    public function __construct(
        TemporaryRegistrationService $tempRegistrationService = null,
        EmailVerificationService $emailVerificationService = null,
        SMSalaService $smsalaService = null
    ) {
        $this->tempRegistrationService = $tempRegistrationService ?? new TemporaryRegistrationService();
        $this->emailVerificationService = $emailVerificationService ?? new EmailVerificationService();
        $this->smsalaService = $smsalaService ?? new SMSalaService();
    }

    /**
     * Start vendor registration process - Step 1: Store data temporarily and send email verification.
     */
    public function startVendorRegistration(array $userData): array
    {
        try {
            // Validate unique fields before storing temporarily
            $this->validateUniqueFields($userData);

            // Store registration data temporarily
            $registrationToken = $this->tempRegistrationService->storeTemporaryRegistration($userData, 'vendor');

            // Generate and store email verification code
            $verificationCode = $this->generateVerificationCode();
            $this->tempRegistrationService->storeEmailVerificationCode($registrationToken, $verificationCode);

            // Send email verification
            $emailResult = $this->emailVerificationService->sendVerificationEmailForTempRegistration(
                $userData['email'],
                $userData['name'],
                $verificationCode,
                'vendor'
            );

            if (!$emailResult['success']) {
                throw new Exception('Failed to send verification email');
            }

            return [
                'success' => true,
                'message' => 'Registration information received. Please check your email for verification.',
                'registration_token' => $registrationToken,
                'next_step' => 'email_verification',
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify email only without creating user (for phone verification flow).
     */
    public function verifyEmailOnly(string $registrationToken, string $verificationCode): array
    {
        try {
            Log::info('Starting email verification process', [
                'registration_token' => $registrationToken,
                'verification_code' => $verificationCode,
            ]);

            // Get temporary registration data
            $tempData = $this->tempRegistrationService->getTemporaryRegistration($registrationToken);

            if (!$tempData) {
                Log::warning('Temporary registration not found or expired', [
                    'token' => $registrationToken,
                ]);
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            // Verify the email verification code
            if (!$this->tempRegistrationService->verifyEmailCode($registrationToken, $verificationCode)) {
                Log::warning('Invalid email verification code provided', [
                    'token' => $registrationToken,
                    'provided_code' => $verificationCode,
                ]);
                return [
                    'success' => false,
                    'message' => 'Invalid verification code. Please try again.',
                ];
            }

            // Mark email as verified by removing the verification code
            $this->tempRegistrationService->removeEmailVerificationCode($registrationToken);

            Log::info('Email verified successfully for temporary registration', [
                'registration_token' => $registrationToken,
                'email' => $tempData['user_data']['email'],
            ]);

            return [
                'success' => true,
                'message' => 'Email verified successfully. You can now proceed to phone verification.',
                'next_step' => 'phone_verification',
            ];
        } catch (Exception $e) {
            Log::error('Email verification error: ' . $e->getMessage(), [
                'registration_token' => $registrationToken,
                'verification_code' => $verificationCode,
                'exception' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => 'Email verification failed. Please try again.',
            ];
        }
    }

    /**
     * Verify email for temporary registration and create user.
     */
    public function verifyEmailAndCreateUser(string $registrationToken, string $verificationCode): array
    {
        try {
            // Verify the email code
            if (!$this->tempRegistrationService->verifyEmailCode($registrationToken, $verificationCode)) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired verification code',
                ];
            }

            // Get temporary registration data
            $tempData = $this->tempRegistrationService->getTemporaryRegistration($registrationToken);
            if (!$tempData) {
                return [
                    'success' => false,
                    'message' => 'Registration data not found or expired',
                ];
            }

            $userData = $tempData['user_data'];
            $userType = $tempData['user_type'];

            DB::beginTransaction();

            // Create the user in database
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'phone' => $userData['phone'],
                'role' => $userType,
                'status' => 'inactive',
                'registration_step' => 'info_completed',
                'email_verified_at' => now(),
            ]);

            // Create role-specific profile
            if ($userType === 'merchant') {
                // Get temporary files
                $tempFiles = $this->getTemporaryFiles($registrationToken);
                $this->createMerchantProfile($user, $userData, $tempFiles);
            } elseif ($userType === 'provider') {
                // Get temporary files
                $tempFiles = $this->getTemporaryFiles($registrationToken);
                $this->createProviderProfile($user, $userData, $tempFiles);
            }

            // Clean up temporary data
            $this->tempRegistrationService->removeTemporaryRegistration($registrationToken);

            // Clean up temporary files
            $this->cleanupTemporaryFiles($registrationToken);

            DB::commit();

            $nextStep = match($userType) {
                'vendor' => 'company_information',
                'provider' => 'license_upload',
                'merchant' => 'license_upload',
                default => 'license_upload'
            };

            return [
                'success' => true,
                'message' => 'Email verified successfully. You can now proceed to the next step.',
                'user_id' => $user->id,
                'next_step' => $nextStep,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate a random 6-digit verification code.
     */
    private function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store uploaded files temporarily during registration.
     */
    private function storeTemporaryFiles(string $registrationToken, array $fileUploads): void
    {
        $filePaths = [];

        foreach ($fileUploads as $fieldName => $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                try {
                    // Get file information before storing
                    $originalName = $file->getClientOriginalName();
                    $mimeType = $file->getClientMimeType();
                    $size = $file->getSize();
                    $extension = $file->getClientOriginalExtension();

                    $filename = $fieldName . '_' . $registrationToken . '_' . time() . '.' . $extension;

                    // Store file using Laravel's storage system
                    $storagePath = $file->storeAs('temp_registration', $filename, 'local');
                    $fullPath = storage_path('app/' . $storagePath);

                    $filePaths[$fieldName] = [
                        'path' => $fullPath,
                        'storage_path' => $storagePath,
                        'original_name' => $originalName,
                        'mime_type' => $mimeType,
                        'size' => $size,
                    ];

                    Log::info("File stored temporarily", [
                        'field' => $fieldName,
                        'filename' => $filename,
                        'size' => $size,
                        'storage_path' => $storagePath,
                    ]);
                } catch (Exception $e) {
                    Log::error("Failed to store temporary file", [
                        'field' => $fieldName,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue with other files, don't fail the entire registration
                }
            }
        }

        // Store file information in cache
        $cacheKey = "temp_files_{$registrationToken}";
        Cache::put($cacheKey, $filePaths, Carbon::now()->addHours(24));

        Log::info("Temporary files stored for registration", [
            'token' => $registrationToken,
            'files' => array_keys($filePaths),
        ]);
    }

    /**
     * Retrieve temporarily stored files.
     */
    private function getTemporaryFiles(string $registrationToken): array
    {
        $cacheKey = "temp_files_{$registrationToken}";
        return Cache::get($cacheKey, []);
    }

    /**
     * Clean up temporary files after registration completion.
     */
    private function cleanupTemporaryFiles(string $registrationToken): void
    {
        // Get file information from cache
        $cacheKey = "temp_files_{$registrationToken}";
        $tempFiles = Cache::get($cacheKey, []);

        // Delete each temporary file
        foreach ($tempFiles as $fieldName => $fileInfo) {
            if (isset($fileInfo['storage_path']) && Storage::disk('local')->exists($fileInfo['storage_path'])) {
                Storage::disk('local')->delete($fileInfo['storage_path']);
                Log::info("Temporary file deleted", [
                    'field' => $fieldName,
                    'storage_path' => $fileInfo['storage_path'],
                ]);
            }
        }

        // Remove from cache
        Cache::forget($cacheKey);

        Log::info("Temporary files cleaned up", [
            'token' => $registrationToken,
            'files_count' => count($tempFiles),
        ]);
    }

    /**
     * Complete vendor company information.
     */
    public function completeVendorCompanyInfo(int $userId, array $companyData): array
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);

            // Validate company unique fields
            $this->validateCompanyUniqueFields($companyData);

            // Create company
            $company = Company::create([
                'user_id' => $userId,
                'name' => $companyData['name'],
                'email' => $companyData['email'],
                'contact_number_1' => $companyData['contact_number_1'],
                'contact_number_2' => $companyData['contact_number_2'] ?? null,
                'address' => $companyData['address'],
                'emirate' => $companyData['emirate'],
                'city' => $companyData['city'],
                'street' => $companyData['street'] ?? null,
                'delivery_capability' => $companyData['delivery_capability'] ?? false,
                'delivery_areas' => $companyData['delivery_areas'] ?? null,
                'description' => $companyData['description'] ?? null,
                'status' => 'pending',
            ]);

            // Handle logo upload if provided
            if (isset($companyData['logo']) && $companyData['logo'] instanceof UploadedFile) {
                $logoPath = $this->uploadCompanyLogo($companyData['logo'], $company->id);
                $company->update(['logo' => $logoPath]);
            }

            // Update user registration step
            $user->update([
                'registration_step' => 'company_completed',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Company information saved successfully',
                'company_id' => $company->id,
                'next_step' => 'license_upload',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Complete vendor license upload.
     */
    public function completeVendorLicense(int $userId, UploadedFile $licenseFile, array $licenseData = []): array
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);

            // Upload license file
            $licensePath = $this->uploadLicenseFile($licenseFile, $userId);

            // Calculate dates
            $startDate = Carbon::now()->toDateString();
            $duration = (int)($licenseData['duration_days'] ?? 365); // Default 1 year, cast to int
            $endDate = Carbon::now()->addDays($duration)->toDateString();
            $renewalDate = $endDate;

            // Create license record
            $license = License::create([
                'user_id' => $userId,
                'license_type' => 'registration',
                'license_file_path' => $licensePath,
                'license_file_name' => $licenseFile->getClientOriginalName(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_days' => $duration,
                'status' => 'active',
                'renewal_date' => $renewalDate,
                'notes' => $licenseData['notes'] ?? null,
            ]);

            // Update user registration step
            $user->update([
                'registration_step' => 'license_completed',
                'status' => 'active', // Activate user after license upload
            ]);

            // Update company status
            $user->company()->update(['status' => 'active']);

            DB::commit();

            return [
                'success' => true,
                'message' => 'License uploaded successfully. Registration completed!',
                'license_id' => $license->id,
                'next_step' => 'verification_pending',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Start provider registration process - Step 1: Store data temporarily and send email verification.
     */
    public function startProviderRegistration(array $userData): array
    {
        try {
            // Validate unique fields before storing temporarily
            $this->validateUniqueFields($userData);

            // Separate file uploads from serializable data
            $fileUploads = [];
            $serializableData = [];

            foreach ($userData as $key => $value) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $fileUploads[$key] = $value;
                } else {
                    $serializableData[$key] = $value;
                }
            }

            // Store registration data temporarily (without files)
            $registrationToken = $this->tempRegistrationService->storeTemporaryRegistration($serializableData, 'provider');

            // Store file uploads temporarily if any exist
            if (!empty($fileUploads)) {
                $this->storeTemporaryFiles($registrationToken, $fileUploads);
            }

            // Generate and store email verification code
            $verificationCode = $this->generateVerificationCode();
            $this->tempRegistrationService->storeEmailVerificationCode($registrationToken, $verificationCode);

            // Send email verification
            $emailResult = $this->emailVerificationService->sendVerificationEmailForTempRegistration(
                $serializableData['email'],
                $serializableData['name'],
                $verificationCode,
                'provider'
            );

            if (!$emailResult['success']) {
                throw new Exception('Failed to send verification email');
            }

            return [
                'success' => true,
                'message' => 'Registration information received. Please check your email for verification.',
                'registration_token' => $registrationToken,
                'next_step' => 'email_verification',
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Start merchant registration process - Step 1: Store data temporarily and send email verification.
     */
    public function startMerchantRegistration(array $userData): array
    {
        try {
            // Validate unique fields before storing temporarily
            $this->validateUniqueFields($userData);

            // Separate file uploads from other data
            $fileUploads = [];
            $serializableData = [];

            foreach ($userData as $key => $value) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $fileUploads[$key] = $value;
                } else {
                    $serializableData[$key] = $value;
                }
            }

            // Store registration data temporarily (without files)
            $registrationToken = $this->tempRegistrationService->storeTemporaryRegistration($serializableData, 'merchant');

            // Store file uploads temporarily if any exist
            if (!empty($fileUploads)) {
                $this->storeTemporaryFiles($registrationToken, $fileUploads);
            }

            // Generate and store email verification code
            $verificationCode = $this->generateVerificationCode();
            $this->tempRegistrationService->storeEmailVerificationCode($registrationToken, $verificationCode);

            // Send email verification
            $emailResult = $this->emailVerificationService->sendVerificationEmailForTempRegistration(
                $userData['email'],
                $userData['name'],
                $verificationCode,
                'merchant'
            );

            if (!$emailResult['success']) {
                throw new Exception('Failed to send verification email');
            }

            return [
                'success' => true,
                'message' => 'Registration information received. Please check your email for verification.',
                'registration_token' => $registrationToken,
                'next_step' => 'email_verification',
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Complete provider registration after email verification.
     */
    public function completeProviderRegistration(int $userId, array $userData): array
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);

            // Create provider profile
            $provider = Provider::create([
                'user_id' => $user->id,
                'business_name' => $userData['business_name'] ?? $userData['name'],
                'contact_email' => $userData['email'],
                'contact_phone' => $userData['phone'],
                'description' => $userData['description'] ?? null,
                'status' => 'pending',
                'is_verified' => false,
            ]);

            // Handle logo upload if provided
            if (isset($userData['logo']) && $userData['logo'] instanceof UploadedFile) {
                $logoPath = $this->uploadProviderLogo($userData['logo'], $provider->id);
                $provider->update(['logo' => $logoPath]);
            }

            // Create vendor locations if provided
            if (isset($userData['stock_locations']) && is_array($userData['stock_locations'])) {
                foreach ($userData['stock_locations'] as $index => $location) {
                    VendorLocation::create([
                        'user_id' => $user->id,
                        'name' => $location['name'] ?? "Location " . ($index + 1),
                        'address' => $location['address'],
                        'emirate' => $location['emirate'],
                        'city' => $location['city'],
                        'latitude' => $location['latitude'] ?? null,
                        'longitude' => $location['longitude'] ?? null,
                        'is_primary' => $index === 0, // First location is primary
                        'can_deliver_to_vendors' => $userData['deliver_to_vendor_capability'] ?? false,
                        'delivery_fees' => $userData['delivery_fees'] ?? null,
                        'status' => 'active',
                    ]);
                }
            }

            // Send email verification notification
            $user->sendEmailVerificationNotification();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Provider information saved successfully. Please check your email for verification.',
                'user_id' => $user->id,
                'provider_id' => $provider->id,
                'next_step' => 'email_verification',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Complete provider license upload.
     */
    public function completeProviderLicense(int $userId, UploadedFile $licenseFile, array $licenseData = []): array
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);

            // Upload license file
            $licensePath = $this->uploadLicenseFile($licenseFile, $userId);

            // Calculate dates
            $startDate = Carbon::now()->toDateString();
            $duration = (int)($licenseData['duration_days'] ?? 365); // Cast to int to fix Carbon type error
            $endDate = Carbon::now()->addDays($duration)->toDateString();
            $renewalDate = $endDate;

            // Create license record
            $license = License::create([
                'user_id' => $userId,
                'license_type' => 'registration',
                'license_file_path' => $licensePath,
                'license_file_name' => $licenseFile->getClientOriginalName(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_days' => $duration,
                'status' => 'active',
                'renewal_date' => $renewalDate,
                'notes' => $licenseData['notes'] ?? null,
            ]);

            // Update user and provider status
            $user->update([
                'registration_step' => 'license_completed',
                'status' => 'active',
            ]);

            $user->provider()->update(['status' => 'active']);

            DB::commit();

            return [
                'success' => true,
                'message' => 'License uploaded successfully. Registration completed!',
                'license_id' => $license->id,
                'next_step' => 'verification_pending',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Upload merchant license.
     */
    public function uploadMerchantLicense(User $user, array $licenseData): array
    {
        try {
            DB::beginTransaction();

            // Upload license file
            $licensePath = $this->uploadLicenseFile($licenseData['license_file'], $user->id);

            // Calculate dates
            $startDate = Carbon::now()->toDateString();
            $duration = (int)($licenseData['duration_days'] ?? 365);
            $endDate = Carbon::now()->addDays($duration)->toDateString();
            $renewalDate = $endDate;

            // Create license record
            $license = License::create([
                'user_id' => $user->id,
                'license_type' => $licenseData['license_type'] ?? 'registration',
                'license_file_path' => $licensePath,
                'license_file_name' => $licenseData['license_file']->getClientOriginalName(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_days' => $duration,
                'status' => 'active',
                'renewal_date' => $renewalDate,
                'notes' => $licenseData['notes'] ?? null,
            ]);

            // Update user registration step
            $user->update([
                'registration_step' => 'license_completed',
                'status' => 'active',
            ]);

            // Update merchant status if exists
            if ($user->merchant) {
                $user->merchant()->update(['status' => 'active']);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'License uploaded successfully. Registration completed!',
                'license_id' => $license->id,
                'next_step' => 'verification_pending',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate unique fields for user registration.
     */
    private function validateUniqueFields(array $userData): void
    {
        if (User::where('email', $userData['email'])->exists()) {
            throw new Exception('Email address is already registered');
        }

        if (User::where('phone', $userData['phone'])->exists()) {
            throw new Exception('Phone number is already registered');
        }

        if (User::where('name', $userData['name'])->exists()) {
            throw new Exception('Name is already taken');
        }
    }

    /**
     * Validate unique fields for company registration.
     */
    private function validateCompanyUniqueFields(array $companyData): void
    {
        if (Company::where('name', $companyData['name'])->exists()) {
            throw new Exception('Company name is already registered');
        }

        if (Company::where('email', $companyData['email'])->exists()) {
            throw new Exception('Company email is already registered');
        }

        if (Company::where('contact_number_1', $companyData['contact_number_1'])->exists()) {
            throw new Exception('Primary contact number is already registered');
        }

        if (isset($companyData['contact_number_2']) && 
            Company::where('contact_number_2', $companyData['contact_number_2'])->exists()) {
            throw new Exception('Secondary contact number is already registered');
        }
    }

    /**
     * Upload company logo.
     */
    private function uploadCompanyLogo(UploadedFile $file, int $companyId): string
    {
        $filename = 'company_' . $companyId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('images/companies', $filename, 'public');
    }

    /**
     * Upload provider logo.
     */
    private function uploadProviderLogo(UploadedFile $file, int $providerId): string
    {
        $filename = 'provider_' . $providerId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('images/providers', $filename, 'public');
    }

    /**
     * Upload merchant logo.
     */
    private function uploadMerchantLogo(UploadedFile $file, int $merchantId): string
    {
        $filename = 'merchant_' . $merchantId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('images/merchants', $filename, 'public');
    }

    /**
     * Upload UAE ID image.
     */
    private function uploadUaeIdImage(UploadedFile $file, int $userId, string $side): string
    {
        $filename = 'uae_id_' . $side . '_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('images/uae_ids', $filename, 'public');
    }

    /**
     * Create merchant profile.
     */
    private function createMerchantProfile(User $user, array $userData, array $tempFiles = []): void
    {
        $merchantData = [
            'user_id' => $user->id,
            'business_name' => $userData['name'],
            'status' => 'pending',
            'is_verified' => false,
            'delivery_capability' => $userData['delivery_capability'] ?? false,
        ];

        // Handle store location
        if (isset($userData['store_location_lat']) && isset($userData['store_location_lng'])) {
            $merchantData['store_location_lat'] = $userData['store_location_lat'];
            $merchantData['store_location_lng'] = $userData['store_location_lng'];
            $merchantData['store_location_address'] = $userData['store_location_address'] ?? null;
        }

        // Handle delivery fees
        if (isset($userData['delivery_fees']) && is_array($userData['delivery_fees'])) {
            $merchantData['delivery_fees'] = $userData['delivery_fees'];
        }

        $merchant = Merchant::create($merchantData);

        // Handle temporary file uploads
        foreach ($tempFiles as $fieldName => $fileInfo) {
            if (file_exists($fileInfo['path'])) {
                switch ($fieldName) {
                    case 'logo':
                        $logoPath = $this->moveTemporaryFileToFinal($fileInfo, 'images/merchants', 'merchant_' . $merchant->id . '_logo');
                        $merchant->update(['logo' => $logoPath]);
                        break;
                    case 'uae_id_front':
                        $frontPath = $this->moveTemporaryFileToFinal($fileInfo, 'images/uae_ids', 'uae_id_front_' . $user->id);
                        $merchant->update(['uae_id_front' => $frontPath]);
                        break;
                    case 'uae_id_back':
                        $backPath = $this->moveTemporaryFileToFinal($fileInfo, 'images/uae_ids', 'uae_id_back_' . $user->id);
                        $merchant->update(['uae_id_back' => $backPath]);
                        break;
                }
            }
        }
    }

    /**
     * Create provider profile.
     */
    private function createProviderProfile(User $user, array $userData, array $tempFiles = []): void
    {
        $providerData = [
            'user_id' => $user->id,
            'business_name' => $userData['business_name'],
            'business_type' => $userData['business_type'],
            'description' => $userData['description'] ?? null,
            'delivery_capability' => $userData['delivery_capability'] ?? false,
            'status' => 'pending',
            'is_verified' => false,
        ];

        // Handle logo upload if provided
        if (isset($userData['logo']) && $userData['logo'] instanceof \Illuminate\Http\UploadedFile) {
            // Direct UploadedFile object (for backward compatibility)
            $logoPath = $userData['logo']->store('logos', 'public');
            $providerData['logo'] = $logoPath;
        } elseif (isset($tempFiles['logo']) && file_exists($tempFiles['logo']['path'])) {
            // Temporary file from registration process
            $logoPath = $this->moveTemporaryFileToFinal($tempFiles['logo'], 'logos', 'provider_' . $user->id . '_logo');
            $providerData['logo'] = $logoPath;
        }

        Provider::create($providerData);

        Log::info("Provider profile created", [
            'user_id' => $user->id,
            'business_name' => $userData['business_name'],
            'business_type' => $userData['business_type'],
            'logo_uploaded' => isset($providerData['logo']),
        ]);
    }

    /**
     * Move temporary file to final storage location.
     */
    private function moveTemporaryFileToFinal(array $fileInfo, string $directory, string $baseFilename): string
    {
        $extension = pathinfo($fileInfo['original_name'], PATHINFO_EXTENSION);
        $filename = $baseFilename . '_' . time() . '.' . $extension;
        $finalPath = $directory . '/' . $filename;

        // Copy file from temp storage to public storage
        if (isset($fileInfo['storage_path']) && Storage::disk('local')->exists($fileInfo['storage_path'])) {
            $fileContents = Storage::disk('local')->get($fileInfo['storage_path']);
            Storage::disk('public')->put($finalPath, $fileContents);

            Log::info("File moved from temporary to final location", [
                'from' => $fileInfo['storage_path'],
                'to' => $finalPath,
                'public_path' => $finalPath,
            ]);

            return $finalPath;
        } else {
            Log::error("Temporary file not found", [
                'storage_path' => $fileInfo['storage_path'] ?? 'not set',
                'file_info' => $fileInfo,
            ]);
            throw new Exception("Temporary file not found");
        }
    }

    /**
     * Upload license file.
     */
    private function uploadLicenseFile(UploadedFile $file, int $userId): string
    {
        $filename = 'license_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('licenses', $filename, 'public');
    }

    /**
     * Verify temporary registration email.
     */
    public function verifyTempRegistrationEmail(string $registrationToken, string $verificationCode): array
    {
        try {
            // Get temporary registration data
            $tempData = $this->tempRegistrationService->getTemporaryRegistration($registrationToken);

            if (!$tempData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            // Verify email code
            $result = $this->tempRegistrationService->verifyEmailCode($registrationToken, $verificationCode);

            if (!$result) {
                return [
                    'success' => false,
                    'message' => 'Invalid verification code. Please try again.',
                ];
            }

            return [
                'success' => true,
                'message' => 'Email verified successfully. Please verify your phone number.',
                'next_step' => 'phone_verification',
            ];
        } catch (Exception $e) {
            Log::error('Temp email verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Verification failed. Please try again.',
            ];
        }
    }

    /**
     * Send phone verification OTP.
     */
    public function sendPhoneVerificationOTP(string $registrationToken): array
    {
        try {
            // Get temporary registration data
            $tempData = $this->tempRegistrationService->getTemporaryRegistration($registrationToken);

            if (!$tempData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            // Check if email is verified first
            if (!$this->tempRegistrationService->isEmailVerified($registrationToken)) {
                return [
                    'success' => false,
                    'message' => 'Please verify your email first.',
                ];
            }

            $phoneNumber = $tempData['user_data']['phone'];
            $userType = $tempData['user_type'];

            // Send OTP via SMSala
            $result = $this->smsalaService->sendOTP($phoneNumber, 'registration');

            if ($result['success']) {
                // Store the request ID for this registration token
                $this->tempRegistrationService->storePhoneVerificationRequestId(
                    $registrationToken,
                    $result['request_id']
                );

                Log::info('Phone verification OTP sent', [
                    'registration_token' => $registrationToken,
                    'phone' => $phoneNumber,
                    'request_id' => $result['request_id'],
                    'user_type' => $userType,
                ]);

                return [
                    'success' => true,
                    'message' => 'OTP sent to your phone number.',
                    'request_id' => $result['request_id'],
                    'expires_in' => $result['expires_in'],
                ];
            } else {
                Log::error('Failed to send phone verification OTP', [
                    'registration_token' => $registrationToken,
                    'phone' => $phoneNumber,
                    'error' => $result['message'],
                ]);

                return [
                    'success' => false,
                    'message' => $result['message'],
                ];
            }
        } catch (Exception $e) {
            Log::error('Phone verification OTP send error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
            ];
        }
    }

    /**
     * Verify phone OTP and create user.
     */
    public function verifyPhoneOTPAndCreateUser(string $registrationToken, string $otpCode): array
    {
        try {
            // Get temporary registration data
            $tempData = $this->tempRegistrationService->getTemporaryRegistration($registrationToken);

            if (!$tempData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            // Check if email is verified first
            if (!$this->tempRegistrationService->isEmailVerified($registrationToken)) {
                return [
                    'success' => false,
                    'message' => 'Please verify your email first.',
                ];
            }

            // Get the phone verification request ID
            $requestId = $this->tempRegistrationService->getPhoneVerificationRequestId($registrationToken);
            if (!$requestId) {
                return [
                    'success' => false,
                    'message' => 'Phone verification not initiated. Please request OTP first.',
                ];
            }

            // Verify OTP
            $verificationResult = $this->smsalaService->verifyOTP($requestId, $otpCode);

            if (!$verificationResult['success']) {
                return [
                    'success' => false,
                    'message' => $verificationResult['message'],
                ];
            }

            $userData = $tempData['user_data'];
            $userType = $tempData['user_type'];

            DB::beginTransaction();

            // Create the user in database
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'phone' => $userData['phone'],
                'role' => $userType,
                'status' => 'inactive',
                'registration_step' => 'phone_verified',
                'email_verified_at' => now(),
                'phone_verified' => true,
                'phone_verified_at' => now(),
            ]);

            // Create role-specific profile
            if ($userType === 'merchant') {
                // Get temporary files
                $tempFiles = $this->getTemporaryFiles($registrationToken);
                $this->createMerchantProfile($user, $userData, $tempFiles);
            } elseif ($userType === 'provider') {
                // Get temporary files
                $tempFiles = $this->getTemporaryFiles($registrationToken);
                $this->createProviderProfile($user, $userData, $tempFiles);
            }

            // Clean up temporary data
            $this->tempRegistrationService->removeTemporaryRegistration($registrationToken);

            // Clean up temporary files
            $this->cleanupTemporaryFiles($registrationToken);

            DB::commit();

            $nextStep = match($userType) {
                'vendor' => 'company_information',
                'provider' => 'license_upload',
                'merchant' => 'license_upload',
                default => 'license_upload'
            };

            Log::info('User created after phone verification', [
                'user_id' => $user->id,
                'phone' => $userData['phone'],
                'user_type' => $userType,
            ]);

            return [
                'success' => true,
                'message' => 'Phone verified successfully. Registration completed!',
                'user_id' => $user->id,
                'next_step' => $nextStep,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Phone verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Verification failed. Please try again.',
            ];
        }
    }

    /**
     * Resend phone verification OTP.
     */
    public function resendPhoneVerificationOTP(string $registrationToken): array
    {
        try {
            // Get temporary registration data
            $tempData = $this->tempRegistrationService->getTemporaryRegistration($registrationToken);

            if (!$tempData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            // Check if email is verified first
            if (!$this->tempRegistrationService->isEmailVerified($registrationToken)) {
                return [
                    'success' => false,
                    'message' => 'Please verify your email first.',
                ];
            }

            // Get the current phone verification request ID
            $currentRequestId = $this->tempRegistrationService->getPhoneVerificationRequestId($registrationToken);

            if ($currentRequestId) {
                // Try to resend using existing request ID
                $result = $this->smsalaService->resendOTP($currentRequestId);

                if ($result['success']) {
                    // Update the request ID if a new one was generated
                    if (isset($result['request_id']) && $result['request_id'] !== $currentRequestId) {
                        $this->tempRegistrationService->storePhoneVerificationRequestId(
                            $registrationToken,
                            $result['request_id']
                        );
                    }

                    return $result;
                }
            }

            // If resend failed or no current request ID, send new OTP
            return $this->sendPhoneVerificationOTP($registrationToken);
        } catch (Exception $e) {
            Log::error('Phone verification OTP resend error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.',
            ];
        }
    }

    /**
     * Resend temporary email verification.
     */
    public function resendTempEmailVerification(string $registrationToken): array
    {
        try {
            // Get temporary registration data
            $tempData = $this->tempRegistrationService->getTemporaryRegistration($registrationToken);

            if (!$tempData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            // Generate new verification code
            $verificationCode = $this->generateVerificationCode();
            $this->tempRegistrationService->storeEmailVerificationCode($registrationToken, $verificationCode);

            // Send email verification
            $emailResult = $this->emailVerificationService->sendVerificationEmailForTempRegistration(
                $tempData['user_data']['email'],
                $tempData['user_data']['name'],
                $verificationCode,
                $tempData['user_type']
            );

            if (!$emailResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to send verification email. Please try again.',
                ];
            }

            return [
                'success' => true,
                'message' => 'Verification email sent successfully.',
            ];
        } catch (Exception $e) {
            Log::error('Resend temp email verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to resend verification email. Please try again.',
            ];
        }
    }

    /**
     * Resend email verification for registration token.
     */
    public function resendEmailVerification(string $registrationToken): array
    {
        return $this->resendTempEmailVerification($registrationToken);
    }
}
