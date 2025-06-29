<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\EmailExists;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Exception\FirebaseException;
use App\Providers\FirebaseServiceProvider;

class FirebaseService
{
    protected $auth;

    public function __construct()
    {
        $this->auth = FirebaseServiceProvider::getAuth();
    }

    /**
     * Check if Firebase is available and configured
     */
    public function isAvailable(): bool
    {
        return $this->auth !== null && FirebaseServiceProvider::isConfigured();
    }

    /**
     * Create a Firebase user with email and password
     */
    public function createUser(string $email, string $password, array $additionalClaims = []): array
    {
        if (!$this->isAvailable()) {
            Log::warning('Firebase service not available for user creation', ['email' => $email]);
            return [
                'success' => false,
                'error' => 'Firebase service not available',
                'fallback' => true
            ];
        }

        try {
            $userProperties = [
                'email' => $email,
                'password' => $password,
                'emailVerified' => false,
            ];

            // Add additional claims if provided
            if (!empty($additionalClaims)) {
                $userProperties = array_merge($userProperties, $additionalClaims);
            }

            $createdUser = $this->auth->createUser($userProperties);

            Log::info('Firebase user created successfully', [
                'uid' => $createdUser->uid,
                'email' => $email
            ]);

            return [
                'success' => true,
                'uid' => $createdUser->uid,
                'user' => $createdUser
            ];

        } catch (EmailExists $e) {
            Log::warning('Firebase user creation failed - email exists', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Email already exists in Firebase',
                'code' => 'email_exists'
            ];

        } catch (InvalidPassword $e) {
            Log::error('Firebase user creation failed - invalid password', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Invalid password format',
                'code' => 'invalid_password'
            ];

        } catch (FirebaseException $e) {
            Log::error('Firebase user creation failed - Firebase exception', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => 'Firebase service error: ' . $e->getMessage(),
                'code' => 'firebase_error'
            ];

        } catch (\Exception $e) {
            Log::error('Firebase user creation failed - unexpected error', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => 'Unexpected error during user creation',
                'code' => 'unexpected_error'
            ];
        }
    }

    /**
     * Send email verification to a Firebase user
     */
    public function sendEmailVerification(string $uid): array
    {
        if (!$this->isAvailable()) {
            Log::warning('Firebase service not available for email verification', ['uid' => $uid]);
            return [
                'success' => false,
                'error' => 'Firebase service not available',
                'fallback' => true
            ];
        }

        try {
            $this->auth->sendEmailVerificationLink($uid);

            Log::info('Firebase email verification sent successfully', ['uid' => $uid]);

            return [
                'success' => true,
                'message' => 'Email verification sent successfully'
            ];

        } catch (UserNotFound $e) {
            Log::error('Firebase email verification failed - user not found', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'User not found in Firebase',
                'code' => 'user_not_found'
            ];

        } catch (FirebaseException $e) {
            Log::error('Firebase email verification failed - Firebase exception', [
                'uid' => $uid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => 'Firebase service error: ' . $e->getMessage(),
                'code' => 'firebase_error'
            ];

        } catch (\Exception $e) {
            Log::error('Firebase email verification failed - unexpected error', [
                'uid' => $uid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => 'Unexpected error during email verification',
                'code' => 'unexpected_error'
            ];
        }
    }

    /**
     * Get Firebase user by UID
     */
    public function getUser(string $uid): array
    {
        if (!$this->isAvailable()) {
            Log::warning('Firebase service not available for user retrieval', ['uid' => $uid]);
            return [
                'success' => false,
                'error' => 'Firebase service not available',
                'fallback' => true
            ];
        }

        try {
            $user = $this->auth->getUser($uid);

            return [
                'success' => true,
                'user' => $user
            ];

        } catch (UserNotFound $e) {
            Log::warning('Firebase user not found', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'User not found in Firebase',
                'code' => 'user_not_found'
            ];

        } catch (FirebaseException $e) {
            Log::error('Firebase user retrieval failed - Firebase exception', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Firebase service error: ' . $e->getMessage(),
                'code' => 'firebase_error'
            ];

        } catch (\Exception $e) {
            Log::error('Firebase user retrieval failed - unexpected error', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Unexpected error during user retrieval',
                'code' => 'unexpected_error'
            ];
        }
    }

    /**
     * Check if a Firebase user's email is verified
     */
    public function isEmailVerified(string $uid): array
    {
        $userResult = $this->getUser($uid);
        
        if (!$userResult['success']) {
            return $userResult;
        }

        return [
            'success' => true,
            'verified' => $userResult['user']->emailVerified
        ];
    }

    /**
     * Delete a Firebase user
     */
    public function deleteUser(string $uid): array
    {
        if (!$this->isAvailable()) {
            Log::warning('Firebase service not available for user deletion', ['uid' => $uid]);
            return [
                'success' => false,
                'error' => 'Firebase service not available',
                'fallback' => true
            ];
        }

        try {
            $this->auth->deleteUser($uid);

            Log::info('Firebase user deleted successfully', ['uid' => $uid]);

            return [
                'success' => true,
                'message' => 'User deleted successfully'
            ];

        } catch (UserNotFound $e) {
            Log::warning('Firebase user deletion failed - user not found', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'User not found in Firebase',
                'code' => 'user_not_found'
            ];

        } catch (FirebaseException $e) {
            Log::error('Firebase user deletion failed - Firebase exception', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Firebase service error: ' . $e->getMessage(),
                'code' => 'firebase_error'
            ];

        } catch (\Exception $e) {
            Log::error('Firebase user deletion failed - unexpected error', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Unexpected error during user deletion',
                'code' => 'unexpected_error'
            ];
        }
    }
}
