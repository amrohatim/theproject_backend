<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\RegistrationService;
use App\Services\OtpService;
use App\Services\TemporaryRegistrationService;
use App\Models\User;
use App\Models\Company;
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
            // Custom validation logic based on the validation guide
            $name = $request->input('name');
            $email = $request->input('email');
            $phone = $request->input('phone');

            // Check if full name is already taken
            $existingUserByName = User::where('name', $name)->first();
            if ($existingUserByName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Full name is already taken.',
                    'errors' => ['name' => ['Full name is already taken.']]
                ], 422);
            }

            // Check email validation rules from the guide
            $existingUserByEmail = User::where('email', $email)->first();
            if ($existingUserByEmail) {
                if ($existingUserByEmail->registration_step === 'verified') {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have a registered company with this email you cannot create two accounts with the same email, please log in',
                        'errors' => ['email' => ['You have a registered company with this email you cannot create two accounts with the same email, please log in']],
                        'show_login' => true
                    ], 422);
                } elseif ($existingUserByEmail->registration_step === 'license_completed') {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have a submit company information wait for admin approval you will receive an email or a call from our support team, Thank you for your patience.',
                        'errors' => ['email' => ['You have a submit company information wait for admin approval you will receive an email or a call from our support team, Thank you for your patience.']],
                        'show_login' => true
                    ], 422);
                }
            }

            // Check phone validation rules from the guide
            $existingUserByPhone = User::where('phone', $phone)->first();
            if ($existingUserByPhone && $existingUserByPhone->registration_step === 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'You have a registered company with this phone you cannot create two accounts with the same phone',
                    'errors' => ['phone' => ['You have a registered company with this phone you cannot create two accounts with the same phone']],
                    'show_login' => true
                ], 422);
            }

            // Check if phone and email exist and are verified but registration step is not 'verified' or 'license_completed'
            if ($existingUserByPhone && $existingUserByEmail &&
                $existingUserByPhone->id === $existingUserByEmail->id &&
                $existingUserByPhone->phone_verified_at &&
                $existingUserByEmail->email_verified_at &&
                !in_array($existingUserByPhone->registration_step, ['verified', 'license_completed'])) {

                // Skip email and phone verification, move to license upload step
                session([
                    'vendor_registration' => [
                        'step' => 4, // Skip to license upload
                        'personal_info' => $request->all(),
                        'email_verified' => true,
                        'phone_verified' => true,
                        'skip_verification' => true,
                        'existing_user_id' => $existingUserByPhone->id,
                        'created_at' => now()->timestamp,
                    ]
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Account found. Proceeding to license upload.',
                    'skip_to_step' => 4
                ]);
            }

            // Standard validation for new registrations
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
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
            // Check for business name uniqueness first
            $existingCompany = \App\Models\Company::where('name', $request->input('name'))->first();
            if ($existingCompany) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business name already exists. Please choose a different name.',
                    'errors' => ['name' => ['Business name already exists. Please choose a different name.']]
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:companies,email',
                'contact_number_1' => 'required|string|max:20|unique:companies,contact_number_1',
                'contact_number_2' => 'nullable|string|max:20|unique:companies,contact_number_2',
                'address' => 'required|string|max:500',
                'emirate' => 'required|string|max:100',
                'city' => 'required|string|max:100', // Made required instead of nullable
                'street' => 'nullable|string|max:255',
                'delivery_capability' => 'boolean',
                'delivery_areas' => 'nullable|array',
                'description' => 'nullable|string|max:1000',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Changed back to 'logo'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->completeVendorRegistrationSession($request->all());



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
