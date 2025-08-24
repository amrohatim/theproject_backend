<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\FirebaseServiceProvider;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class ValidateFirebaseConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:validate {--test : Test Firebase operations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate Firebase configuration and test connectivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔥 Firebase Configuration Validation');
        $this->newLine();

        // Check if Firebase is configured
        if (!FirebaseServiceProvider::isConfigured()) {
            $this->error('❌ Firebase is not properly configured');
            $this->newLine();
            $this->warn('Please ensure one of the following:');
            $this->line('1. Service account file exists: dala3chic-e2b81-firebase-adminsdk-fbsvc-e5c52a715e.json');
            $this->line('2. Environment variables are set:');
            $this->line('   - FIREBASE_PROJECT_ID');
            $this->line('   - FIREBASE_PRIVATE_KEY');
            $this->line('   - FIREBASE_CLIENT_EMAIL');
            return 1;
        }

        $this->info('✅ Firebase configuration found');

        // Check service account file
        $serviceAccountPath = base_path('dala3chic-e2b81-firebase-adminsdk-fbsvc-e5c52a715e.json');
        if (file_exists($serviceAccountPath)) {
            $this->info('✅ Service account file exists');

            // Validate JSON structure
            $content = file_get_contents($serviceAccountPath);
            $json = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('❌ Service account file contains invalid JSON');
                return 1;
            }

            $requiredFields = ['type', 'project_id', 'private_key', 'client_email'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (empty($json[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                $this->error('❌ Service account file missing required fields: ' . implode(', ', $missingFields));
                return 1;
            }

            $this->info('✅ Service account file structure is valid');
        } else {
            $this->warn('⚠️  Service account file not found, checking environment variables...');

            $envVars = [
                'FIREBASE_PROJECT_ID' => config('services.firebase.project_id'),
                'FIREBASE_PRIVATE_KEY' => config('services.firebase.private_key'),
                'FIREBASE_CLIENT_EMAIL' => config('services.firebase.client_email'),
            ];

            $missingVars = [];
            foreach ($envVars as $var => $value) {
                if (empty($value)) {
                    $missingVars[] = $var;
                }
            }

            if (!empty($missingVars)) {
                $this->error('❌ Missing environment variables: ' . implode(', ', $missingVars));
                return 1;
            }

            $this->info('✅ Environment variables are configured');
        }

        // Test Firebase initialization
        try {
            $auth = FirebaseServiceProvider::getAuth();
            if ($auth === null) {
                $this->error('❌ Firebase Auth initialization failed');
                return 1;
            }
            $this->info('✅ Firebase Auth initialized successfully');
        } catch (\Exception $e) {
            $this->error('❌ Firebase initialization failed: ' . $e->getMessage());
            return 1;
        }

        // Test Firebase operations if requested
        if ($this->option('test')) {
            $this->newLine();
            $this->info('🧪 Testing Firebase operations...');

            $firebaseService = new FirebaseService();

            if (!$firebaseService->isAvailable()) {
                $this->error('❌ Firebase service is not available');
                return 1;
            }

            $this->info('✅ Firebase service is available');

            // Test listing users (this will fail if no permissions, but that's OK)
            try {
                $auth->listUsers(1);
                $this->info('✅ Firebase connection test successful');
            } catch (\Exception $e) {
                $this->warn('⚠️  Firebase connection test failed (this may be due to permissions): ' . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('🎉 Firebase validation completed successfully!');

        return 0;
    }
}
