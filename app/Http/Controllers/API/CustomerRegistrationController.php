<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Avatar;
use App\Mail\TempEmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\SMSalaService;

class CustomerRegistrationController extends Controller
{
    protected $smsalaService;

    public function __construct(SMSalaService $smsalaService)
    {
        $this->smsalaService = $smsalaService;
    }

    /**
     * Send email verification code
     */
    public function sendEmailVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store verification code in cache for 10 minutes
        Cache::put("email_verification_{$email}", $code, 600);
        
        try {
            // Send email using Mailgun via TempEmailVerification
            Mail::to($email)->send(new TempEmailVerification(
                $email,
                'Customer', // Default name for customer registration
                $code,
                'customer'
            ));

            Log::info("Email verification code sent to {$email}: {$code}", [
                'email' => $email,
                'code' => $code,
                'user_type' => 'customer',
                'expires_at' => now()->addMinutes(10),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email',
                'debug_code' => config('app.debug') ? $code : null, // Only in debug mode
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send email verification to {$email}: " . $e->getMessage(), [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify email code
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $code = $request->code;
        $storedCode = Cache::get("email_verification_{$email}");

        if (!$storedCode || $storedCode !== $code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code',
            ], 400);
        }

        // Mark email as verified in cache
        Cache::put("email_verified_{$email}", true, 3600); // 1 hour
        Cache::forget("email_verification_{$email}");

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully',
        ]);
    }

    /**
     * Send phone verification code using SMSala service
     */
    public function sendPhoneVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|regex:/^\+971[0-9]{9}$/',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->smsalaService->sendOTP(
                $request->phone,
                'registration'
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Customer SMSala OTP send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification SMS',
            ], 500);
        }
    }

    /**
     * Verify phone code using SMSala service
     */
    public function verifyPhone(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'request_id' => 'required|string',
                'code' => 'required|string|size:6',
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
                $request->code
            );

            // If OTP is verified, mark phone as verified in cache
            if ($result['success']) {
                $phone = $result['phone'] ?? null;
                if ($phone) {
                    // Ensure phone number has +971 prefix for cache key consistency
                    $formattedPhone = $phone;
                    if (!str_starts_with($phone, '+')) {
                        $formattedPhone = '+' . $phone;
                    }
                    Cache::put("phone_verified_{$formattedPhone}", true, 3600); // 1 hour

                    // Also store with the original format for backward compatibility
                    Cache::put("phone_verified_{$phone}", true, 3600); // 1 hour
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Customer SMSala OTP verify error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify phone code',
            ], 500);
        }
    }

    /**
     * Check username availability
     */
    public function checkUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $username = $request->username;
        $exists = User::where('name', $username)->exists();

        return response()->json([
            'success' => true,
            'available' => !$exists,
            'message' => $exists ? 'Username is already taken' : 'Username is available',
        ]);
    }

    /**
     * Get available avatars
     */
    public function getAvatars()
    {
        try {
            $avatars = Avatar::select('id', 'name', 'avatar_image')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($avatar) {
                    return [
                        'id' => $avatar->id,
                        'name' => $avatar->name,
                        'url' => $avatar->avatar_image ? asset('storage/' . $avatar->avatar_image) : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'avatars' => $avatars,
            ]);
        } catch (\Exception $e) {
            Log::error('Avatar fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch avatars',
            ], 500);
        }
    }

    /**
     * Complete customer registration
     */
    public function completeRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|regex:/^\+971[0-9]{9}$/|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|min:3|max:255|unique:users,name',
            'avatar_id' => 'required|exists:avatars,id',
            'social_provider' => 'nullable|string|in:google,facebook', // Allow social provider info
            'social_id' => 'nullable|string', // Social provider user ID
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check verification status based on registration method
        $socialProvider = $request->social_provider;
        $phoneVerified = Cache::get("phone_verified_{$request->phone}");

        // For social login users (Google/Facebook), skip email verification check
        // as their emails are already verified by the OAuth provider
        if (!$socialProvider) {
            // Regular registration - check email verification
            $emailVerified = Cache::get("email_verified_{$request->email}");
            if (!$emailVerified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not verified',
                ], 400);
            }
        } else {
            // Social login - log that we're skipping email verification
            Log::info("Skipping email verification for {$socialProvider} user: {$request->email}");
        }

        if (!$phoneVerified) {
            return response()->json([
                'success' => false,
                'message' => 'Phone not verified',
            ], 400);
        }

        try {
            // Get avatar URL
            $avatar = Avatar::find($request->avatar_id);

            // Prepare registration data for social login users
            $registrationData = null;
            if ($socialProvider) {
                $registrationData = [
                    'social_provider' => $socialProvider,
                    'social_id' => $request->social_id,
                    'registration_method' => 'social_login',
                    'email_verified_by' => $socialProvider, // Track that email was verified by OAuth provider
                ];
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'profile_image' => $avatar ? $avatar->avatar_image : null,
                'email_verified_at' => now(),
                'phone_verified' => true,
                'phone_verified_at' => now(),
                'registration_step' => 'verified',
                'status' => 'active',
                'registration_data' => $registrationData,
            ]);

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Clean up verification cache
            Cache::forget("email_verified_{$request->email}");
            Cache::forget("phone_verified_{$request->phone}");

            return response()->json([
                'success' => true,
                'message' => 'Registration completed successfully',
                'user' => $user,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }
}
