<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\RegistrationService;
use App\Services\OtpService;
use App\Models\User;
use App\Models\Merchant;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class MerchantRegistrationController extends Controller
{
    private RegistrationService $registrationService;
    private OtpService $otpService;

    public function __construct(RegistrationService $registrationService, OtpService $otpService)
    {
        $this->registrationService = $registrationService;
        $this->otpService = $otpService;
    }

    /**
     * Step 1: Register merchant info and UAE ID.
     */
    public function registerMerchantInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|unique:users,email',
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
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->startMerchantRegistration($request->all());

            if ($result['success']) {
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

            $result = $this->registrationService->verifyEmailAndCreateUser(
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
                'license_type' => 'nullable|string|max:100',
                'duration_days' => 'nullable|integer|min:1',
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
            Log::error('Merchant OTP send error: ' . $e->getMessage());
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
                        'registration_step' => 'license_completed',
                    ]);
                }
            }

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Merchant OTP verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed. Please try again.',
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
