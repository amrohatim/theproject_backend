<?php

echo "🧪 Detailed Mailgun API Test\n";
echo "============================\n\n";

$mailgunDomain = 'dala3chic.com';
$mailgunApiKey = '23901c987ba068f7567ea73e1d98273e-a1dad75f-c748adae';
$fromEmail = 'dala@dala3chic.com';
$toEmail = 'gogoh3296@gmail.com';

echo "📧 Testing Mailgun API with detailed debugging...\n";
echo "Domain: {$mailgunDomain}\n";
echo "API Key: " . substr($mailgunApiKey, 0, 10) . "...\n";
echo "From: {$fromEmail}\n";
echo "To: {$toEmail}\n\n";

// Test 1: Check domain endpoint
echo "📧 Test 1: Checking domain info...\n";
$domainUrl = "https://api.mailgun.net/v3/domains/{$mailgunDomain}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $domainUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "api:{$mailgunApiKey}");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$domainResponse = curl_exec($ch);
$domainHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Domain API Response Code: {$domainHttpCode}\n";
echo "Domain Response: " . substr($domainResponse, 0, 200) . "...\n\n";

// Test 2: Try sending email with more detailed error handling
echo "📧 Test 2: Sending email with detailed error handling...\n";

$url = "https://api.mailgun.net/v3/{$mailgunDomain}/messages";

$postData = [
    'from' => "Dala3Chic Test <{$fromEmail}>",
    'to' => $toEmail,
    'subject' => 'Mailgun API Test - Dala3Chic',
    'text' => "Hello!\n\nThis is a test email sent via Mailgun API to verify the configuration.\n\nTest Details:\n- Domain: {$mailgunDomain}\n- API Key: " . substr($mailgunApiKey, 0, 10) . "...\n- Time: " . date('Y-m-d H:i:s') . "\n\nIf you receive this email, Mailgun is working correctly!\n\nBest regards,\nDala3Chic Team"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "api:{$mailgunApiKey}");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);

// Capture verbose output
$verboseOutput = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verboseOutput);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

// Get verbose output
rewind($verboseOutput);
$verboseInfo = stream_get_contents($verboseOutput);
fclose($verboseOutput);

curl_close($ch);

echo "Email API Response Code: {$httpCode}\n";
echo "Email Response: {$response}\n";

if ($error) {
    echo "cURL Error: {$error}\n";
}

echo "\nVerbose cURL Info:\n";
echo substr($verboseInfo, 0, 500) . "...\n\n";

// Test 3: Try different API endpoints
echo "📧 Test 3: Testing API key validation...\n";

$validateUrl = "https://api.mailgun.net/v3/domains";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $validateUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "api:{$mailgunApiKey}");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$validateResponse = curl_exec($ch);
$validateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Domains List Response Code: {$validateHttpCode}\n";
echo "Domains Response: " . substr($validateResponse, 0, 300) . "...\n\n";

// Analysis
echo "🔍 Analysis:\n";
if ($validateHttpCode == 200) {
    echo "✅ API Key is valid - can access domains list\n";
} else {
    echo "❌ API Key validation failed - HTTP {$validateHttpCode}\n";
}

if ($domainHttpCode == 200) {
    echo "✅ Domain '{$mailgunDomain}' is accessible\n";
} else {
    echo "❌ Domain '{$mailgunDomain}' access failed - HTTP {$domainHttpCode}\n";
}

if ($httpCode == 200) {
    echo "✅ Email sent successfully!\n";
    echo "📬 Check your email at {$toEmail}\n";
} else {
    echo "❌ Email sending failed - HTTP {$httpCode}\n";
    
    if ($httpCode == 401) {
        echo "🔍 401 Unauthorized suggests:\n";
        echo "  - API key is incorrect\n";
        echo "  - API key doesn't have permission for this domain\n";
        echo "  - Domain is not properly configured\n";
    } elseif ($httpCode == 403) {
        echo "🔍 403 Forbidden suggests:\n";
        echo "  - Domain is not verified\n";
        echo "  - Account has restrictions\n";
        echo "  - Sending limits exceeded\n";
    }
}

echo "\n📋 Recommendations:\n";
echo "1. Verify the API key is correct in Mailgun dashboard\n";
echo "2. Ensure the domain '{$mailgunDomain}' is fully verified\n";
echo "3. Check if the API key has the correct permissions\n";
echo "4. Verify account status and sending limits\n";
