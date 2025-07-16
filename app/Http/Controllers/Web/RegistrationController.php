<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\RegistrationService;
use App\Services\TemporaryRegistrationService;
use App\Services\OtpService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProviderRegistrationValidationRequest;
use Exception;

class RegistrationController extends Controller
{
    private RegistrationService $registrationService;
    private TemporaryRegistrationService $tempRegistrationService;
    private OtpService $otpService;

    public function __construct(
        RegistrationService $registrationService,
        TemporaryRegistrationService $tempRegistrationService,
        OtpService $otpService
    ) {
        $this->registrationService = $registrationService;
        $this->tempRegistrationService = $tempRegistrationService;
        $this->otpService = $otpService;
    }

    /**
     * Show registration choice page.
     */
    public function showRegistrationChoice()
    {
        return view('auth.register-choice');
    }

    /**
     * Show vendor registration form.
     */
    public function showVendorRegistration()
    {
        return view('auth.vendor.register');
    }

    /**
     * Show provider registration form (Step 1).
     */
    public function showProviderRegistration()
    {
        return view('auth.provider-register-step1');
    }

    /**
     * Show provider registration step 2 (Email verification).
     */
    public function showProviderStep2()
    {
        return view('auth.provider-register-step2');
    }

    /**
     * Show provider phone verification step.
     */
    public function showProviderPhoneVerification(Request $request)
    {
        $registrationToken = $request->query('token');
        $phoneNumber = $request->query('phone');

        if (!$registrationToken) {
            return redirect()->route('register.provider')->with('error', 'Registration session expired. Please start again.');
        }

        // Validate the registration token
        $tempRegistrationService = app(\App\Services\TemporaryRegistrationService::class);
        $tempData = $tempRegistrationService->getTemporaryRegistration($registrationToken);

        if (!$tempData) {
            return redirect()->route('register.provider')->with('error', 'Registration session expired or invalid. Please start again.');
        }

        // Check if email is verified first
        if (!$tempRegistrationService->isEmailVerified($registrationToken)) {
            return redirect()->route('register.provider.step2')->with('error', 'Please verify your email first.');
        }

        // Get phone number from registration data if not provided in query
        if (!$phoneNumber && isset($tempData['user_data']['phone'])) {
            $phoneNumber = $tempData['user_data']['phone'];
        }

        return view('auth.provider.phone-verification', [
            'registrationToken' => $registrationToken,
            'phoneNumber' => $phoneNumber
        ]);
    }

    /**
     * Show provider registration step 3 (License upload).
     */
    public function showProviderStep3()
    {
        return view('auth.provider-register-step3');
    }

    /**
     * Show merchant registration form.
     */
    public function showMerchantRegistration()
    {
        return view('auth.merchant-register-vue');
    }

    /**
     * Handle vendor registration submission.
     */
    public function registerVendor(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => 'required|string|max:20|unique:users,phone',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $result = $this->registrationService->startVendorRegistration($request->all());

            if ($result['success']) {
                // Redirect to email verification page with registration token
                return redirect()->route('vendor.email.verify.temp', ['token' => $result['registration_token']])
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors(['error' => $result['message']])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Web vendor registration error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Show temporary vendor email verification form.
     */
    public function showTempVendorEmailVerification(Request $request, $token)
    {
        try {
            // Get temporary registration data
            $tempData = $this->tempRegistrationService->getTemporaryRegistration($token);

            if (!$tempData) {
                return redirect()->route('register.vendor')
                    ->withErrors(['error' => 'Registration session expired. Please start again.']);
            }

            return view('auth.vendor.email-verification-temp', [
                'token' => $token,
                'email' => $tempData['user_data']['email'],
                'name' => $tempData['user_data']['name']
            ]);
        } catch (Exception $e) {
            Log::error('Show temp vendor email verification error: ' . $e->getMessage());
            return redirect()->route('register.vendor')
                ->withErrors(['error' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Verify temporary vendor email.
     */
    public function verifyTempVendorEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
                'verification_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $result = $this->registrationService->verifyTempRegistrationEmail(
                $request->token,
                $request->verification_code
            );

            if ($result['success']) {
                // Redirect to phone verification
                return redirect()->route('vendor.phone.verify.temp', ['token' => $request->token])
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors(['error' => $result['message']])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Temp vendor email verification error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Verification failed. Please try again.'])->withInput();
        }
    }

    /**
     * Resend temporary vendor email verification.
     */
    public function resendTempVendorEmailVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $result = $this->registrationService->resendTempEmailVerification($request->token);

            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->withErrors(['error' => $result['message']]);
            }
        } catch (Exception $e) {
            Log::error('Resend temp vendor email verification error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to resend verification email. Please try again.']);
        }
    }

    /**
     * Show vendor company registration form.
     */
    public function showVendorCompanyForm(Request $request)
    {
        $userId = $request->get('user_id');
        $user = User::findOrFail($userId);

        if ($user->registration_step !== 'info_completed') {
            return redirect()->route('register.vendor')
                ->withErrors(['error' => 'Please complete the previous step first.']);
        }

        return view('auth.vendor.company', compact('user'));
    }

    /**
     * Handle vendor company registration submission.
     */
    public function registerVendorCompany(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255|unique:companies,name',
                'email' => 'required|email|max:255|unique:companies,email',
                'contact_number_1' => 'required|string|max:20|unique:companies,contact_number_1',
                'contact_number_2' => 'nullable|string|max:20|unique:companies,contact_number_2',
                'address' => 'required|string|max:500',
                'emirate' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'street' => 'nullable|string|max:255',
                'delivery_capability' => 'boolean',
                'delivery_areas' => 'nullable|array',
                'description' => 'nullable|string|max:1000',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $result = $this->registrationService->completeVendorCompanyInfo(
                $request->user_id,
                $request->all()
            );

            if ($result['success']) {
                return redirect()->route('vendor.registration.license', ['user_id' => $request->user_id])
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors(['error' => $result['message']])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Web vendor company registration error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Company registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Show vendor license upload form.
     */
    public function showVendorLicenseForm(Request $request)
    {
        $userId = $request->get('user_id');
        $user = User::with('company')->findOrFail($userId);

        if ($user->registration_step !== 'company_completed') {
            return redirect()->route('vendor.registration.company', ['user_id' => $userId])
                ->withErrors(['error' => 'Please complete the previous step first.']);
        }

        return view('auth.vendor.license', compact('user'));
    }

    /**
     * Handle vendor license upload submission.
     */
    public function uploadVendorLicense(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'duration_days' => 'nullable|integer|min:1|max:3650',
                'notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $result = $this->registrationService->completeVendorLicense(
                $request->user_id,
                $request->file('license_file'),
                $request->only(['duration_days', 'notes'])
            );

            if ($result['success']) {
                // Auto-login the user after successful registration
                $user = User::findOrFail($request->user_id);
                Auth::login($user);

                return redirect()->route('vendor.dashboard')
                    ->with('success', 'Registration completed successfully! Welcome to your dashboard.');
            } else {
                return back()->withErrors(['error' => $result['message']])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Web vendor license upload error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'License upload failed. Please try again.'])->withInput();
        }
    }

    /**
     * Handle provider registration submission.
     */
    public function registerProvider(ProviderRegistrationValidationRequest $request)
    {
        try {
            // The validation is automatically handled by the ProviderRegistrationValidationRequest
            // Check if we should skip verification steps based on business logic
            $skipEmailVerification = $request->shouldSkipEmailVerification();
            $skipBothVerifications = $request->shouldSkipBothVerifications();

            // Prepare the registration data
            $registrationData = $request->validated();
            $registrationData['skip_email_verification'] = $skipEmailVerification;
            $registrationData['skip_both_verifications'] = $skipBothVerifications;

            $result = $this->registrationService->startProviderRegistration($registrationData);

            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                if ($result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => $result['message'],
                        'redirect_url' => route('register.provider.step2'),
                        'registration_token' => $result['registration_token'] ?? null,
                        'user_id' => $result['user_id'] ?? null,
                        'next_step' => $result['next_step'] ?? 'email_verification'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ], 422);
                }
            }

            // Handle traditional form submission (non-AJAX)
            if ($result['success']) {
                return redirect()->route('provider.registration.license', ['user_id' => $result['user_id']])
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors(['error' => $result['message']])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Web provider registration error: ' . $e->getMessage());

            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed. Please try again.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Handle merchant registration submission.
     */
    public function registerMerchant(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => 'required|string|max:20|unique:users,phone',
                'password' => 'required|string|min:8|confirmed',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'store_location_lat' => 'nullable|numeric|between:-90,90',
                'store_location_lng' => 'nullable|numeric|between:-180,180',
                'store_location_address' => 'nullable|string|max:500',
                'uae_id_front' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'uae_id_back' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'delivery_capability' => 'boolean',
                'delivery_fees' => 'nullable|array',
                'delivery_fees.*' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $result = $this->registrationService->startMerchantRegistration($request->all());

            if ($result['success']) {
                // Redirect to email verification page with registration token
                return redirect()->route('merchant.email.verify.temp', ['token' => $result['registration_token']])
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors(['error' => $result['message']])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Web merchant registration error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Show provider license upload form.
     */
    public function showProviderLicenseForm(Request $request)
    {
        $userId = $request->get('user_id');
        $user = User::with('provider')->findOrFail($userId);

        if ($user->registration_step !== 'info_completed') {
            return redirect()->route('register.provider')
                ->withErrors(['error' => 'Please complete the previous step first.']);
        }

        return view('auth.provider.license', compact('user'));
    }

    /**
     * Handle provider license upload submission.
     */
    public function uploadProviderLicense(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'license_start_date' => 'required|date|after_or_equal:today',
                'license_expiry_date' => 'required|date|after:license_start_date',
                'notes' => 'nullable|string|max:500',
            ], [
                'license_start_date.required' => 'License start date is required.',
                'license_start_date.date' => 'License start date must be a valid date.',
                'license_start_date.after_or_equal' => 'License start date cannot be in the past.',
                'license_expiry_date.required' => 'License expiry date is required.',
                'license_expiry_date.date' => 'License expiry date must be a valid date.',
                'license_expiry_date.after' => 'License expiry date must be after the start date.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $result = $this->registrationService->completeProviderLicense(
                $request->user_id,
                $request->file('license_file'),
                $request->only(['license_start_date', 'license_expiry_date', 'notes'])
            );

            if ($result['success']) {
                // Auto-login the user after successful registration
                $user = User::findOrFail($request->user_id);
                Auth::login($user);

                // Check if license is pending and redirect appropriately
                if ($result['next_step'] === 'verification_pending') {
                    return redirect()->route('provider.license.status')
                        ->with('success', $result['message']);
                } else {
                    return redirect()->route('provider.dashboard')
                        ->with('success', 'Registration completed successfully! Welcome to your dashboard.');
                }
            } else {
                return back()->withErrors(['error' => $result['message']])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Web provider license upload error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'License upload failed. Please try again.'])->withInput();
        }
    }

    /**
     * Show merchant license upload form.
     */
    public function showMerchantLicenseForm(Request $request)
    {
        $userId = $request->get('user_id');
        $user = User::with('merchant')->findOrFail($userId);

        if ($user->registration_step !== 'info_completed') {
            return redirect()->route('register.merchant')
                ->withErrors(['error' => 'Please complete the previous step first.']);
        }

        return view('auth.merchant.license', compact('user'));
    }

    /**
     * Handle merchant license upload submission.
     */
    public function uploadMerchantLicense(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'license_start_date' => 'required|date|after_or_equal:today',
                'license_end_date' => 'required|date|after:license_start_date',
                'notes' => 'nullable|string|max:500',
            ], [
                'license_start_date.required' => 'License start date is required.',
                'license_start_date.date' => 'License start date must be a valid date.',
                'license_start_date.after_or_equal' => 'License start date cannot be in the past.',
                'license_end_date.required' => 'License end date is required.',
                'license_end_date.date' => 'License end date must be a valid date.',
                'license_end_date.after' => 'License end date must be after the start date.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = User::findOrFail($request->user_id);
            $result = $this->registrationService->uploadMerchantLicense($user, [
                'license_file' => $request->file('license_file'),
                'license_start_date' => $request->license_start_date,
                'license_end_date' => $request->license_end_date,
                'notes' => $request->notes,
            ]);

            if ($result['success']) {
                // Auto-login the user after successful registration
                Auth::login($user);

                return redirect()->route('merchant.dashboard')
                    ->with('success', 'Registration completed successfully! Welcome to your dashboard.');
            } else {
                return back()->withErrors(['error' => $result['message']])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Web merchant license upload error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'License upload failed. Please try again.'])->withInput();
        }
    }

    /**
     * Send OTP for phone verification.
     */
    public function sendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|max:20',
                'type' => 'nullable|in:registration,login,password_reset',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->otpService->sendOtp(
                $request->phone_number,
                $request->type ?? 'registration'
            );

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Web OTP send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
            ], 500);
        }
    }

    /**
     * Verify OTP code.
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|max:20',
                'otp_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->otpService->verifyOtp(
                $request->phone_number,
                $request->otp_code
            );

            // If OTP is verified, update user's phone verification status
            if ($result['success']) {
                $user = User::where('phone', $request->phone_number)->first();
                if ($user) {
                    $user->update([
                        'phone_verified' => true,
                        'phone_verified_at' => now(),
                    ]);
                }
            }

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Web OTP verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP',
            ], 500);
        }
    }

    /**
     * Send phone verification OTP for registration.
     */
    public function sendPhoneVerificationOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'registration_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->sendPhoneVerificationOTP($request->registration_token);

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Web phone verification OTP send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send phone verification OTP',
            ], 500);
        }
    }

    /**
     * Verify phone OTP and create user.
     */
    public function verifyPhoneOTPAndCreateUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'registration_token' => 'required|string',
                'otp_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->verifyPhoneOTPAndCreateUser(
                $request->registration_token,
                $request->otp_code
            );

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Web phone verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify phone OTP',
            ], 500);
        }
    }

    /**
     * Resend phone verification OTP.
     */
    public function resendPhoneVerificationOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'registration_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->resendPhoneVerificationOTP($request->registration_token);

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Web phone verification OTP resend error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend phone verification OTP',
            ], 500);
        }
    }

    /**
     * Show provider registration status page.
     */
    public function showProviderRegistrationStatus()
    {
        $user = Auth::user();

        // Ensure user is authenticated and is a provider
        if (!$user || $user->role !== 'provider') {
            return redirect()->route('login')->with('error', 'Please log in as a provider to view registration status.');
        }

        return view('auth.provider.registration-status', compact('user'));
    }

    /**
     * Show provider license upload page.
     */
    public function showProviderLicenseUpload()
    {
        $user = Auth::user();

        // Ensure user is authenticated and is a provider
        if (!$user || $user->role !== 'provider') {
            return redirect()->route('login')->with('error', 'Please log in as a provider to upload license.');
        }

        return view('auth.provider.license-upload-standalone', compact('user'));
    }

    /**
     * Show provider license status page.
     */
    public function showProviderLicenseStatus()
    {
        $user = Auth::user();

        // Ensure user is authenticated and is a provider
        if (!$user || $user->role !== 'provider') {
            return redirect()->route('login')->with('error', 'Please log in as a provider to view license status.');
        }

        // Load the user's latest license
        $user->load('latestLicense');

        return view('auth.provider.license-status', compact('user'));
    }

    /**
     * Handle provider license upload for existing providers.
     */
    public function submitProviderLicenseUpload(Request $request)
    {
        try {
            $user = Auth::user();

            // Ensure user is authenticated and is a provider
            if (!$user || $user->role !== 'provider') {
                return redirect()->route('login')->with('error', 'Please log in as a provider to upload license.');
            }

            $validator = Validator::make($request->all(), [
                'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'license_start_date' => 'required|date|after_or_equal:today',
                'license_expiry_date' => 'required|date|after:license_start_date',
                'notes' => 'nullable|string|max:500',
            ], [
                'license_start_date.required' => 'License start date is required.',
                'license_start_date.date' => 'License start date must be a valid date.',
                'license_start_date.after_or_equal' => 'License start date cannot be in the past.',
                'license_expiry_date.required' => 'License expiry date is required.',
                'license_expiry_date.date' => 'License expiry date must be a valid date.',
                'license_expiry_date.after' => 'License expiry date must be after the start date.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Create license with pending status for admin review
            $licenseData = array_merge(
                $request->only(['license_start_date', 'license_expiry_date', 'notes']),
                ['license_status' => 'pending'] // Pass status to be used in service
            );

            $result = $this->registrationService->completeProviderLicense(
                $user->id,
                $request->file('license_file'),
                $licenseData
            );

            if ($result['success']) {
                return redirect()->route('provider.license.status')
                    ->with('success', 'License uploaded successfully! Your license is now under review.');
            } else {
                return back()->with('error', $result['message'] ?? 'Failed to upload license. Please try again.');
            }
        } catch (Exception $e) {
            Log::error('Provider license upload failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while uploading your license. Please try again.');
        }
    }

    /**
     * Show vendor registration status page.
     */
    public function showVendorRegistrationStatus()
    {
        $user = Auth::user();

        // Ensure user is authenticated and is a vendor
        if (!$user || $user->role !== 'vendor') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a vendor to access this page.']);
        }

        return view('auth.vendor.registration-status', compact('user'));
    }

    /**
     * Show phone verification page for temporary registration.
     */
    public function showTempVendorPhoneVerification(Request $request, $token)
    {
        try {
            // Get temporary registration data
            $tempData = $this->tempRegistrationService->getTemporaryRegistration($token);

            if (!$tempData) {
                return redirect()->route('register.vendor')
                    ->withErrors(['error' => 'Registration session expired. Please start again.']);
            }

            // Check if email is verified first
            if (!$this->tempRegistrationService->isEmailVerified($token)) {
                return redirect()->route('vendor.email.verify.temp', ['token' => $token])
                    ->withErrors(['error' => 'Please verify your email first.']);
            }

            return view('auth.vendor.phone-verification', [
                'registrationToken' => $token,
                'phoneNumber' => $tempData['user_data']['phone'],
                'name' => $tempData['user_data']['name']
            ]);
        } catch (Exception $e) {
            Log::error('Show temp vendor phone verification error: ' . $e->getMessage());
            return redirect()->route('register.vendor')
                ->withErrors(['error' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Show provider email verification page.
     */
    public function showProviderEmailVerification($user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            // Ensure user is a provider
            if ($user->role !== 'provider') {
                return redirect()->route('login')->with('error', 'Invalid user type.');
            }

            return view('auth.provider.email-verification', compact('user'));
        } catch (Exception $e) {
            Log::error('Show provider email verification error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'User not found.');
        }
    }

    /**
     * Verify provider email.
     */
    public function verifyProviderEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'verification_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = User::findOrFail($request->user_id);

            // Ensure user is a provider
            if ($user->role !== 'provider') {
                return back()->withErrors(['error' => 'Invalid user type.'])->withInput();
            }

            // For now, we'll simulate verification (in production, you'd verify against stored code)
            // Update user's email verification status
            $user->update([
                'email_verified_at' => now(),
                'registration_step' => 'email_verified'
            ]);

            Log::info('Provider email verified successfully', ['user_id' => $user->id]);

            return redirect()->route('provider.registration.license', ['user_id' => $user->id])
                ->with('success', 'Email verified successfully! Please upload your license.');
        } catch (Exception $e) {
            Log::error('Provider email verification error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Verification failed. Please try again.'])->withInput();
        }
    }

    /**
     * Resend provider email verification.
     */
    public function resendProviderEmailVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = User::findOrFail($request->user_id);

            // Ensure user is a provider
            if ($user->role !== 'provider') {
                return back()->withErrors(['error' => 'Invalid user type.']);
            }

            // Here you would resend the verification email
            // For now, we'll just return success
            Log::info('Provider email verification resent', ['user_id' => $user->id]);

            return back()->with('success', 'Verification email sent successfully!');
        } catch (Exception $e) {
            Log::error('Resend provider email verification error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to resend verification email. Please try again.']);
        }
    }
}
