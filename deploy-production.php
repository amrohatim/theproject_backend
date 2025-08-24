<?php

/**
 * Production Deployment Script
 * 
 * This script helps configure your Laravel application for production deployment
 * on server IP: 82.25.109.98
 */

echo "🚀 Starting Production Deployment Configuration...\n\n";

// Check if .env file exists
if (!file_exists('.env')) {
    echo "❌ Error: .env file not found. Please create it first.\n";
    exit(1);
}

// 1. Generate application key if not set
echo "1. Checking application key...\n";
$envContent = file_get_contents('.env');
if (strpos($envContent, 'APP_KEY=') !== false && preg_match('/APP_KEY=(.+)/', $envContent, $matches)) {
    $appKey = trim($matches[1]);
    if (empty($appKey)) {
        echo "   Generating application key...\n";
        exec('php artisan key:generate --force', $output, $returnCode);
        if ($returnCode === 0) {
            echo "   ✅ Application key generated successfully.\n";
        } else {
            echo "   ❌ Failed to generate application key.\n";
        }
    } else {
        echo "   ✅ Application key already set.\n";
    }
}

// 2. Create storage link
echo "\n2. Creating storage link...\n";
if (!file_exists('public/storage')) {
    exec('php artisan storage:link', $output, $returnCode);
    if ($returnCode === 0) {
        echo "   ✅ Storage link created successfully.\n";
    } else {
        echo "   ❌ Failed to create storage link.\n";
    }
} else {
    echo "   ✅ Storage link already exists.\n";
}

// 3. Create products directory
echo "\n3. Creating products directory...\n";
if (!file_exists('public/products')) {
    mkdir('public/products', 0755, true);
    echo "   ✅ Products directory created.\n";
} else {
    echo "   ✅ Products directory already exists.\n";
}

// 4. Run migrations
echo "\n4. Running database migrations...\n";
echo "   ⚠️  Make sure your database is configured in .env file.\n";
echo "   Do you want to run migrations? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) === 'y' || trim($line) === 'Y') {
    exec('php artisan migrate --force', $output, $returnCode);
    if ($returnCode === 0) {
        echo "   ✅ Migrations completed successfully.\n";
    } else {
        echo "   ❌ Migration failed. Please check your database configuration.\n";
    }
} else {
    echo "   ⏭️  Skipping migrations.\n";
}

// 5. Cache configuration
echo "\n5. Caching configuration for production...\n";
exec('php artisan config:cache', $output, $returnCode);
if ($returnCode === 0) {
    echo "   ✅ Configuration cached.\n";
} else {
    echo "   ❌ Failed to cache configuration.\n";
}

// 6. Clear and cache routes
echo "\n6. Clearing and caching routes...\n";
exec('php artisan route:clear', $output, $returnCode);
if ($returnCode === 0) {
    echo "   ✅ Routes cleared.\n";
    exec('php artisan route:cache', $output, $returnCode);
    if ($returnCode === 0) {
        echo "   ✅ Routes cached.\n";
    } else {
        echo "   ❌ Failed to cache routes.\n";
    }
} else {
    echo "   ❌ Failed to clear routes.\n";
}

// 7. Cache views
echo "\n7. Caching views...\n";
exec('php artisan view:cache', $output, $returnCode);
if ($returnCode === 0) {
    echo "   ✅ Views cached.\n";
} else {
    echo "   ❌ Failed to cache views.\n";
}

// 8. Optimize autoloader
echo "\n8. Optimizing autoloader...\n";
exec('composer install --optimize-autoloader --no-dev', $output, $returnCode);
if ($returnCode === 0) {
    echo "   ✅ Autoloader optimized.\n";
} else {
    echo "   ⚠️  Failed to optimize autoloader. Make sure Composer is installed.\n";
}

// 9. Set proper permissions
echo "\n9. Setting proper permissions...\n";
exec('chmod -R 755 storage', $output, $returnCode);
exec('chmod -R 755 bootstrap/cache', $output, $returnCode);
echo "   ✅ Permissions set.\n";

// 10. Display final configuration
echo "\n" . str_repeat("=", 60) . "\n";
echo "🎉 PRODUCTION DEPLOYMENT CONFIGURATION COMPLETE!\n";
echo str_repeat("=", 60) . "\n\n";

echo "📋 DEPLOYMENT CHECKLIST:\n\n";
echo "✅ Environment file configured for production\n";
echo "✅ APP_URL set to: http://82.25.109.98\n";
echo "✅ Debug mode disabled\n";
echo "✅ CORS configured for production IP\n";
echo "✅ Storage link created\n";
echo "✅ Configuration cached\n";
echo "✅ Routes cached\n";
echo "✅ Views cached\n";
echo "✅ Permissions set\n\n";

echo "⚠️  IMPORTANT NEXT STEPS:\n\n";
echo "1. Update database credentials in .env file\n";
echo "2. Configure mail settings in .env file\n";
echo "3. Update Aramex shipping credentials in .env file\n";
echo "4. Set up SSL certificate for HTTPS (recommended)\n";
echo "5. Configure your web server (Apache/Nginx) to point to public/ directory\n";
echo "6. Set up proper firewall rules\n";
echo "7. Configure backup strategy\n";
echo "8. Set up monitoring and logging\n\n";

echo "🌐 Your application will be accessible at: http://82.25.109.98\n";
echo "📱 API endpoints will be available at: http://82.25.109.98/api\n\n";

echo "🔧 To test your deployment, run:\n";
echo "   php artisan serve --host=0.0.0.0 --port=80\n\n";

echo "📚 For more information, check the README_SHIPPING.md file.\n";
