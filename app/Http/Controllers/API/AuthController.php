<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Providers\FirebaseServiceProvider;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|in:vendor,customer,provider',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Default role is customer if not specified
        $role = $request->role ?? 'customer';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $role,
            'status' => 'active',
        ]);

        // Create token - safely handle if table doesn't exist
        try {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            // Log the error and return a fallback response
            Log::error('Error creating token during registration: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully (token creation failed - API authentication is not available)',
                'user' => $user,
                'token' => 'authentication-not-available',
                'error_details' => 'The personal_access_tokens table is missing. Please run migrations.'
            ], 201);
        }
    }

    /**
     * Login user and create token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required_without:email|string',
            'email' => 'required_without:identifier|string|email',
            'password' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = $request->input('identifier') ?? $request->input('email');

        if (!$identifier && $request->filled('phone')) {
            $identifier = $request->input('phone');
        }

        if (!$identifier) {
            return response()->json([
                'success' => false,
                'message' => 'Email or phone number is required.',
                'error_code' => 'missing_identifier'
            ], 422);
        }

        $loginField = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials = [
            $loginField => $identifier,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials. Please check your email/phone and password.',
                'error_code' => 'invalid_credentials'
            ], 401);
        }

        $user = User::where($loginField, $identifier)->firstOrFail();

        // Check if user is active
        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not active. Please contact support.',
            ], 403);
        }

        // Revoke all existing tokens - safely handle if table doesn't exist
        try {
            $user->tokens()->delete();
        } catch (\Exception $e) {
            // Log the error but continue - this is likely due to missing personal_access_tokens table
            Log::error('Error deleting tokens: ' . $e->getMessage());
        }

        // Create token - safely handle if table doesn't exist
        try {
            $token = $user->createToken('auth_token')->plainTextToken;
        } catch (\Exception $e) {
            // Log the error and return a fallback response
            Log::error('Error creating token: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Login successful (token creation failed - API authentication is not available)',
                'user' => $user,
                'token' => 'authentication-not-available',
                'error_details' => 'The personal_access_tokens table is missing. Please run migrations.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Social login using Firebase ID token (Google).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|in:google',
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $auth = FirebaseServiceProvider::getAuth();
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase service not available. Please try again later.',
            ], 500);
        }

        try {
            $verifiedToken = $auth->verifyIdToken($request->input('id_token'));
            $claims = $verifiedToken->claims();
            $email = $claims->get('email');
            $emailVerified = $claims->get('email_verified');

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not available from Google token.',
                ], 400);
            }

            if (!$emailVerified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google email is not verified.',
                ], 403);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You dont have account please create account first',
                ], 404);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is not active. Please contact support.',
                ], 403);
            }

            try {
                $user->tokens()->delete();
            } catch (\Exception $e) {
                Log::error('Error deleting tokens: ' . $e->getMessage());
            }

            try {
                $token = $user->createToken('auth_token')->plainTextToken;
            } catch (\Exception $e) {
                Log::error('Error creating token: ' . $e->getMessage());

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful (token creation failed - API authentication is not available)',
                    'user' => $user,
                    'token' => 'authentication-not-available',
                    'error_details' => 'The personal_access_tokens table is missing. Please run migrations.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Social login failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Invalid Google token. Please try again.',
            ], 401);
        }
    }

    /**
     * Logout user (revoke the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            // Log the error and return a fallback response
            Log::error('Error during logout: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Logged out (token deletion failed)',
                'error_details' => 'The personal_access_tokens table is missing. Please run migrations.'
            ]);
        }
    }

    /**
     * Get the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }
}
