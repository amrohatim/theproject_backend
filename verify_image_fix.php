<?php

/**
 * Comprehensive verification script for the image 403 fix
 */

echo "🔍 VERIFYING IMAGE ACCESS FIX\n";
echo "============================\n\n";

$baseUrl = 'https://dala3chic.com';
$errors = [];
$successes = [];

/**
 * Test URL accessibility
 */
function testUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode;
}

// 1. Test storage directory structure
echo "1. TESTING STORAGE DIRECTORY STRUCTURE\n";
echo "--------------------------------------\n";

$storageDir = __DIR__ . '/public/storage';
if (is_dir($storageDir)) {
    echo "✅ public/storage directory exists\n";
    $successes[] = "Storage directory exists";
} else {
    echo "❌ public/storage directory missing\n";
    $errors[] = "Storage directory missing";
}

$htaccessPath = $storageDir . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "✅ .htaccess file exists in storage directory\n";
    $successes[] = ".htaccess file exists";
} else {
    echo "❌ .htaccess file missing in storage directory\n";
    $errors[] = ".htaccess file missing";
}

// 2. Test image directories
echo "\n2. TESTING IMAGE DIRECTORIES\n";
echo "----------------------------\n";

$imageDirs = ['services', 'products', 'product-colors', 'merchant-logos'];
foreach ($imageDirs as $dir) {
    $dirPath = $storageDir . '/' . $dir;
    if (is_dir($dirPath)) {
        $fileCount = count(array_diff(scandir($dirPath), array('.', '..')));
        echo "✅ {$dir} directory exists with {$fileCount} files\n";
        $successes[] = "{$dir} directory accessible";
    } else {
        echo "❌ {$dir} directory missing\n";
        $errors[] = "{$dir} directory missing";
    }
}

// 3. Test specific image URLs
echo "\n3. TESTING SPECIFIC IMAGE URLS\n";
echo "------------------------------\n";

$testImages = [];

// Get some test images from each directory
foreach ($imageDirs as $dir) {
    $dirPath = $storageDir . '/' . $dir;
    if (is_dir($dirPath)) {
        $files = array_diff(scandir($dirPath), array('.', '..'));
        if (!empty($files)) {
            $testFile = reset($files);
            $testImages[] = "/storage/{$dir}/{$testFile}";
        }
    }
}

foreach ($testImages as $imagePath) {
    $fullUrl = $baseUrl . $imagePath;
    $httpCode = testUrl($fullUrl);
    
    if ($httpCode == 200) {
        echo "✅ {$imagePath} - HTTP {$httpCode}\n";
        $successes[] = "Image accessible: {$imagePath}";
    } else {
        echo "❌ {$imagePath} - HTTP {$httpCode}\n";
        $errors[] = "Image not accessible: {$imagePath} (HTTP {$httpCode})";
    }
}

// 4. Test file permissions
echo "\n4. TESTING FILE PERMISSIONS\n";
echo "---------------------------\n";

$permissionTests = [
    $storageDir => 'Storage directory',
    $htaccessPath => '.htaccess file'
];

foreach ($permissionTests as $path => $description) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        if (is_dir($path)) {
            $expected = in_array($perms, ['0755', '0775']) ? '✅' : '⚠️';
        } else {
            $expected = in_array($perms, ['0644', '0664']) ? '✅' : '⚠️';
        }
        echo "{$expected} {$description}: {$perms}\n";
        
        if ($expected === '✅') {
            $successes[] = "Correct permissions: {$description}";
        } else {
            $errors[] = "Incorrect permissions: {$description} ({$perms})";
        }
    }
}

// 5. Test sync command
echo "\n5. TESTING SYNC COMMAND\n";
echo "----------------------\n";

$output = shell_exec('cd ' . __DIR__ . ' && php artisan storage:sync 2>&1');
if (strpos($output, 'Sync completed') !== false) {
    echo "✅ Storage sync command works\n";
    $successes[] = "Sync command functional";
} else {
    echo "❌ Storage sync command failed\n";
    echo "Output: {$output}\n";
    $errors[] = "Sync command failed";
}

// 6. Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "VERIFICATION SUMMARY\n";
echo str_repeat("=", 50) . "\n";

echo "\n✅ SUCCESSES (" . count($successes) . "):\n";
foreach ($successes as $success) {
    echo "  • {$success}\n";
}

if (!empty($errors)) {
    echo "\n❌ ISSUES (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "  • {$error}\n";
    }
} else {
    echo "\n🎉 ALL TESTS PASSED! Image access is working correctly.\n";
}

echo "\n📝 RECOMMENDATIONS:\n";
echo "  • Run 'php artisan storage:sync' after new image uploads\n";
echo "  • Consider adding a cron job: */5 * * * * php artisan storage:sync\n";
echo "  • Monitor server logs for any 403 errors\n";

echo "\n✅ FIX VERIFICATION COMPLETED\n";

?>
