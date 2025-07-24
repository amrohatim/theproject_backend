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
            width: 70px;
            height: 90px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            padding-left: 21px;
        }
        .logo.provider-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }
        .logo.vendor-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        .logo.merchant-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        h1 {
            color: #2d3748;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .subtitle {
            color: #718096;
            margin: 0 0 20px 0;
            font-size: 16px;
        }
        .verification-code {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 30px 0;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .verification-code.provider-code {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }
        .verification-code.merchant-code {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        .instructions {
            background-color: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .warning {
            background-color: #fef5e7;
            border: 1px solid #f6ad55;
            color: #744210;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .button.provider-button {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class= "logo {{ $userType === 'provider' ? 'provider-logo' : ($userType === 'merchant' ? 'merchant-logo' : 'vendor-logo') }}">
               D3C
            </div>
            <h1>Email Verification</h1>
            <p class="subtitle">
                @if($userType === 'vendor')
                    Welcome to Dala3Chic Vendor Platform
                @elseif($userType === 'provider')
                    Welcome to Dala3Chic Provider Platform
                @elseif($userType === 'merchant')
                    Welcome to Dala3Chic Merchant Platform
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
            @elseif($userType === 'merchant')
                as a merchant
            @endif
            with Dala3Chic! To complete your registration, please verify your email address.
        </p>

        <!-- Verification Code -->
        <div class="verification-code {{ $userType === 'provider' ? 'provider-code' : ($userType === 'merchant' ? 'merchant-code' : '') }}">
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

        <!-- Instructions for temporary registration -->
        <div style="text-align: center;">
            <p>Please return to the registration page and enter the verification code above to continue.</p>
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
            @elseif($userType === 'merchant')
                Once verified, you'll be able to set up your merchant account, manage your business profile, and start accepting orders.
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
