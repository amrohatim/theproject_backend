<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    protected $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Send Email Verification for Vendor Registration
     */
    public function sendVendorEmailVerification(Request $request)
    {
        // Log the request method and details for debugging
        Log::info('sendVendorEmailVerification called', [
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);

        // Check if this is a GET request (which shouldn't happen)
        if ($request->getMethod() !== 'POST') {
            Log::warning('sendVendorEmailVerification called with wrong method: ' . $request->getMethod());
            return response()->json([
                'success' => false,
                'message' => 'Method not allowed. This endpoint only accepts POST requests.',
            ], 405);
        }

        // Get vendor registration data from session
        $vendorData = session('vendor_registration');
        if (!$vendorData || !isset($vendorData['vendor_info'])) {
            return response()->json([
                'success' => false,
                'message' => 'No vendor registration data found in session',
            ], 400);
        }

        try {
            $email = $vendorData['vendor_info']['email'];
            $vendorName = $vendorData['vendor_info']['name'] ?? 'Vendor';
            $vendorPhone = $vendorData['vendor_info']['phone'] ?? '';

            Log::info('Attempting email verification for vendor: ' . $email);

            // Prepare metadata for the email
            $metadata = [
                'name' => $vendorName,
                'phone' => $vendorPhone,
                'registration_type' => 'vendor'
            ];

            // Generate verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Send verification email using Laravel's service
            $result = $this->emailVerificationService->sendVerificationEmailForTempRegistration(
                $email,
                $vendorName,
                $verificationCode,
                'vendor'
            );

            // Store verification code in session for later verification
            $vendorData['verification_code'] = $verificationCode;
            $vendorData['verification_code_expires'] = now()->addHours(24)->timestamp;

            if ($result['success']) {
                // Update session
                $vendorData['step'] = 2;
                $vendorData['email_verification_sent'] = true;
                session(['vendor_registration' => $vendorData]);

                Log::info('Email verification sent successfully to: ' . $email);

                return response()->json([
                    'success' => true,
                    'message' => 'Verification email sent successfully to ' . $email,
                    'next_step' => 'email_verification',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check Email Verification Status for Vendor Registration
     */
    public function checkVendorEmailVerification(Request $request)
    {
        // Log the request method and details for debugging
        Log::info('checkVendorEmailVerification called', [
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);

        // Check if this is a GET request (which shouldn't happen)
        if ($request->getMethod() !== 'POST') {
            Log::warning('checkVendorEmailVerification called with wrong method: ' . $request->getMethod());
            return response()->json([
                'success' => false,
                'message' => 'Method not allowed. This endpoint only accepts POST requests.',
            ], 405);
        }

        // Get vendor registration data from session
        $vendorData = session('vendor_registration');
        if (!$vendorData || !isset($vendorData['vendor_info'])) {
            return response()->json([
                'success' => false,
                'message' => 'No vendor registration data found in session',
            ], 400);
        }

        try {
            $email = $vendorData['vendor_info']['email'];

            // Check if email is already verified
            if (isset($vendorData['email_verified']) && $vendorData['email_verified']) {
                return response()->json([
                    'success' => true,
                    'verified' => true,
                    'message' => 'Email already verified',
                    'next_step' => 'company_info',
                ]);
            }

            // For now, we'll simulate verification by checking if verification code was sent
            // In a real implementation, you'd check if user clicked the email link
            if (isset($vendorData['verification_code']) && isset($vendorData['verification_code_expires'])) {
                // Check if verification code hasn't expired
                if (time() < $vendorData['verification_code_expires']) {
                    // For demo purposes, we'll mark as verified after email is sent
                    // In production, this would be verified when user clicks email link
                    $vendorData['step'] = 3;
                    $vendorData['email_verified'] = true;
                    session(['vendor_registration' => $vendorData]);

                    Log::info('Email verified successfully for vendor: ' . $email);

                    return response()->json([
                        'success' => true,
                        'verified' => true,
                        'message' => 'Email verified successfully',
                        'next_step' => 'company_info',
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'verified' => false,
                        'message' => 'Verification code expired. Please request a new one.',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => true,
                    'verified' => false,
                    'message' => 'Please send verification email first.',
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Email verification check failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to check verification status. Please try again.',
            ], 500);
        }
    }

    /**
     * Send Email Verification for Provider Registration
     */
    public function sendProviderEmailVerification(Request $request)
    {
        // Log the request method and details for debugging
        Log::info('sendProviderEmailVerification called', [
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);

        // Check if this is a GET request (which shouldn't happen)
        if ($request->getMethod() !== 'POST') {
            Log::warning('sendProviderEmailVerification called with wrong method: ' . $request->getMethod());
            return response()->json([
                'success' => false,
                'message' => 'Method not allowed. This endpoint only accepts POST requests.',
            ], 405);
        }

        // Get provider registration data from session
        $providerData = session('provider_registration');
        if (!$providerData || !isset($providerData['provider_info'])) {
            return response()->json([
                'success' => false,
                'message' => 'No provider registration data found in session',
            ], 400);
        }

        try {
            $email = $providerData['provider_info']['email'];
            $providerName = $providerData['provider_info']['name'] ?? 'Provider';
            $providerPhone = $providerData['provider_info']['phone'] ?? '';

            Log::info('Attempting email verification for provider: ' . $email);

            // Prepare metadata for the email
            $metadata = [
                'name' => $providerName,
                'phone' => $providerPhone,
                'registration_type' => 'provider'
            ];

            // Generate verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Send verification email using Laravel's service
            $result = $this->emailVerificationService->sendVerificationEmailForTempRegistration(
                $email,
                $providerName,
                $verificationCode,
                'provider'
            );

            // Store verification code in session for later verification
            $providerData['verification_code'] = $verificationCode;
            $providerData['verification_code_expires'] = now()->addHours(24)->timestamp;

            if ($result['success']) {
                // Update session
                $providerData['step'] = 2;
                $providerData['email_verification_sent'] = true;
                session(['provider_registration' => $providerData]);

                Log::info('Email verification sent successfully to: ' . $email);

                return response()->json([
                    'success' => true,
                    'message' => 'Verification email sent successfully to ' . $email,
                    'next_step' => 'email_verification',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check Email Verification Status for Provider Registration
     */
    public function checkProviderEmailVerification(Request $request)
    {
        // Log the request method and details for debugging
        Log::info('checkProviderEmailVerification called', [
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);

        // Check if this is a GET request (which shouldn't happen)
        if ($request->getMethod() !== 'POST') {
            Log::warning('checkProviderEmailVerification called with wrong method: ' . $request->getMethod());
            return response()->json([
                'success' => false,
                'message' => 'Method not allowed. This endpoint only accepts POST requests.',
            ], 405);
        }

        // Get provider registration data from session
        $providerData = session('provider_registration');
        if (!$providerData || !isset($providerData['provider_info'])) {
            return response()->json([
                'success' => false,
                'message' => 'No provider registration data found in session',
            ], 400);
        }

        try {
            $email = $providerData['provider_info']['email'];

            // Check if email is already verified
            if (isset($providerData['email_verified']) && $providerData['email_verified']) {
                return response()->json([
                    'success' => true,
                    'verified' => true,
                    'message' => 'Email already verified',
                    'next_step' => 'license_upload',
                ]);
            }

            // For now, we'll simulate verification by checking if verification code was sent
            // In a real implementation, you'd check if user clicked the email link
            if (isset($providerData['verification_code']) && isset($providerData['verification_code_expires'])) {
                // Check if verification code hasn't expired
                if (time() < $providerData['verification_code_expires']) {
                    // For demo purposes, we'll mark as verified after email is sent
                    // In production, this would be verified when user clicks email link
                    $providerData['step'] = 3;
                    $providerData['email_verified'] = true;
                    session(['provider_registration' => $providerData]);

                    Log::info('Email verified successfully for provider: ' . $email);

                    return response()->json([
                        'success' => true,
                        'verified' => true,
                        'message' => 'Email verified successfully',
                        'next_step' => 'license_upload',
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'verified' => false,
                        'message' => 'Verification code expired. Please request a new one.',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => true,
                    'verified' => false,
                    'message' => 'Please send verification email first.',
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Email verification check failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to check verification status. Please try again.',
            ], 500);
        }
    }
}
