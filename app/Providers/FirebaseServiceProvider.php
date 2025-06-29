<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Http\HttpClientOptions;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('firebase.auth', function ($app) {
            return $this->createFirebaseAuth();
        });

        $this->app->singleton('firebase.factory', function ($app) {
            return $this->createFirebaseFactory();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure SSL settings for Firebase in development
        $this->configureSSLSettings();
    }

    /**
     * Create Firebase Factory instance
     */
    private function createFirebaseFactory(): Factory
    {
        try {
            // Configure SSL settings before creating factory
            $this->configureSSLSettings();

            $factory = new Factory();

            // Configure HTTP client options for development
            if (app()->environment('local') && config('services.firebase.disable_ssl_verification', env('FIREBASE_DISABLE_SSL_VERIFICATION', false))) {
                $httpClientOptions = HttpClientOptions::default()
                    ->withTimeOut(60)
                    ->withConnectTimeout(30);
                $factory = $factory->withHttpClientOptions($httpClientOptions);
                Log::info('Firebase: Using HTTP client options with SSL verification disabled');
            }

            // Set project ID if available
            $projectId = config('services.firebase.project_id');
            if ($projectId) {
                $factory = $factory->withProjectId($projectId);
            }

            // Try to use service account file first, then environment variables
            $serviceAccountPath = base_path('dala3chic-e2b81-firebase-adminsdk-fbsvc-e5c52a715e.json');

            if (file_exists($serviceAccountPath)) {
                Log::info('Firebase: Using service account file', ['path' => $serviceAccountPath]);
                $factory = $factory->withServiceAccount($serviceAccountPath);
            } elseif ($this->hasEnvironmentCredentials()) {
                Log::info('Firebase: Using service account from environment variables');
                $serviceAccount = $this->buildServiceAccountFromEnvironment();
                $factory = $factory->withServiceAccount($serviceAccount);
            } else {
                Log::warning('Firebase: No service account credentials found');
                throw new \Exception('Firebase service account credentials not configured');
            }

            return $factory;
        } catch (\Exception $e) {
            Log::error('Firebase Factory creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Create Firebase Auth instance
     */
    private function createFirebaseAuth(): ?Auth
    {
        try {
            // Skip Firebase initialization in testing environment
            if (app()->environment('testing')) {
                Log::info('Firebase: Skipping initialization in testing environment');
                return null;
            }

            $factory = $this->createFirebaseFactory();
            $auth = $factory->createAuth();
            
            Log::info('Firebase Auth initialized successfully');
            return $auth;
        } catch (\Exception $e) {
            Log::error('Firebase Auth initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // In production, you might want to throw an exception
            // For now, we'll return null and handle it gracefully
            return null;
        }
    }

    /**
     * Check if environment credentials are available
     */
    private function hasEnvironmentCredentials(): bool
    {
        return !empty(config('services.firebase.private_key')) &&
               !empty(config('services.firebase.client_email')) &&
               !empty(config('services.firebase.project_id'));
    }

    /**
     * Build service account array from environment variables
     */
    private function buildServiceAccountFromEnvironment(): array
    {
        return [
            'type' => 'service_account',
            'project_id' => config('services.firebase.project_id'),
            'private_key_id' => config('services.firebase.private_key_id'),
            'private_key' => str_replace('\\n', "\n", config('services.firebase.private_key')),
            'client_email' => config('services.firebase.client_email'),
            'client_id' => config('services.firebase.client_id'),
            'auth_uri' => config('services.firebase.auth_uri'),
            'token_uri' => config('services.firebase.token_uri'),
            'auth_provider_x509_cert_url' => config('services.firebase.auth_provider_x509_cert_url'),
            'client_x509_cert_url' => config('services.firebase.client_x509_cert_url'),
        ];
    }

    /**
     * Configure SSL settings for Firebase SDK
     */
    private function configureSSLSettings(): void
    {
        // Only configure SSL in local environment
        if (!app()->environment('local')) {
            return;
        }

        $disableSSL = config('services.firebase.disable_ssl_verification', env('FIREBASE_DISABLE_SSL_VERIFICATION', false));

        if ($disableSSL) {
            Log::info('Firebase: SSL verification disabled for development environment');

            // Configure curl default options for development
            $curlDefaults = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 30,
            ];

            // Set curl defaults globally
            foreach ($curlDefaults as $option => $value) {
                curl_setopt_array(curl_init(), [$option => $value]);
            }

            // Configure stream context defaults
            $streamContext = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
                'http' => [
                    'timeout' => 60,
                    'ignore_errors' => true,
                ]
            ];

            stream_context_set_default($streamContext);

            // Set PHP ini settings
            ini_set('curl.cainfo', '');
            ini_set('openssl.cafile', '');

            // Set environment variable for Guzzle
            putenv('GUZZLE_CURL_OPTIONS=' . json_encode($curlDefaults));

        } else {
            // Use CA certificate bundle if available
            $caCertPath = env('CURL_CA_BUNDLE');
            if ($caCertPath) {
                $fullCertPath = base_path($caCertPath);
                if (file_exists($fullCertPath)) {
                    ini_set('curl.cainfo', $fullCertPath);
                    ini_set('openssl.cafile', $fullCertPath);
                    putenv('CURL_CA_BUNDLE=' . $fullCertPath);
                    putenv('SSL_CERT_FILE=' . $fullCertPath);
                    Log::info('Firebase: Using CA certificate bundle', ['path' => $fullCertPath]);
                } else {
                    Log::warning('Firebase: CA certificate file not found', ['path' => $fullCertPath]);
                }
            }
        }
    }

    /**
     * Check if Firebase is properly configured
     */
    public static function isConfigured(): bool
    {
        $serviceAccountPath = base_path('dala3chic-e2b81-firebase-adminsdk-fbsvc-e5c52a715e.json');
        
        if (file_exists($serviceAccountPath)) {
            return true;
        }

        return !empty(config('services.firebase.private_key')) &&
               !empty(config('services.firebase.client_email')) &&
               !empty(config('services.firebase.project_id'));
    }

    /**
     * Get Firebase Auth instance
     */
    public static function getAuth(): ?Auth
    {
        return app('firebase.auth');
    }

    /**
     * Get Firebase Factory instance
     */
    public static function getFactory(): Factory
    {
        return app('firebase.factory');
    }
}
