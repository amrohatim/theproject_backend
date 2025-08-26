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
use App\Services\OtpService;
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
    protected $otpService;

    public function __construct(
        TemporaryRegistrationService $tempRegistrationService = null,
        EmailVerificationService $emailVerificationService = null,
        SMSalaService $smsalaService = null,
        OtpService $otpService = null
    ) {
        $this->tempRegistrationService = $tempRegistrationService ?? new TemporaryRegistrationService();
        $this->emailVerificationService = $emailVerificationService ?? new EmailVerificationService();
        $this->smsalaService = $smsalaService ?? new SMSalaService();
        $this->otpService = $otpService ?? new OtpService();
    }

    /**
     * Start vendor registration process - Step 1: Store data in session and send email verification.
     */
    public function startVendorRegistrationSession(array $userData): array
    {
        try {
            // Validate unique fields before storing in session
            $this->validateUniqueFields($userData);

            // Generate verification code
            $verificationCode = $this->generateVerificationCode();

            // Store registration data in session
            session([
                'vendor_registration' => [
                    'step' => 1,
                    'personal_info' => $userData,
                    'email_verification_code' => $verificationCode,
                    'email_verification_code_expires' => now()->addHours(24)->timestamp,
                    'email_verified' => false,
                    'phone_verified' => false,
                    'created_at' => now()->timestamp,
                ]
            ]);

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
                'next_step' => 'email_verification',
            ];
        } catch (Exception $e) {
            throw $e;
        }
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
     * Verify email using session-based storage.
     */
    public function verifyEmailSession(string $verificationCode): array
    {
        try {
            $vendorData = session('vendor_registration');

            if (!$vendorData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            // Check if verification code has expired
            if (now()->timestamp > $vendorData['email_verification_code_expires']) {
                return [
                    'success' => false,
                    'message' => 'Verification code has expired. Please request a new one.',
                ];
            }

            // Verify the code
            if ($vendorData['email_verification_code'] !== $verificationCode) {
                return [
                    'success' => false,
                    'message' => 'Invalid verification code. Please try again.',
                ];
            }

            // Mark email as verified in session
            $vendorData['email_verified'] = true;
            $vendorData['step'] = 2;
            session(['vendor_registration' => $vendorData]);

            return [
                'success' => true,
                'message' => 'Email verified successfully. You can now proceed to phone verification.',
                'next_step' => 'phone_verification',
            ];
        } catch (Exception $e) {
            Log::error('Session email verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Email verification failed. Please try again.',
            ];
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
     * Resend email verification using session data.
     */
    public function resendEmailVerificationSession(): array
    {
        try {
            $vendorData = session('vendor_registration');

            if (!$vendorData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            $personalInfo = $vendorData['personal_info'];

            // Generate new verification code
            $verificationCode = $this->generateVerificationCode();

            // Update session with new code
            $vendorData['email_verification_code'] = $verificationCode;
            $vendorData['email_verification_code_expires'] = now()->addHours(24)->timestamp;
            session(['vendor_registration' => $vendorData]);

            // Send email verification
            $emailResult = $this->emailVerificationService->sendVerificationEmailForTempRegistration(
                $personalInfo['email'],
                $personalInfo['name'],
                $verificationCode,
                'vendor'
            );

            if (!$emailResult['success']) {
                throw new Exception('Failed to send verification email');
            }

            return [
                'success' => true,
                'message' => 'Verification email sent successfully.',
            ];
        } catch (Exception $e) {
            Log::error('Session email verification resend error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send verification email',
            ];
        }
    }

    /**
     * Send phone verification OTP using session data.
     */
    public function sendPhoneVerificationOTPSession(): array
    {
        try {
            $vendorData = session('vendor_registration');

            if (!$vendorData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            if (!$vendorData['email_verified']) {
                return [
                    'success' => false,
                    'message' => 'Please verify your email first.',
                ];
            }

            $phoneNumber = $vendorData['personal_info']['phone'];

            // Send OTP using the OTP service
            $result = $this->otpService->sendOtp($phoneNumber, 'registration');

            if ($result['success']) {
                // Store OTP verification status in session
                $vendorData['phone_otp_sent'] = true;
                $vendorData['phone_otp_sent_at'] = now()->timestamp;
                session(['vendor_registration' => $vendorData]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Session phone OTP send error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send phone verification OTP',
            ];
        }
    }

    /**
     * Verify phone OTP using session data.
     */
    public function verifyPhoneOTPSession(string $otpCode): array
    {
        try {
            $vendorData = session('vendor_registration');

            if (!$vendorData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            if (!$vendorData['email_verified']) {
                return [
                    'success' => false,
                    'message' => 'Please verify your email first.',
                ];
            }

            $phoneNumber = $vendorData['personal_info']['phone'];

            // Verify OTP using the OTP service
            $result = $this->otpService->verifyOtp($phoneNumber, $otpCode);

            if ($result['success']) {
                // Mark phone as verified in session
                $vendorData['phone_verified'] = true;
                $vendorData['step'] = 3;
                session(['vendor_registration' => $vendorData]);

                return [
                    'success' => true,
                    'message' => 'Phone verified successfully. You can now proceed to company information.',
                    'next_step' => 'company_information',
                ];
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Session phone OTP verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to verify phone OTP',
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
        $tempFiles = Cache::get($cacheKey, []);

        Log::info("Retrieved temporary files for registration", [
            'registration_token' => $registrationToken,
            'cache_key' => $cacheKey,
            'files_found' => array_keys($tempFiles),
            'files_count' => count($tempFiles),
        ]);

        // Log details of each file
        foreach ($tempFiles as $fieldName => $fileInfo) {
            Log::info("Temporary file details", [
                'field' => $fieldName,
                'storage_path' => $fileInfo['storage_path'] ?? 'not set',
                'original_name' => $fileInfo['original_name'] ?? 'not set',
                'file_exists' => isset($fileInfo['storage_path']) ? Storage::disk('local')->exists($fileInfo['storage_path']) : false,
            ]);
        }

        return $tempFiles;
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
     * Complete vendor registration using session data - creates user and company in single transaction.
     */
    public function completeVendorRegistrationSession(array $companyData): array
    {
        try {
            $vendorData = session('vendor_registration');

            if (!$vendorData) {
                return [
                    'success' => false,
                    'message' => 'Registration session expired. Please start again.',
                ];
            }

            if (!$vendorData['email_verified'] || !$vendorData['phone_verified']) {
                return [
                    'success' => false,
                    'message' => 'Please complete email and phone verification first.',
                ];
            }

            $personalInfo = $vendorData['personal_info'];

            // Validate unique fields again (in case they were taken during the session)
            $this->validateUniqueFields($personalInfo);
            $this->validateCompanyUniqueFields($companyData);

            DB::beginTransaction();

            // Create the user - vendors are now active immediately after registration
            $user = User::create([
                'name' => $personalInfo['name'],
                'email' => $personalInfo['email'],
                'password' => Hash::make($personalInfo['password']),
                'phone' => $personalInfo['phone'],
                'role' => 'vendor',
                'status' => 'active',
                'registration_step' => 'company_completed',
                'email_verified_at' => now(),
                'phone_verified' => true,
                'phone_verified_at' => now(),
            ]);

            // Create company
            $company = Company::create([
                'user_id' => $user->id,
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

            // Handle logo upload if provided (save to companies.logo)
            if (isset($companyData['logo']) && $companyData['logo'] instanceof UploadedFile) {
                $logoPath = $this->uploadCompanyLogo($companyData['logo'], $company->id);
                $company->update(['logo' => $logoPath]); // Save to companies.logo
            }

            DB::commit();

            // Clear session data
            session()->forget('vendor_registration');

            return [
                'success' => true,
                'message' => 'Registration completed successfully! You can now log in to access your dashboard.',
                'user_id' => $user->id,
                'company_id' => $company->id,
                'next_step' => 'completed',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Session vendor registration completion error: ' . $e->getMessage());
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

            // Use provided dates or calculate defaults (updated field names)
            $startDate = $licenseData['start_date'] ?? Carbon::now()->toDateString();
            $endDate = $licenseData['end_date'] ?? Carbon::now()->addYear()->toDateString();

            // Validate that start date is not after end date
            if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
                throw new \InvalidArgumentException('License start date cannot be after the expiration date.');
            }

            // Calculate duration in days
            $duration = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
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
                'status' => 'pending', // Set to pending for admin review
                'renewal_date' => $renewalDate,
                'notes' => $licenseData['notes'] ?? null,
            ]);

            // Update user registration step
            $user->update([
                'registration_step' => 'license_completed',
                'status' => 'pending', // Set to pending for admin review
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

            // Use provided start date or default to current date
            $startDate = $licenseData['license_start_date'] ?? Carbon::now()->toDateString();

            // Use provided expiry date or calculate from duration_days (for backward compatibility)
            if (isset($licenseData['license_expiry_date'])) {
                $endDate = $licenseData['license_expiry_date'];
                $duration = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
            } else {
                $duration = (int)($licenseData['duration_days'] ?? 365); // Cast to int to fix Carbon type error
                $endDate = Carbon::parse($startDate)->addDays($duration)->toDateString();
            }
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
                'status' => $licenseData['license_status'] ?? 'pending', // Use passed status or default to pending for admin review
                'renewal_date' => $renewalDate,
                'notes' => $licenseData['notes'] ?? null,
            ]);

            // Update user and provider status based on license status
            $licenseStatus = $licenseData['license_status'] ?? 'pending';
            $userStatus = $licenseStatus === 'active' ? 'active' : 'pending';
            $providerStatus = $licenseStatus === 'active' ? 'active' : 'pending';

            $user->update([
                'registration_step' => 'license_completed',
                'status' => $userStatus,
            ]);

            $user->provider()->update(['status' => $providerStatus]);

            DB::commit();

            // Prepare response message based on license status
            $message = $licenseStatus === 'active'
                ? 'License uploaded successfully. Registration completed!'
                : 'License uploaded successfully. Your license is now under review by our admin team.';

            return [
                'success' => true,
                'message' => $message,
                'license_id' => $license->id,
                'next_step' => $licenseStatus === 'active' ? 'registration_complete' : 'verification_pending',
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

            // Use provided dates
            $startDate = $licenseData['license_start_date'];
            $endDate = $licenseData['license_end_date'];
            $renewalDate = $endDate;

            // Calculate duration in days
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = Carbon::parse($endDate);
            $duration = $startCarbon->diffInDays($endCarbon);

            // Create license record
            $license = License::create([
                'user_id' => $user->id,
                'license_type' => $licenseData['license_type'] ?? 'registration',
                'license_file_path' => $licensePath,
                'license_file_name' => $licenseData['license_file']->getClientOriginalName(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_days' => $duration,
                'status' => $licenseData['license_status'] ?? 'pending', // Use passed status or default to pending for admin review
                'renewal_date' => $renewalDate,
                'notes' => $licenseData['notes'] ?? null,
            ]);

            // Update user and merchant status based on license status
            $licenseStatus = $licenseData['license_status'] ?? 'pending';
            $userStatus = $licenseStatus === 'active' ? 'active' : 'pending';
            $merchantStatus = $licenseStatus === 'active' ? 'active' : 'pending';

            $user->update([
                'registration_step' => 'license_completed',
                'status' => $userStatus,
            ]);

            // Update merchant status and license information if exists
            if ($user->merchant) {
                $user->merchant()->update([
                    'status' => $merchantStatus,
                    'license_file' => $licensePath,
                    'license_start_date' => $startDate,
                    'license_expiry_date' => $endDate,
                    'license_status' => $licenseStatus === 'active' ? 'verified' : 'checking',
                    'license_verified' => $licenseStatus === 'active',
                    'license_uploaded_at' => now(),
                    'license_rejection_reason' => null,
                    'license_approved_at' => $licenseStatus === 'active' ? now() : null,
                    'license_approved_by' => null,
                ]);
            }

            DB::commit();

            // Prepare response message based on license status
            $message = $licenseStatus === 'active'
                ? 'License uploaded successfully. Registration completed!'
                : 'License uploaded successfully. Your license is now under review by our admin team.';

            return [
                'success' => true,
                'message' => $message,
                'license_id' => $license->id,
                'next_step' => $licenseStatus === 'active' ? 'registration_complete' : 'verification_pending',
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
        $file->storeAs('companies', $filename, 'public');
        return 'storage/companies/' . $filename;
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
     * Upload user profile image.
     */
    private function uploadUserProfileImage(UploadedFile $file, int $userId): string
    {
        try {
            // Use the storage/users directory to match ImageHelper expectations
            $destinationPath = public_path('storage/users');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $fileName = time() . '_' . $file->getClientOriginalName();

            // Move the file directly to the public directory
            $file->move($destinationPath, $fileName);

            // Return the relative path for database storage (storage/users pattern)
            return "storage/users/{$fileName}";
        } catch (Exception $e) {
            Log::error("Failed to upload user profile image", [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Move temporary file to user profile directory.
     */
    private function moveTemporaryFileToUserProfile(array $fileInfo, int $userId): string
    {
        try {
            // Normalize the path to handle mixed separators
            $tempFilePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $fileInfo['path']);

            Log::info("Starting moveTemporaryFileToUserProfile", [
                'user_id' => $userId,
                'fileInfo' => $fileInfo,
                'original_path' => $fileInfo['path'],
                'normalized_path' => $tempFilePath,
                'temp_file_exists' => file_exists($tempFilePath),
                'storage_exists' => Storage::disk('local')->exists($fileInfo['storage_path'] ?? ''),
            ]);

            // Check if file exists using Laravel Storage (more reliable)
            if (!isset($fileInfo['storage_path']) || !Storage::disk('local')->exists($fileInfo['storage_path'])) {
                throw new Exception("Temporary file not found in storage: " . ($fileInfo['storage_path'] ?? 'not set'));
            }

            // Use the storage/users directory to match ImageHelper expectations
            $destinationPath = public_path('storage/users');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
                Log::info("Created users directory", ['path' => $destinationPath]);
            }

            $extension = pathinfo($fileInfo['original_name'], PATHINFO_EXTENSION);
            $fileName = time() . "_user_{$userId}_profile.{$extension}";
            $destinationFile = "{$destinationPath}/{$fileName}";

            Log::info("Attempting to move file using Storage", [
                'storage_path' => $fileInfo['storage_path'],
                'destination' => $destinationFile,
                'destination_dir_exists' => file_exists($destinationPath),
                'destination_dir_writable' => is_writable($destinationPath),
            ]);

            // Get file contents from storage and write to destination
            $fileContents = Storage::disk('local')->get($fileInfo['storage_path']);
            if (file_put_contents($destinationFile, $fileContents) !== false) {
                Log::info("File moved successfully using Storage", [
                    'destination' => $destinationFile,
                    'relative_path' => "storage/users/{$fileName}",
                ]);
                // Return the relative path for database storage (storage/users pattern)
                return "storage/users/{$fileName}";
            } else {
                throw new Exception("Failed to write file to user profile directory");
            }
        } catch (Exception $e) {
            Log::error("Failed to move temporary file to user profile", [
                'user_id' => $userId,
                'temp_file' => $fileInfo['path'] ?? 'not set',
                'storage_path' => $fileInfo['storage_path'] ?? 'not set',
                'storage_exists' => isset($fileInfo['storage_path']) ? Storage::disk('local')->exists($fileInfo['storage_path']) : false,
                'error' => $e->getMessage(),
                'fileInfo' => $fileInfo,
            ]);
            throw $e;
        }
    }

    /**
     * Create merchant profile.
     */
    private function createMerchantProfile(User $user, array $userData, array $tempFiles = []): void
    {
        Log::info("Creating merchant profile", [
            'user_id' => $user->id,
            'business_name' => $userData['name'],
            'temp_files_count' => count($tempFiles),
            'temp_files_fields' => array_keys($tempFiles),
        ]);

        $merchantData = [
            'user_id' => $user->id,
            'business_name' => $userData['name'],
            'status' => 'pending',
            'is_verified' => false,
            'delivery_capability' => $userData['delivery_capability'] ?? false,
            'emirate' => $userData['emirate'] ?? null,
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
            // Check if file exists using Laravel Storage system (consistent with moveTemporaryFileToFinal)
            if (isset($fileInfo['storage_path']) && Storage::disk('local')->exists($fileInfo['storage_path'])) {
                Log::info("Processing temporary file for merchant", [
                    'field' => $fieldName,
                    'storage_path' => $fileInfo['storage_path'],
                    'merchant_id' => $merchant->id,
                    'user_id' => $user->id,
                ]);

                try {
                    switch ($fieldName) {
                        case 'logo':
                            $logoPath = $this->moveTemporaryFileToFinal($fileInfo, 'images/merchants', 'merchant_' . $merchant->id . '_logo');
                            $merchant->update(['logo' => $logoPath]);
                            Log::info("Merchant logo updated", [
                                'merchant_id' => $merchant->id,
                                'logo_path' => $logoPath,
                            ]);
                            break;
                        case 'uae_id_front':
                            $frontPath = $this->moveTemporaryFileToFinal($fileInfo, 'images/uae_ids', 'uae_id_front_' . $user->id);
                            $merchant->update(['uae_id_front' => $frontPath]);
                            Log::info("Merchant UAE ID front updated", [
                                'merchant_id' => $merchant->id,
                                'uae_id_front_path' => $frontPath,
                            ]);
                            break;
                        case 'uae_id_back':
                            $backPath = $this->moveTemporaryFileToFinal($fileInfo, 'images/uae_ids', 'uae_id_back_' . $user->id);
                            $merchant->update(['uae_id_back' => $backPath]);
                            Log::info("Merchant UAE ID back updated", [
                                'merchant_id' => $merchant->id,
                                'uae_id_back_path' => $backPath,
                            ]);
                            break;
                    }
                } catch (Exception $e) {
                    Log::error("Failed to process temporary file for merchant", [
                        'field' => $fieldName,
                        'merchant_id' => $merchant->id,
                        'error' => $e->getMessage(),
                        'file_info' => $fileInfo,
                    ]);
                    // Continue with other files, don't fail the entire registration
                }
            } else {
                Log::warning("Temporary file not found for merchant", [
                    'field' => $fieldName,
                    'storage_path' => $fileInfo['storage_path'] ?? 'not set',
                    'merchant_id' => $merchant->id,
                    'file_info' => $fileInfo,
                ]);
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
            'business_name' => $userData['business_name'] ?? $user->name,
            'business_type' => $userData['business_type'] ?? 'general',
            'description' => $userData['description'] ?? null,
            'delivery_capability' => $userData['delivery_capability'] ?? false,
            'status' => 'pending',
            'is_verified' => false,
        ];

        // Handle delivery fees if delivery capability is enabled
        if (isset($userData['delivery_capability']) && $userData['delivery_capability']) {
            if (isset($userData['delivery_fees']) && is_array($userData['delivery_fees'])) {
                $providerData['delivery_fees'] = $userData['delivery_fees'];
            }
        }

        // Handle stock locations if provided
        if (isset($userData['stock_locations']) && is_array($userData['stock_locations'])) {
            $providerData['stock_locations'] = $userData['stock_locations'];
        }

        // Handle logo upload if provided - save to user's profile_image
        $logoPath = null;

        Log::info("Processing logo upload for provider", [
            'user_id' => $user->id,
            'has_userData_logo' => isset($userData['logo']),
            'userData_logo_type' => isset($userData['logo']) ? get_class($userData['logo']) : 'not set',
            'has_tempFiles_logo' => isset($tempFiles['logo']),
            'tempFiles_logo_path' => isset($tempFiles['logo']['path']) ? $tempFiles['logo']['path'] : 'not set',
            'tempFiles_logo_exists' => isset($tempFiles['logo']['path']) ? file_exists($tempFiles['logo']['path']) : false,
        ]);

        if (isset($userData['logo']) && $userData['logo'] instanceof UploadedFile) {
            // Direct UploadedFile object (for backward compatibility)
            Log::info("Using direct UploadedFile for logo upload");
            $logoPath = $this->uploadUserProfileImage($userData['logo'], $user->id);
        } elseif (isset($tempFiles['logo']) && isset($tempFiles['logo']['path'])) {
            // Temporary file from registration process
            Log::info("Using temporary file for logo upload", [
                'temp_file_path' => $tempFiles['logo']['path'],
                'original_name' => $tempFiles['logo']['original_name'] ?? 'unknown',
            ]);
            $logoPath = $this->moveTemporaryFileToUserProfile($tempFiles['logo'], $user->id);
        } else {
            Log::warning("No valid logo file found for upload", [
                'tempFiles_keys' => array_keys($tempFiles),
                'userData_keys' => array_keys($userData),
            ]);
        }

        // Update user's profile_image if logo was uploaded
        if ($logoPath) {
            Log::info("Updating user profile_image", [
                'user_id' => $user->id,
                'logo_path' => $logoPath,
            ]);
            $user->update(['profile_image' => $logoPath]);
            // Also save to provider's logo field for backward compatibility
            $providerData['logo'] = $logoPath;
        } else {
            Log::warning("No logo path generated, skipping profile_image update", [
                'user_id' => $user->id,
            ]);
        }

        Provider::create($providerData);

        Log::info("Provider profile created", [
            'user_id' => $user->id,
            'business_name' => $userData['business_name'] ?? $user->name,
            'business_type' => $userData['business_type'] ?? 'general',
            'delivery_capability' => $userData['delivery_capability'] ?? false,
            'delivery_fees_count' => isset($userData['delivery_fees']) ? count($userData['delivery_fees']) : 0,
            'stock_locations_count' => isset($userData['stock_locations']) ? count($userData['stock_locations']) : 0,
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
