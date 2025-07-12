const { execSync } = require('child_process');

/**
 * Setup function to prepare the test environment
 */
async function globalSetup() {
  console.log('🔧 Setting up test environment...');
  
  try {
    // Clear caches
    console.log('Clearing Laravel caches...');
    execSync('php artisan config:clear', { stdio: 'inherit' });
    execSync('php artisan cache:clear', { stdio: 'inherit' });
    execSync('php artisan route:clear', { stdio: 'inherit' });
    execSync('php artisan view:clear', { stdio: 'inherit' });
    
    // Run migrations
    console.log('Running database migrations...');
    execSync('php artisan migrate:fresh --force', { stdio: 'inherit' });
    
    // Seed test data if needed
    console.log('Seeding test data...');
    execSync('php artisan db:seed --force', { stdio: 'inherit' });
    
    // Create test data for validation tests
    console.log('Creating test data for validation tests...');
    execSync('php artisan tinker --execute="
      use App\\Models\\User;
      use App\\Models\\Provider;
      
      // Create a user with verified registration step
      $verifiedUser = User::create([
        \'name\' => \'Verified User\',
        \'email\' => \'verified@test.com\',
        \'phone\' => \'+971501111111\',
        \'password\' => bcrypt(\'password\'),
        \'role\' => \'provider\',
        \'registration_step\' => \'verified\',
        \'email_verified_at\' => now(),
        \'phone_verified\' => true,
        \'phone_verified_at\' => now()
      ]);
      
      // Create a user with license_completed registration step
      $licenseUser = User::create([
        \'name\' => \'License User\',
        \'email\' => \'license@test.com\',
        \'phone\' => \'+971502222222\',
        \'password\' => bcrypt(\'password\'),
        \'role\' => \'provider\',
        \'registration_step\' => \'license_completed\',
        \'email_verified_at\' => now(),
        \'phone_verified\' => true,
        \'phone_verified_at\' => now()
      ]);
      
      // Create provider with existing business name
      $existingProvider = User::create([
        \'name\' => \'Existing Provider\',
        \'email\' => \'existing@test.com\',
        \'phone\' => \'+971503333333\',
        \'password\' => bcrypt(\'password\'),
        \'role\' => \'provider\',
        \'registration_step\' => \'verified\',
        \'email_verified_at\' => now(),
        \'phone_verified\' => true,
        \'phone_verified_at\' => now()
      ]);
      
      Provider::create([
        \'user_id\' => $existingProvider->id,
        \'business_name\' => \'Existing Business Name\',
        \'business_type\' => \'Technology\',
        \'status\' => \'active\',
        \'is_verified\' => true
      ]);
      
      echo \'Test data created successfully\';
    "', { stdio: 'inherit' });
    
    console.log('✅ Test environment setup complete!');
    
  } catch (error) {
    console.error('❌ Error setting up test environment:', error.message);
    process.exit(1);
  }
}

module.exports = globalSetup;
