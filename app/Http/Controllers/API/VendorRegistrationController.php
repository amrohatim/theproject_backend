<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\RegistrationService;
use App\Services\OtpService;
use App\Services\TemporaryRegistrationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class VendorRegistrationController extends Controller
{
    private RegistrationService $registrationService;
    private OtpService $otpService;

    public function __construct(RegistrationService $registrationService, OtpService $otpService)
    {
        $this->registrationService = $registrationService;
        $this->otpService = $otpService;
    }

    /**
     * Step 1: Register vendor basic information (session-based).
     */
    public function registerVendorInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => 'required|string|max:20|unique:users,phone',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->startVendorRegistrationSession($request->all());

            return response()->json($result, 201);
        } catch (Exception $e) {
            Log::error('Vendor registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Step 2: Verify email (session-based).
     */
    public function verifyEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'verification_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->verifyEmailSession($request->verification_code);

            if ($result['success']) {
                return response()->json($result, 200);
            } else {
                return response()->json($result, 400);
            }
        } catch (Exception $e) {
            Log::error('Email verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Email verification failed',
            ], 500);
        }
    }

    /**
     * Step 4: Register vendor company information and complete registration (session-based).
     */
    public function registerCompanyInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
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
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->completeVendorRegistrationSession($request->all());

            // Store user_id in session for license upload step
            if ($result['success'] && isset($result['user_id'])) {
                session(['vendor_license_upload' => [
                    'user_id' => $result['user_id'],
                    'created_at' => now()->timestamp,
                ]]);
            }

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Company registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Step 3: Upload vendor license.
     * Supports both traditional flow (with user_id) and session-based flow.
     */
    public function uploadLicense(Request $request)
    {
        try {
            // Check if we have user_id from request or need to get it from session
            $userId = $request->user_id;

            // If no user_id provided, check for session-based registration data
            if (!$userId) {
                $sessionData = session('vendor_license_upload');

                if ($sessionData && isset($sessionData['user_id'])) {
                    $userId = $sessionData['user_id'];
                } else {
                    // Try to find user by email from session data
                    $tempRegistrationData = session('vendor_temp_registration');
                    if ($tempRegistrationData && isset($tempRegistrationData['email'])) {
                        $user = User::where('email', $tempRegistrationData['email'])
                                   ->where('role', 'vendor')
                                   ->first();
                        if ($user) {
                            $userId = $user->id;
                        }
                    }

                    // If still no user found, return helpful error
                    if (!$userId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unable to identify user for license upload. Please restart the registration process.',
                            'errors' => ['session' => ['Registration session expired or invalid']],
                        ], 422);
                    }
                }
            }

            // Validate the user exists and is a vendor
            $user = User::where('id', $userId)->where('role', 'vendor')->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid vendor user. Please complete the registration process first.',
                    'errors' => ['user' => ['Vendor user not found or invalid']],
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'license_start_date' => 'required|date|after_or_equal:today',
                'license_expiry_date' => 'required|date|after:license_start_date',
                'notes' => 'nullable|string|max:500',
            ], [
                'license_file.required' => 'Please upload your business license document.',
                'license_file.file' => 'The license document must be a valid file.',
                'license_file.mimes' => 'The license document must be a PDF file.',
                'license_file.max' => 'The license document must not exceed 10MB in size.',
                'license_start_date.required' => 'Please provide the license start date.',
                'license_start_date.date' => 'Please provide a valid license start date.',
                'license_start_date.after_or_equal' => 'The license start date cannot be in the past.',
                'license_expiry_date.required' => 'Please provide the license expiration date.',
                'license_expiry_date.date' => 'Please provide a valid license expiration date.',
                'license_expiry_date.after' => 'The license expiration date must be after the start date.',
                'notes.string' => 'Notes must be text.',
                'notes.max' => 'Notes cannot exceed 500 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the following errors and try again.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->completeVendorLicense(
                $userId,
                $request->file('license_file'),
                $request->only(['license_start_date', 'license_expiry_date', 'notes'])
            );

            // Clear the session data after successful license upload
            if (session()->has('vendor_license_upload')) {
                session()->forget('vendor_license_upload');
            }

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('License upload error: ' . $e->getMessage(), [
                'user_id' => $userId ?? null,
                'request_data' => $request->except(['license_file']),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'License upload failed due to a server error. Please try again or contact support if the problem persists.',
                'error_code' => 'LICENSE_UPLOAD_ERROR'
            ], 500);
        }
    }

    /**
     * Send OTP for phone verification (session-based).
     */
    public function sendOtp()
    {
        try {
            $result = $this->registrationService->sendPhoneVerificationOTPSession();
            return response()->json($result);
        } catch (Exception $e) {
            Log::error('OTP send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
            ], 500);
        }
    }

    /**
     * Verify OTP code (session-based).
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->verifyPhoneOTPSession($request->otp_code);

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('OTP verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP',
            ], 500);
        }
    }

    /**
     * Get registration status (session-based).
     */
    public function getRegistrationStatus()
    {
        try {
            $vendorData = session('vendor_registration');

            if (!$vendorData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No registration session found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'session_data' => [
                    'step' => $vendorData['step'],
                    'email_verified' => $vendorData['email_verified'],
                    'phone_verified' => $vendorData['phone_verified'],
                    'personal_info' => [
                        'name' => $vendorData['personal_info']['name'],
                        'email' => $vendorData['personal_info']['email'],
                        'phone' => $vendorData['personal_info']['phone'],
                    ],
                    'created_at' => $vendorData['created_at'],
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Get registration status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get registration status',
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
            Log::error('API phone verification OTP send error: ' . $e->getMessage());
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
            Log::error('API phone verification error: ' . $e->getMessage());
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
            Log::error('API phone verification OTP resend error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend phone verification OTP',
            ], 500);
        }
    }

    /**
     * Resend email verification (session-based).
     */
    public function resendEmailVerification()
    {
        try {
            $result = $this->registrationService->resendEmailVerificationSession();

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Resend email verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email verification',
            ], 500);
        }
    }
}
