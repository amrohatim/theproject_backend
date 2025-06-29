<?php

echo "🧪 Testing SMTP Email Configuration\n";
echo "===================================\n\n";

// Test 1: Check if required functions are available
echo "📧 Test 1: Checking PHP mail functions...\n";
if (function_exists('mail')) {
    echo "✅ PHP mail() function is available\n";
} else {
    echo "❌ PHP mail() function is not available\n";
}

if (extension_loaded('openssl')) {
    echo "✅ OpenSSL extension is loaded (required for TLS)\n";
} else {
    echo "❌ OpenSSL extension is not loaded\n";
}
echo "\n";

// Test 2: Test SMTP connection using PHPMailer-like approach
echo "📧 Test 2: Testing SMTP connection...\n";

$smtpHost = 'smtp.mailgun.org';
$smtpPort = 587;
$smtpUsername = 'amro@www.dala3chic.com';
$smtpPassword = '17fa1857f6cbc7c7d92815e6d0123a7e-a1dad75f-564a318d';
$fromEmail = 'amro@www.dala3chic.com';
$toEmail = 'gogoh3296@gmail.com';

// Test SMTP connection
$socket = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 10);
if ($socket) {
    echo "✅ Successfully connected to {$smtpHost}:{$smtpPort}\n";
    fclose($socket);
} else {
    echo "❌ Failed to connect to {$smtpHost}:{$smtpPort} - Error: {$errstr} ({$errno})\n";
}
echo "\n";

// Test 3: Send test email using PHP's mail() function with SMTP headers
echo "📧 Test 3: Sending test email...\n";

$subject = 'Test Email from Dala3Chic - SMTP Configuration';
$message = "Hello!\n\n";
$message .= "This is a test email to verify that SMTP is configured correctly for your Dala3Chic application.\n\n";
$message .= "If you receive this email, the SMTP configuration is working properly!\n\n";
$message .= "Test Details:\n";
$message .= "- SMTP Host: {$smtpHost}\n";
$message .= "- SMTP Port: {$smtpPort}\n";
$message .= "- From: {$fromEmail}\n";
$message .= "- To: {$toEmail}\n";
$message .= "- Time: " . date('Y-m-d H:i:s') . "\n\n";
$message .= "Best regards,\nDala3Chic Team";

$headers = "From: Dala3Chic Test <{$fromEmail}>\r\n";
$headers .= "Reply-To: {$fromEmail}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Configure PHP to use SMTP (this is a basic test)
ini_set('SMTP', $smtpHost);
ini_set('smtp_port', $smtpPort);
ini_set('sendmail_from', $fromEmail);

$mailSent = @mail($toEmail, $subject, $message, $headers);

if ($mailSent) {
    echo "✅ Email sent successfully using PHP mail()!\n";
    echo "📬 Please check your email at {$toEmail}\n";
} else {
    echo "❌ Failed to send email using PHP mail()\n";
    echo "📋 Note: PHP mail() may not support SMTP authentication directly\n";
}
echo "\n";

// Test 4: Configuration summary
echo "📧 Test 4: SMTP Configuration Summary...\n";
echo "✓ SMTP Configuration:\n";
echo "  - Host: {$smtpHost}\n";
echo "  - Port: {$smtpPort}\n";
echo "  - Username: {$smtpUsername}\n";
echo "  - Password: " . substr($smtpPassword, 0, 10) . "...\n";
echo "  - From Email: {$fromEmail}\n";
echo "  - Test Email: {$toEmail}\n";
echo "  - Encryption: TLS\n\n";

// Test 5: Laravel configuration check
echo "📧 Test 5: Laravel .env Configuration...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    if (strpos($envContent, 'MAIL_MAILER=smtp') !== false) {
        echo "✅ MAIL_MAILER is set to smtp\n";
    } else {
        echo "❌ MAIL_MAILER is not set to smtp\n";
    }
    
    if (strpos($envContent, 'MAIL_HOST=smtp.mailgun.org') !== false) {
        echo "✅ MAIL_HOST is set correctly\n";
    } else {
        echo "❌ MAIL_HOST is not set correctly\n";
    }
    
    if (strpos($envContent, 'MAIL_PORT=587') !== false) {
        echo "✅ MAIL_PORT is set correctly\n";
    } else {
        echo "❌ MAIL_PORT is not set correctly\n";
    }
    
    if (strpos($envContent, $smtpUsername) !== false) {
        echo "✅ MAIL_USERNAME is set correctly\n";
    } else {
        echo "❌ MAIL_USERNAME is not set correctly\n";
    }
} else {
    echo "❌ .env file not found\n";
}
echo "\n";

echo "🎯 Next Steps:\n";
echo "1. Check your email inbox at {$toEmail}\n";
echo "2. Look for an email from 'Dala3Chic Test'\n";
echo "3. If received, SMTP is working correctly\n";
echo "4. If not received, we'll test with Laravel's mail system\n\n";

echo "📝 Note: This test uses basic PHP mail() which may not support SMTP auth.\n";
echo "Laravel's mail system will handle SMTP authentication properly.\n";
