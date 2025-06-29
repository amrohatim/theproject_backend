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
     * Show provider registration form.
     */
    public function showProviderRegistration()
    {
        return view('auth.provider.register');
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
                return redirect()->route('vendor.otp.verify.temp', ['token' => $request->token])
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
    public function registerProvider(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => 'required|string|max:20|unique:users,phone',
                'password' => 'required|string|min:8|confirmed',
                'business_name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'deliver_to_vendor_capability' => 'boolean',
                'delivery_fees' => 'nullable|array',
                'stock_locations' => 'nullable|array',
                'stock_locations.*.name' => 'required_with:stock_locations|string|max:255',
                'stock_locations.*.address' => 'required_with:stock_locations|string|max:500',
                'stock_locations.*.emirate' => 'required_with:stock_locations|string|max:100',
                'stock_locations.*.city' => 'required_with:stock_locations|string|max:100',
                'stock_locations.*.latitude' => 'nullable|numeric|between:-90,90',
                'stock_locations.*.longitude' => 'nullable|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $result = $this->registrationService->startProviderRegistration($request->all());

            if ($result['success']) {
                return redirect()->route('provider.registration.license', ['user_id' => $result['user_id']])
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors(['error' => $result['message']])->withInput();
            }
        } catch (Exception $e) {
            Log::error('Web provider registration error: ' . $e->getMessage());
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
                'duration_days' => 'nullable|integer|min:1|max:3650',
                'notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $result = $this->registrationService->completeProviderLicense(
                $request->user_id,
                $request->file('license_file'),
                $request->only(['duration_days', 'notes'])
            );

            if ($result['success']) {
                // Auto-login the user after successful registration
                $user = User::findOrFail($request->user_id);
                Auth::login($user);

                return redirect()->route('provider.dashboard')
                    ->with('success', 'Registration completed successfully! Welcome to your dashboard.');
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
                'duration_days' => 'nullable|integer|min:1|max:3650',
                'notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = User::findOrFail($request->user_id);
            $result = $this->registrationService->uploadMerchantLicense($user, [
                'license_file' => $request->file('license_file'),
                'duration_days' => $request->duration_days,
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
}
