<?php

echo "🧪 Testing Mailgun Configuration (Simple Test)\n";
echo "=============================================\n\n";

// Test 1: Check if curl is available
echo "📧 Test 1: Checking cURL availability...\n";
if (function_exists('curl_init')) {
    echo "✅ cURL is available\n\n";
} else {
    echo "❌ cURL is not available - cannot test Mailgun\n";
    exit(1);
}

// Test 2: Test Mailgun API directly
echo "📧 Test 2: Testing Mailgun API directly...\n";

$mailgunDomain = 'dala3chic.com';
$mailgunApiKey = '461a36c520950c5207840d2b2288ad4f-a1dad75f-1cb3eb0e';
$fromEmail = 'dala@dala3chic.com';
$toEmail = 'gogoh3296@gmail.com';

$url = "https://api.mailgun.net/v3/{$mailgunDomain}/messages";

$postData = [
    'from' => "Dala3Chic Test <{$fromEmail}>",
    'to' => $toEmail,
    'subject' => 'Test Email from Dala3Chic - Email Verification Setup',
    'text' => "Hello!\n\nThis is a test email to verify that Mailgun is configured correctly for your Dala3Chic application.\n\nIf you receive this email, the Mailgun configuration is working properly!\n\nTest Details:\n- Domain: {$mailgunDomain}\n- From: {$fromEmail}\n- To: {$toEmail}\n- Time: " . date('Y-m-d H:i:s') . "\n\nBest regards,\nDala3Chic Team",
    'html' => "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #8B5CF6;'>🎉 Mailgun Test Successful!</h2>
            <p>Hello!</p>
            <p>This is a test email to verify that <strong>Mailgun is configured correctly</strong> for your Dala3Chic application.</p>
            <div style='background-color: #F3F4F6; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <p><strong>✅ If you receive this email, the Mailgun configuration is working properly!</strong></p>
            </div>
            <h3>Test Details:</h3>
            <ul>
                <li><strong>Domain:</strong> {$mailgunDomain}</li>
                <li><strong>From:</strong> {$fromEmail}</li>
                <li><strong>To:</strong> {$toEmail}</li>
                <li><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</li>
            </ul>
            <p>Best regards,<br><strong>Dala3Chic Team</strong></p>
        </div>
    </body>
    </html>"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "api:{$mailgunApiKey}");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
// Disable SSL verification for testing (not recommended for production)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: {$error}\n\n";
} else {
    echo "📤 HTTP Response Code: {$httpCode}\n";
    echo "📤 Response: {$response}\n\n";
    
    if ($httpCode == 200) {
        echo "✅ Email sent successfully via Mailgun!\n";
        echo "📬 Please check your email at {$toEmail}\n";
        echo "📋 If you receive the email, Mailgun configuration is working correctly.\n\n";
    } else {
        echo "❌ Failed to send email. HTTP Code: {$httpCode}\n";
        echo "📋 Response: {$response}\n\n";
    }
}

// Test 3: Configuration summary
echo "📧 Test 3: Configuration Summary...\n";
echo "✓ Mailgun Configuration:\n";
echo "  - Domain: {$mailgunDomain}\n";
echo "  - API Key: " . substr($mailgunApiKey, 0, 10) . "...\n";
echo "  - From Email: {$fromEmail}\n";
echo "  - Test Email: {$toEmail}\n\n";

echo "🎯 Next Steps:\n";
echo "1. Check your email inbox at {$toEmail}\n";
echo "2. Look for an email from 'Dala3Chic Test'\n";
echo "3. If received, Mailgun is working correctly\n";
echo "4. If not received, check spam folder or verify Mailgun domain settings\n\n";

echo "📝 Note: This test bypasses Laravel and tests Mailgun directly.\n";
echo "If this test succeeds, the issue might be with Laravel configuration.\n";
