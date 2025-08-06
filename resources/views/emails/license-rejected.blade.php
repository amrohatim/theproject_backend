<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Rejected - Dala3Chic</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }
        .provider-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }
        .merchant-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 90px;
            vertical-align: middle;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
        .rejection-banner {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
            font-size: 20px;
            font-weight: bold;
        }
        .message-content {
            background-color: #ffffff;
            border-left: 4px solid #dc2626;
            color: #000000de;
            padding: 10px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
            white-space: pre-line;
        }
        .rejection-reason {
            background-color: #fffeff;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .rejection-reason h4 {
            color: #403434;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .rejection-reason p {
            margin-bottom: 0;
            color: #7f1d1d;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .call-to-action {
            background-color: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .rtl {
            direction: rtl;
            text-align: right;
        }
        .rtl .message-content {
            border-left: none;
            border-right: 4px solid #dc2626;
            border-radius: 8px 0 0 8px;
        }
        .rtl .call-to-action {
            border-left: none;
            border-right: 4px solid #0ea5e9;
            border-radius: 8px 0 0 8px;
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
            <h1>{{ __('Registration Update') }}</h1>
            <p class="subtitle">{{ __('Important information about your application') }}</p>
        </div>

        @php
            // Get message content from the template file
            $templatePath = resource_path('../message_rejected.md');
            $messageData = ['title' => '', 'body' => '', 'call_to_action' => ''];
            
            if (file_exists($templatePath)) {
                $content = file_get_contents($templatePath);
                
                // Extract title
                if (preg_match('/\*\*Title\*\*\s*\n(.+?)(?=\n\*\*|$)/s', $content, $matches)) {
                    $messageData['title'] = trim($matches[1]);
                }
                
                // Extract body
                if (preg_match('/\*\*Body\*\*\s*\n(.*?)(?=\n\*\*Reason\*\*)/s', $content, $matches)) {
                    $messageData['body'] = trim($matches[1]);
                }
                
                // Extract call to action
                if (preg_match('/\*\*Call to Action\*\*\s*\n(.+?)(?=\n|$)/s', $content, $matches)) {
                    $messageData['call_to_action'] = trim($matches[1]);
                }
                
                // Replace placeholders
                $messageData['body'] = str_replace('[User Name]', $user->name, $messageData['body']);
            } else {
                // Fallback content
                $messageData['title'] = 'Your Dala3Chic Registration Has Been Rejected';
                $messageData['body'] = "Hello {$user->name},\n\nWe regret to inform you that your Dala3Chic registration has been rejected. We have reviewed your application and found that it does not meet our requirements. Please review our guidelines and reapply once you have made the necessary improvements.";
                $messageData['call_to_action'] = 'Please review our guidelines and reapply once you have made the necessary improvements.';
            }
        @endphp

        

        <!-- Message Content from Template -->
        <div class="message-content {{ app()->getLocale() === 'ar' ? 'rtl' : '' }}">
            Hello {{$user->name}},.
       <br>
       <p class="text-[#000000de]">We regret to inform you that your Dala3Chic registration has been rejected</p>
       <br>
       <p class="text-[#000000de]"> We have reviewed your application and found that it does not meet our requirements. Please review our guidelines and reapply once you have made the necessary improvements.</p>
        </div>

        <!-- Rejection Reason -->
        <div class="rejection-reason">
            <h4>{{ __('Reason for Rejection:') }}</h4>
            <p>{{ $rejectionReason }}</p>
        </div>

        <!-- Call to Action -->
        @if($messageData['call_to_action'])
            <div class="call-to-action {{ app()->getLocale() === 'ar' ? 'rtl' : '' }}">
                <strong>{{ __('Next Steps:') }}</strong><br>
                {{ $messageData['call_to_action'] }}
            </div>
        @endif

       

        <!-- Support Information -->
        <div style="margin-top: 30px;">
            <p>{{ __('If you have any questions about this decision or need assistance, our support team is here to help:') }}</p>
            <ul>
                <li><strong>{{ __('Email:') }}</strong> support@dala3chic.com</li>
                <li><strong>{{ __('Phone:') }}</strong> +971-xxx-xxxx</li>
                <li><strong>{{ __('Help Center:') }}</strong> <a href="https://help.dala3chic.com">help.dala3chic.com</a></li>
            </ul>
        </div>

        <p>{{ __('Best regards,') }}<br>
        {{ __('The Dala3Chic Team') }}</p>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} Dala3Chic. {{ __('All rights reserved.') }}</p>
            <p>{{ __('This email was sent to :email.', ['email' => $user->email]) }}</p>
        </div>
    </div>
</body>
</html>
