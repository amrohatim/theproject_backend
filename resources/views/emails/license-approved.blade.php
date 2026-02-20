<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Approved - glowlabs</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            padding-left: 21px;
        }
        .vendor-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            background:  #667eea;
        }
        .provider-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            background:  #f093fb;
        }
        .merchant-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            background:  #fbbf24;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #383a39;
            font-size: 16px;
            margin-bottom: 30px;
            font-weight:600;
        }
        .celebration-banner {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
            font-size: 20px;
            font-weight: bold;
        }
        .vendor-banner {
            background:  #667eea
        }
        .provider-banner {
            background: #f093fb 
        }
        .merchant-banner {
            background:  #fbbf24 
        }
        .message-content {
            background-color: #f7fafc;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
            white-space: pre-line;
        }
        .vendor-content {
            border-left-color: #667eea;
        }
        .provider-content {
            border-left-color: #f093fb;
        }
        .merchant-content {
            border-left-color: #fbbf24;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s ease;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .vendor-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .provider-button {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .merchant-button {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .admin-message {
            background-color: #edf2f7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #4299e1;
        }
        .rtl {
            direction: rtl;
            text-align: right;
        }
        .rtl .message-content {
            border-left: none;
            border-right: 4px solid #10b981;
            border-radius: 8px 0 0 8px;
        }
        .rtl .vendor-content {
            border-right-color: #667eea;
        }
        .rtl .provider-content {
            border-right-color: #f093fb;
        }
        .rtl .merchant-content {
            border-right-color: #fbbf24;
        }
        .rtl .admin-message {
            border-left: none;
            border-right: 4px solid #4299e1;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo {{ $licenseType }}-logo">
                D3C
            </div>
            <h1>{{ __('Congratulations!') }}</h1>
            <p class="subtitle">{{ __('Your license has been approved') }}</p>
            
        </div>

        <!-- Celebration Banner -->
        <div class="celebration-banner {{ $licenseType }}-banner">
             {{ __('Welcome to the glowlabs :type Community!', ['type' => ucfirst($licenseType)]) }} 
        </div>

        @php
            // Get message content from the template file
            $templatePath = resource_path('views/messages_when_approval.md');
            $content = file_get_contents($templatePath);

            // Parse the content based on license type and language
            $language = app()->getLocale() === 'ar' ? 'AR' : 'EN';
            $pattern = "/\*\*" . ucfirst($licenseType) . " message when approved:{$language}\*\*(.*?)(?=\*\*|$)/is";
            preg_match($pattern, $content, $matches);

            $messageBody = '';
            if (!empty($matches[1])) {
                $messageContent = trim($matches[1]);

                // Extract body (skip subject line)
                if (preg_match('/Subject:\s*(.+?)(?:\n\n|\n(?=[A-Z]))/s', $messageContent, $subjectMatch)) {
                    $messageBody = trim(str_replace($subjectMatch[0], '', $messageContent));
                } else {
                    $messageBody = $messageContent;
                }

                // Replace placeholders
                $userName = $user->name ?? 'Valued User';
                $messageBody = str_replace(
                    ['[Provider Name]', '[Vendor Name]', '[Merchant Name]', '[اسم المزود ]', '[اسم البائع ]', '[اسم التاجر ]'],
                    $userName,
                    $messageBody
                );
            }

            // Fallback message if parsing fails
            if (empty($messageBody)) {
                $type = ucfirst($licenseType);
                $messageBody = "Hello {$user->name},\n\nWe are thrilled to inform you that your glowlabs {$licenseType} registration has been approved! You are now officially part of the glowlabs marketplace.\n\nYou can access your dashboard and manage your account using your email and password at: https://glowlabs.ae/login";
            }
        @endphp

        <!-- Message Content from Template -->
        <div class="message-content {{ $licenseType }}-content {{ app()->getLocale() === 'ar' ? 'rtl' : '' }} text-color-black ">
            {!! nl2br(e($messageBody)) !!}
        </div>

        <!-- Dashboard Button -->
        <div style="text-align: center;">
            <a href="https://glowlabs.ae/login"
               class="button {{ $licenseType }}-button">
                {{ __('Access Your Dashboard') }}
            </a>
        </div>

        <!-- Admin Message (if provided) -->
        @if($adminMessage)
            <div class="admin-message {{ app()->getLocale() === 'ar' ? 'rtl' : '' }}">
                <h4 style="margin-top: 0; color: #2d3748;">{{ __('Message from Admin:') }}</h4>
                <p style="margin-bottom: 0;">{{ $adminMessage }}</p>
            </div>
        @endif

        <!-- Support Information -->
        <div style="margin-top: 30px;">
            <p>{{ __('If you have any questions or need assistance, our support team is here to help:') }}</p>
            <ul>
                <li><strong>{{ __('Email:') }}</strong> support@glowlabs.ae</li>
                <li><strong>{{ __('Phone:') }}</strong> +971-xxx-xxxx</li>
                <li><strong>{{ __('Help Center:') }}</strong> <a href="https://help.glowlabs.ae">help.glowlabs.ae</a></li>
            </ul>
        </div>

        <p>{{ __('Best regards,') }}<br>
        {{ __('The glowlabs Team') }}</p>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} glowlabs. {{ __('All rights reserved.') }}</p>
            <p>{{ __('This email was sent to :email.', ['email' => $user->email]) }}</p>
        </div>
    </div>
</body>
</html>
