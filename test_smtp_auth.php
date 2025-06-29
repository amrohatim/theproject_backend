<?php

echo "🧪 Testing SMTP Authentication\n";
echo "==============================\n\n";

$smtpHost = 'smtp.eu.mailgun.org';
$smtpPort = 587;
$smtpUsername = 'dala@dala3chic.com';
$smtpPassword = '461a36c520950c5207840d2b2288ad4f-a1dad75f-1cb3eb0e';

echo "📧 Testing SMTP connection and authentication...\n";
echo "Host: {$smtpHost}\n";
echo "Port: {$smtpPort}\n";
echo "Username: {$smtpUsername}\n";
echo "Password: " . substr($smtpPassword, 0, 10) . "...\n\n";

// Test SMTP connection manually
$socket = fsockopen($smtpHost, $smtpPort, $errno, $errstr, 30);

if (!$socket) {
    echo "❌ Failed to connect to SMTP server: {$errstr} ({$errno})\n";
    exit(1);
}

echo "✅ Connected to SMTP server\n";

// Read initial response
$response = fgets($socket, 512);
echo "Server: " . trim($response) . "\n";

// Send EHLO command
fwrite($socket, "EHLO localhost\r\n");
$response = '';
while ($line = fgets($socket, 512)) {
    $response .= $line;
    if (substr($line, 3, 1) == ' ') break;
}
echo "EHLO Response:\n" . $response . "\n";

// Start TLS
fwrite($socket, "STARTTLS\r\n");
$response = fgets($socket, 512);
echo "STARTTLS: " . trim($response) . "\n";

if (strpos($response, '220') === 0) {
    echo "✅ TLS negotiation started\n";
    
    // Enable crypto
    if (stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        echo "✅ TLS encryption enabled\n";
        
        // Send EHLO again after TLS
        fwrite($socket, "EHLO localhost\r\n");
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
        echo "EHLO after TLS:\n" . $response . "\n";
        
        // Try authentication
        $authString = base64_encode("\0{$smtpUsername}\0{$smtpPassword}");
        fwrite($socket, "AUTH PLAIN {$authString}\r\n");
        $authResponse = fgets($socket, 512);
        echo "AUTH Response: " . trim($authResponse) . "\n";
        
        if (strpos($authResponse, '235') === 0) {
            echo "✅ Authentication successful!\n";
        } else {
            echo "❌ Authentication failed\n";
            
            // Try LOGIN method
            echo "\nTrying LOGIN authentication method...\n";
            fwrite($socket, "AUTH LOGIN\r\n");
            $loginResponse = fgets($socket, 512);
            echo "AUTH LOGIN: " . trim($loginResponse) . "\n";
            
            if (strpos($loginResponse, '334') === 0) {
                // Send username
                fwrite($socket, base64_encode($smtpUsername) . "\r\n");
                $userResponse = fgets($socket, 512);
                echo "Username: " . trim($userResponse) . "\n";
                
                // Send password
                fwrite($socket, base64_encode($smtpPassword) . "\r\n");
                $passResponse = fgets($socket, 512);
                echo "Password: " . trim($passResponse) . "\n";
                
                if (strpos($passResponse, '235') === 0) {
                    echo "✅ LOGIN authentication successful!\n";
                } else {
                    echo "❌ LOGIN authentication failed\n";
                }
            }
        }
    } else {
        echo "❌ Failed to enable TLS encryption\n";
    }
} else {
    echo "❌ TLS not supported or failed\n";
}

// Close connection
fwrite($socket, "QUIT\r\n");
fclose($socket);

echo "\n🔍 Troubleshooting suggestions:\n";
echo "1. Verify the username and password in your Mailgun dashboard\n";
echo "2. Check if the domain is properly verified in Mailgun\n";
echo "3. Ensure the SMTP credentials are for the correct domain\n";
echo "4. Try regenerating the SMTP password in Mailgun\n";
echo "5. Check if there are any IP restrictions in Mailgun settings\n\n";

echo "📋 Current credentials being tested:\n";
echo "Domain: www.dala3chic.com\n";
echo "Username: {$smtpUsername}\n";
echo "Password length: " . strlen($smtpPassword) . " characters\n";
echo "Password starts with: " . substr($smtpPassword, 0, 8) . "...\n";
