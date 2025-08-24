<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\EmailExists;

class FirebaseEmailController extends Controller
{
    protected $firebaseAuth;

    public function __construct()
    {
        $this->initializeFirebase();
    }

    /**
     * Initialize Firebase Auth service
     */
    private function initializeFirebase()
    {
        try {
            $serviceAccountPath = base_path('dala3chic-e2b81-firebase-adminsdk-fbsvc-e5c52a715e.json');
            
            Log::info('Initializing Firebase with service account: ' . $serviceAccountPath);
            
            if (!file_exists($serviceAccountPath)) {
                Log::error('Firebase service account file not found: ' . $serviceAccountPath);
                $this->firebaseAuth = null;
                return;
            }

            // Configure SSL verification for development
            if (env('FIREBASE_DISABLE_SSL_VERIFICATION', false)) {
                // Disable SSL verification by setting curl options globally
                ini_set('curl.cainfo', '');
                Log::info('Firebase SSL verification disabled for development');
            } else {
                // Use the CA certificate bundle if available
                $caCertPath = env('CURL_CA_BUNDLE');
                if ($caCertPath) {
                    $fullCertPath = base_path($caCertPath);
                    if (file_exists($fullCertPath)) {
                        ini_set('curl.cainfo', $fullCertPath);
                        Log::info('Firebase using CA certificate: ' . $fullCertPath);
                    } else {
                        Log::warning('CA certificate file not found: ' . $fullCertPath);
                    }
                }
            }

            // Initialize Firebase
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $this->firebaseAuth = $factory->createAuth();
            
            Log::info('Firebase Auth initialized successfully');
            
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            $this->firebaseAuth = null;
        }
    }

    /**
     * Send Firebase Email Verification for Vendor Registration
     */
    public function sendVendorEmailVerification(Request $request)
    {
        // Log the request method and details for debugging
        Log::info('sendVendorEmailVerification called', [
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
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

        $vendorData = session('vendor_registration');

        if (!$vendorData || $vendorData['step'] !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration session. Please complete basic information first.',
            ], 400);
        }

        try {
            $email = $vendorData['vendor_info']['email'];
            $password = $vendorData['vendor_info']['password'];

            Log::info('Attempting Firebase email verification for vendor: ' . $email);

            if (!$this->firebaseAuth) {
                Log::error('Firebase Auth not initialized');
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase service not available. Please try again later.',
                ], 500);
            }

            // Create user properties
            $userProperties = [
                'email' => $email,
                'password' => $password,
                'emailVerified' => false,
            ];

            try {
                // Try to create the user
                $createdUser = $this->firebaseAuth->createUser($userProperties);
                Log::info('Firebase user created with UID: ' . $createdUser->uid);
                $uid = $createdUser->uid;
            } catch (EmailExists $e) {
                // User already exists, get existing user
                Log::info('Firebase user already exists for: ' . $email);
                $existingUser = $this->firebaseAuth->getUserByEmail($email);
                $uid = $existingUser->uid;
            }

            // Send email verification
            $this->firebaseAuth->sendEmailVerificationLink($email);
            Log::info('Firebase verification email sent to: ' . $email);

            // Update session
            $vendorData['step'] = 2;
            $vendorData['firebase_uid'] = $uid;
            $vendorData['email_verification_sent'] = true;
            session(['vendor_registration' => $vendorData]);

            return response()->json([
                'success' => true,
                'message' => 'Verification email sent successfully to ' . $email,
                'next_step' => 'email_verification',
            ]);

        } catch (\Exception $e) {
            Log::error('Firebase email verification failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check Firebase Email Verification Status for Vendor Registration
     */
    public function checkVendorEmailVerification(Request $request)
    {
        $vendorData = session('vendor_registration');

        if (!$vendorData || !isset($vendorData['firebase_uid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration session. Please start over.',
            ], 400);
        }

        try {
            if (!$this->firebaseAuth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase not initialized',
                ], 500);
            }

            // Get user from Firebase
            $firebaseUser = $this->firebaseAuth->getUser($vendorData['firebase_uid']);

            if ($firebaseUser->emailVerified) {
                // Mark email as verified and proceed to next step
                $vendorData['step'] = 3;
                $vendorData['email_verified'] = true;
                session(['vendor_registration' => $vendorData]);

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
                    'message' => 'Email not yet verified. Please check your inbox.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Firebase email verification check failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to check verification status. Please try again.',
            ], 500);
        }
    }

    /**
     * Send Firebase Email Verification for Provider Registration
     */
    public function sendProviderEmailVerification(Request $request)
    {
        $providerData = session('provider_registration');
        
        if (!$providerData || $providerData['step'] !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration session. Please complete basic information first.',
            ], 400);
        }

        try {
            $email = $providerData['provider_info']['email'];
            $password = $providerData['provider_info']['password'];

            Log::info('Attempting Firebase email verification for provider: ' . $email);

            if (!$this->firebaseAuth) {
                Log::error('Firebase Auth not initialized');
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase service not available. Please try again later.',
                ], 500);
            }

            // Create user properties
            $userProperties = [
                'email' => $email,
                'password' => $password,
                'emailVerified' => false,
            ];

            try {
                // Try to create the user
                $createdUser = $this->firebaseAuth->createUser($userProperties);
                Log::info('Firebase user created with UID: ' . $createdUser->uid);
                $uid = $createdUser->uid;
            } catch (EmailExists $e) {
                // User already exists, get existing user
                Log::info('Firebase user already exists for: ' . $email);
                $existingUser = $this->firebaseAuth->getUserByEmail($email);
                $uid = $existingUser->uid;
            }

            // Send email verification
            $this->firebaseAuth->sendEmailVerificationLink($email);
            Log::info('Firebase verification email sent to: ' . $email);

            // Update session
            $providerData['step'] = 2;
            $providerData['firebase_uid'] = $uid;
            $providerData['email_verification_sent'] = true;
            session(['provider_registration' => $providerData]);

            return response()->json([
                'success' => true,
                'message' => 'Verification email sent successfully to ' . $email,
                'next_step' => 'email_verification',
            ]);

        } catch (\Exception $e) {
            Log::error('Firebase email verification failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check Firebase Email Verification Status for Provider Registration
     */
    public function checkProviderEmailVerification(Request $request)
    {
        $providerData = session('provider_registration');

        if (!$providerData || !isset($providerData['firebase_uid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration session. Please start over.',
            ], 400);
        }

        try {
            if (!$this->firebaseAuth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase not initialized',
                ], 500);
            }

            // Get user from Firebase
            $firebaseUser = $this->firebaseAuth->getUser($providerData['firebase_uid']);

            if ($firebaseUser->emailVerified) {
                // Mark email as verified and proceed to next step
                $providerData['step'] = 3;
                $providerData['email_verified'] = true;
                session(['provider_registration' => $providerData]);

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
                    'message' => 'Email not yet verified. Please check your inbox.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Firebase email verification check failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to check verification status. Please try again.',
            ], 500);
        }
    }
}
