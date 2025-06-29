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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Exception;

class RegistrationService
{
    protected $tempRegistrationService;
    protected $emailVerificationService;

    public function __construct(
        TemporaryRegistrationService $tempRegistrationService = null,
        EmailVerificationService $emailVerificationService = null
    ) {
        $this->tempRegistrationService = $tempRegistrationService ?? new TemporaryRegistrationService();
        $this->emailVerificationService = $emailVerificationService ?? new EmailVerificationService();
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

            // Create role-specific profile if merchant
            if ($userType === 'merchant') {
                $this->createMerchantProfile($user, $userData);
            }

            // Clean up temporary data
            $this->tempRegistrationService->removeTemporaryRegistration($registrationToken);

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

            // Store registration data temporarily
            $registrationToken = $this->tempRegistrationService->storeTemporaryRegistration($userData, 'provider');

            // Generate and store email verification code
            $verificationCode = $this->generateVerificationCode();
            $this->tempRegistrationService->storeEmailVerificationCode($registrationToken, $verificationCode);

            // Send email verification
            $emailResult = $this->emailVerificationService->sendVerificationEmailForTempRegistration(
                $userData['email'],
                $userData['name'],
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

            // Store registration data temporarily
            $registrationToken = $this->tempRegistrationService->storeTemporaryRegistration($userData, 'merchant');

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
    private function createMerchantProfile(User $user, array $userData): void
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

        // Handle file uploads
        if (isset($userData['logo']) && $userData['logo'] instanceof UploadedFile) {
            $logoPath = $this->uploadMerchantLogo($userData['logo'], $merchant->id);
            $merchant->update(['logo' => $logoPath]);
        }

        if (isset($userData['uae_id_front']) && $userData['uae_id_front'] instanceof UploadedFile) {
            $frontPath = $this->uploadUaeIdImage($userData['uae_id_front'], $user->id, 'front');
            $merchant->update(['uae_id_front' => $frontPath]);
        }

        if (isset($userData['uae_id_back']) && $userData['uae_id_back'] instanceof UploadedFile) {
            $backPath = $this->uploadUaeIdImage($userData['uae_id_back'], $user->id, 'back');
            $merchant->update(['uae_id_back' => $backPath]);
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
