<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Dala3Chic</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        .vendor-logo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .provider-logo {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        h1 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #718096;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .verification-code {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .provider-code {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .provider-button {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .instructions {
            background-color: #f7fafc;
            border-left: 4px solid #4299e1;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .warning {
            background-color: #fed7d7;
            border: 1px solid #feb2b2;
            color: #c53030;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo {{ $userType === 'provider' ? 'provider-logo' : 'vendor-logo' }}">
                D3C
            </div>
            <h1>Email Verification</h1>
            <p class="subtitle">
                @if($userType === 'vendor')
                    Welcome to Dala3Chic Vendor Platform
                @elseif($userType === 'provider')
                    Welcome to Dala3Chic Provider Platform
                @else
                    Welcome to Dala3Chic
                @endif
            </p>
        </div>

        <!-- Greeting -->
        <p>Hello {{ $user->name }},</p>

        <p>Thank you for registering
            @if($userType === 'vendor')
                as a vendor
            @elseif($userType === 'provider')
                as a service provider
            @endif
            with Dala3Chic! To complete your registration, please verify your email address.
        </p>

        <!-- Verification Code -->
        <div class="verification-code {{ $userType === 'provider' ? 'provider-code' : '' }}">
            {{ $verificationCode }}
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <h3 style="margin-top: 0; color: #2d3748;">How to verify your email:</h3>
            <ol style="margin: 0; padding-left: 20px;">
                <li>Copy the verification code above</li>
                <li>Return to the registration page</li>
                <li>Enter the code in the verification field</li>
                <li>Click "Verify Email" to complete your registration</li>
            </ol>
        </div>

        <!-- Alternative Button -->
        <div style="text-align: center;">
            <p>Or click the button below to verify automatically:</p>
            <a href="{{ route($userType === 'vendor' ? 'vendor.email.verify' : 'provider.email.verify', ['user_id' => $user->id, 'code' => $verificationCode]) }}"
               class="button {{ $userType === 'provider' ? 'provider-button' : '' }}">
                Verify Email Address
            </a>
        </div>

        <!-- Security Warning -->
        <div class="warning">
            <strong>Security Notice:</strong> This verification code will expire in 24 hours. If you didn't request this verification, please ignore this email or contact our support team.
        </div>

        <!-- Additional Info -->
        <p>
            @if($userType === 'vendor')
                Once verified, you'll be able to set up your store, add products, and start selling on our marketplace.
            @elseif($userType === 'provider')
                Once verified, you'll be able to offer your services and connect with customers looking for your expertise.
            @else
                Once verified, you'll have full access to your account.
            @endif
        </p>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Best regards,<br>
        The Dala3Chic Team</p>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} Dala3Chic. All rights reserved.</p>
            <p>This email was sent to {{ $user->email }}. If you didn't request this verification, please ignore this email.</p>
        </div>
    </div>
</body>
</html>