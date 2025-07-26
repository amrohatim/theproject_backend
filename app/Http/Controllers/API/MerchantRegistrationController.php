<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\RegistrationService;
use App\Services\SMSalaService;
use App\Models\User;
use App\Models\Merchant;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\MerchantRegistrationValidationRequest;
use Exception;

class MerchantRegistrationController extends Controller
{
    private RegistrationService $registrationService;
    private SMSalaService $smsalaService;

    public function __construct(RegistrationService $registrationService, SMSalaService $smsalaService)
    {
        $this->registrationService = $registrationService;
        $this->smsalaService = $smsalaService;
    }

    /**
     * Step 1: Register merchant info and UAE ID.
     */
    public function registerMerchantInfo(MerchantRegistrationValidationRequest $request)
    {
        try {
            // Validation is handled by the MerchantRegistrationValidationRequest
            $validatedData = $request->validated();

            // Check for special scenarios
            $showLoginDialog = $request->input('show_login_dialog', false);
            $skipVerification = $request->input('skip_verification', false);

            // If validation passed but we need to show login dialog, return appropriate response
            if ($showLoginDialog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account already exists',
                    'show_login_dialog' => true,
                    'errors' => $request->errors(),
                ], 422);
            }

            // If we should skip verification, add this info to the result
            if ($skipVerification) {
                $validatedData['skip_verification'] = true;
            }

            $result = $this->registrationService->startMerchantRegistration($validatedData);

            if ($result['success']) {
                // Add skip verification flag to response if applicable
                if ($skipVerification) {
                    $result['skip_verification'] = true;
                }
                return response()->json($result, 201);
            } else {
                return response()->json($result, 400);
            }
        } catch (Exception $e) {
            Log::error('Merchant registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Step 2: Verify email and create user account.
     */
    public function verifyEmail(Request $request)
    {
        try {
            Log::info('Merchant email verification attempt', [
                'registration_token' => $request->registration_token,
                'verification_code' => $request->verification_code,
                'ip' => $request->ip(),
            ]);

            $validator = Validator::make($request->all(), [
                'registration_token' => 'required|string',
                'verification_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                Log::warning('Merchant email verification validation failed', [
                    'errors' => $validator->errors(),
                    'input' => $request->all(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->verifyEmailOnly(
                $request->registration_token,
                $request->verification_code
            );

            Log::info('Merchant email verification result', [
                'success' => $result['success'],
                'message' => $result['message'],
                'registration_token' => $request->registration_token,
            ]);

            if ($result['success']) {
                return response()->json($result, 200);
            } else {
                return response()->json($result, 400);
            }
        } catch (Exception $e) {
            Log::error('Merchant email verification error: ' . $e->getMessage(), [
                'registration_token' => $request->registration_token,
                'verification_code' => $request->verification_code,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Email verification failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Step 3: Upload license.
     */
    public function uploadLicense(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'license_start_date' => 'required|date|after_or_equal:today',
                'license_end_date' => 'required|date|after:license_start_date',
            ], [
                'license_start_date.required' => 'License start date is required.',
                'license_start_date.date' => 'License start date must be a valid date.',
                'license_start_date.after_or_equal' => 'License start date cannot be in the past.',
                'license_end_date.required' => 'License end date is required.',
                'license_end_date.date' => 'License end date must be a valid date.',
                'license_end_date.after' => 'License end date must be after the start date.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::findOrFail($request->user_id);

            // Verify user is a merchant
            if ($user->role !== 'merchant') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a merchant',
                ], 400);
            }

            $result = $this->registrationService->uploadMerchantLicense($user, $request->all());

            if ($result['success']) {
                return response()->json($result, 200);
            } else {
                return response()->json($result, 400);
            }
        } catch (Exception $e) {
            Log::error('Merchant license upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'License upload failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Send OTP for phone verification using SMSala.
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

            $result = $this->smsalaService->sendOTP(
                $request->phone_number,
                $request->type ?? 'registration'
            );

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Merchant SMSala OTP send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
            ], 500);
        }
    }

    /**
     * Verify OTP code using SMSala.
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'request_id' => 'required|string',
                'otp_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->smsalaService->verifyOTP(
                $request->request_id,
                $request->otp_code
            );

            // If OTP is verified, update user's phone verification status
            if ($result['success']) {
                $user = User::where('phone', $result['phone'])->first();
                if ($user) {
                    $user->update([
                        'phone_verified' => true,
                        'phone_verified_at' => now(),
                    ]);
                }
            }

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Merchant SMSala OTP verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP',
            ], 500);
        }
    }

    /**
     * Resend OTP for phone verification using SMSala.
     */
    public function resendOtp(Request $request)
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

            $result = $this->smsalaService->sendOTP(
                $request->phone_number,
                $request->type ?? 'registration'
            );

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Merchant SMSala OTP resend error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP',
            ], 500);
        }
    }

    /**
     * Send OTP for phone verification using registration token.
     */
    public function sendPhoneOtp(Request $request)
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
            Log::error('Merchant phone OTP send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send phone OTP',
            ], 500);
        }
    }

    /**
     * Verify phone OTP using registration token.
     */
    public function verifyPhoneOtp(Request $request)
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
            Log::error('Merchant phone OTP verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify phone OTP',
            ], 500);
        }
    }

    /**
     * Resend phone OTP using registration token.
     */
    public function resendPhoneOtp(Request $request)
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
            Log::error('Merchant phone OTP resend error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend phone OTP',
            ], 500);
        }
    }

    /**
     * Get registration status.
     */
    public function getRegistrationStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::with(['merchant', 'licenses'])->findOrFail($request->user_id);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'registration_step' => $user->registration_step,
                    'phone_verified' => $user->phone_verified,
                    'email_verified' => !is_null($user->email_verified_at),
                    'has_merchant_profile' => !is_null($user->merchant),
                    'has_license' => $user->licenses->count() > 0,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Get merchant registration status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get registration status',
            ], 500);
        }
    }

    /**
     * Resend email verification.
     */
    public function resendEmailVerification(Request $request)
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

            $result = $this->registrationService->resendEmailVerification($request->registration_token);

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Merchant resend email verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend email verification',
            ], 500);
        }
    }
}
