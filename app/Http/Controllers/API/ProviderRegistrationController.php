<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\RegistrationService;
use App\Services\SMSalaService;
use App\Services\TemporaryRegistrationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class ProviderRegistrationController extends Controller
{
    private RegistrationService $registrationService;
    private SMSalaService $smsalaService;

    public function __construct(RegistrationService $registrationService, SMSalaService $smsalaService)
    {
        $this->registrationService = $registrationService;
        $this->smsalaService = $smsalaService;
    }

    /**
     * Step 1: Register provider information.
     */
    public function registerProviderInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => 'required|string|max:20|unique:users,phone',
                'password' => 'required|string|min:8|confirmed',
                'business_name' => 'required|string|max:255',
                'business_type' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'delivery_capability' => 'boolean',
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
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->startProviderRegistration($request->all());

            return response()->json($result, 201);
        } catch (Exception $e) {
            Log::error('Provider registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Step 2: Verify email only (for phone verification flow).
     */
    public function verifyEmail(Request $request)
    {
        try {
            Log::info('Provider email verification attempt', [
                'registration_token' => $request->registration_token,
                'verification_code' => $request->verification_code,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $validator = Validator::make($request->all(), [
                'registration_token' => 'required|string',
                'verification_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                Log::warning('Provider email verification validation failed', [
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

            if ($result['success']) {
                Log::info('Provider email verification successful', [
                    'registration_token' => $request->registration_token,
                ]);
                return response()->json($result, 200);
            } else {
                Log::warning('Provider email verification failed', [
                    'registration_token' => $request->registration_token,
                    'verification_code' => $request->verification_code,
                    'message' => $result['message'],
                ]);
                return response()->json($result, 400);
            }
        } catch (Exception $e) {
            Log::error('Provider email verification error: ' . $e->getMessage(), [
                'registration_token' => $request->registration_token,
                'verification_code' => $request->verification_code,
                'exception' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify email',
            ], 500);
        }
    }

    /**
     * Step 3: Upload provider license.
     */
    public function uploadLicense(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'duration_days' => 'nullable|integer|min:1|max:3650',
                'notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->registrationService->completeProviderLicense(
                $request->user_id,
                $request->file('license_file'),
                $request->only(['duration_days', 'notes'])
            );

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Provider license upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
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
            Log::error('Provider SMSala OTP send error: ' . $e->getMessage());
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
            Log::error('Provider SMSala OTP verification error: ' . $e->getMessage());
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
            Log::error('Provider SMSala OTP resend error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP',
            ], 500);
        }
    }

    /**
     * Send phone verification OTP using registration token.
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
            Log::error('Provider phone OTP send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send phone verification OTP',
            ], 500);
        }
    }

    /**
     * Verify phone OTP and create user using registration token.
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
            Log::error('Provider phone OTP verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify phone OTP',
            ], 500);
        }
    }

    /**
     * Resend phone verification OTP using registration token.
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
            Log::error('Provider phone OTP resend error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend phone verification OTP',
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

            $user = User::with(['provider', 'licenses', 'vendorLocations'])->findOrFail($request->user_id);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'phone_verified' => $user->phone_verified,
                    'email_verified_at' => $user->email_verified_at,
                    'registration_step' => $user->registration_step,
                    'status' => $user->status,
                ],
                'provider' => $user->provider,
                'licenses' => $user->licenses,
                'vendor_locations' => $user->vendorLocations,
            ]);
        } catch (Exception $e) {
            Log::error('Get provider registration status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get registration status',
            ], 500);
        }
    }

    /**
     * Resend email verification for temporary registration.
     */
    public function resendEmailVerification(Request $request)
    {
        try {
            // Check if this is a temporary registration (has registration_token) or existing user (has user_id)
            if ($request->has('registration_token')) {
                // Handle temporary registration resend
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

                if ($result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => $result['message'],
                        'registration_token' => $request->registration_token,
                    ]);
                } else {
                    return response()->json($result, 400);
                }
            } else {
                // Handle existing user resend (legacy support)
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

                $user = User::findOrFail($request->user_id);

                if ($user->hasVerifiedEmail()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email is already verified',
                    ], 400);
                }

                $user->sendEmailVerificationNotification();

                return response()->json([
                    'success' => true,
                    'message' => 'Email verification sent successfully',
                ]);
            }
        } catch (Exception $e) {
            Log::error('Resend provider email verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email verification',
            ], 500);
        }
    }

    /**
     * Add or update vendor location.
     */
    public function addVendorLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'emirate' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'is_primary' => 'boolean',
                'can_deliver_to_vendors' => 'boolean',
                'delivery_fees' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $location = \App\Models\VendorLocation::create($request->all());

            if ($request->is_primary) {
                $location->setAsPrimary();
            }

            return response()->json([
                'success' => true,
                'message' => 'Vendor location added successfully',
                'location' => $location,
            ]);
        } catch (Exception $e) {
            Log::error('Add vendor location error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add vendor location',
            ], 500);
        }
    }
}
