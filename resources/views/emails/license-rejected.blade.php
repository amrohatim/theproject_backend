@php
    $isRtl = app()->getLocale() === 'ar';

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
        $messageData['title'] = 'Your glowlabs Registration Has Been Rejected';
        $messageData['body'] = "Hello {$user->name},\n\nWe regret to inform you that your glowlabs registration has been rejected. We have reviewed your application and found that it does not meet our requirements. Please review our guidelines and reapply once you have made the necessary improvements.";
        $messageData['call_to_action'] = 'Please review our guidelines and reapply once you have made the necessary improvements.';
    }
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Rejected - glowlabs</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f2f2f7;
            font-family: Arial, Helvetica, sans-serif;
            color: #4f5a68;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        img {
            border: 0;
            outline: none;
            text-decoration: none;
            display: block;
            max-width: 100%;
            height: auto;
        }

        .email-shell {
            width: 100%;
            background-color: #f2f2f7;
            padding: 48px 20px;
        }

        .email-card {
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
            background-color: #f2f2f7;
        }

        .logo-wrap {
            padding-bottom: 64px;
        }

        .headline {
            margin: 0;
            font-size: 40px;
            line-height: 44px;
            font-weight: 700;
            color: #4f5a68;
            letter-spacing: -0.4px;
        }

        .subtext {
            margin: 0;
            font-size: 16px;
            line-height: 24px;
            color: #4f5a68;
        }

        .greeting {
            margin: 0 0 12px;
            font-size: 24px;
            line-height: 32px;
            color: #4f5a68;
            font-weight: 400;
        }

        .message-block {
            padding: 0 0 32px;
        }

        .info-card {
            background-color: #ffffff;
            border: 1px solid #d4d5d6;
            border-radius: 8px;
            padding: 14px 16px;
            margin: 0 0 20px;
        }

        .info-title {
            margin: 0 0 8px;
            font-size: 14px;
            line-height: 20px;
            font-weight: 700;
            color: #4f5a68;
        }

        .info-text {
            margin: 0;
            font-size: 14px;
            line-height: 20px;
            color: #4f5a68;
            white-space: pre-line;
        }

        .reason-text {
            color: #7f1d1d;
            font-weight: 700;
        }

        .support-list {
            margin: 0;
            padding-left: 18px;
        }

        .support-list li {
            margin: 0 0 8px;
            font-size: 14px;
            line-height: 20px;
            color: #4f5a68;
        }

        .support-list li:last-child {
            margin-bottom: 0;
        }

        .footer {
            border-top: 1px solid #d4d5d6;
            padding-top: 32px;
        }

        .footer-text {
            margin: 0 0 20px;
            font-size: 14px;
            line-height: 20px;
            color: rgba(79, 90, 104, 0.6);
        }

        .rtl-text {
            direction: rtl;
            text-align: right;
        }

        .rtl-text .support-list {
            padding-right: 18px;
            padding-left: 0;
        }

        @media only screen and (max-width: 600px) {
            .email-shell {
                padding: 24px 14px;
            }

            .email-card {
                width: 100% !important;
            }

            .email-card td {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            .logo-wrap {
                padding-bottom: 40px;
            }

            .headline {
                font-size: 32px;
                line-height: 36px;
            }

            .greeting {
                font-size: 22px;
                line-height: 30px;
            }
        }
    </style>
</head>
<body style="padding-inline:4px;">
    <table role="presentation" width="100%" class="email-shell">
        <tr>
            <td align="center">
                <table role="presentation" width="560" class="email-card">
                    <tr>
                        <td class="logo-wrap">
                            <img src="https://glowlabs.ae/assets/logo.png" alt="Glowlabs" width="124">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 48px;" class="{{ $isRtl ? 'rtl-text' : '' }}">
                            <p class="greeting">{{ __('Hello') }} {{ $user->name }},</p>
                            <h1 class="headline">{{ __('Registration Update') }}</h1>
                        </td>
                    </tr>

                    <tr>
                        <td class="message-block {{ $isRtl ? 'rtl-text' : '' }}">
                            <p class="subtext">{{ __('Important information about your application') }}</p>
                        </td>
                    </tr>

                    @if($messageData['title'])
                    <tr>
                        <td>
                            <div class="info-card {{ $isRtl ? 'rtl-text' : '' }}">
                                <p class="info-title">{{ $messageData['title'] }}</p>
                            </div>
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td>
                            <div class="info-card {{ $isRtl ? 'rtl-text' : '' }}">
                                <p class="info-text">{!! nl2br(e($messageData['body'])) !!}</p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="info-card {{ $isRtl ? 'rtl-text' : '' }}">
                                <p class="info-title">{{ __('Reason for Rejection:') }}</p>
                                <p class="info-text reason-text">{{ $rejectionReason }}</p>
                            </div>
                        </td>
                    </tr>

                    @if($messageData['call_to_action'])
                    <tr>
                        <td>
                            <div class="info-card {{ $isRtl ? 'rtl-text' : '' }}">
                                <p class="info-title">{{ __('Next Steps:') }}</p>
                                <p class="info-text">{{ $messageData['call_to_action'] }}</p>
                            </div>
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td>
                            <div class="info-card {{ $isRtl ? 'rtl-text' : '' }}">
                                <p class="info-title">{{ __('Support Information') }}</p>
                                <p class="info-text" style="margin-bottom: 12px;">{{ __('If you have any questions about this decision or need assistance, our support team is here to help:') }}</p>
                                <ul class="support-list">
                                    <li><strong>{{ __('Email:') }}</strong> support@glowlabs.ae</li>
                                    <li><strong>{{ __('Phone:') }}</strong> +971-xxx-xxxx</li>
                                    <li><strong>{{ __('Help Center:') }}</strong> <a href="https://help.glowlabs.ae">help.glowlabs.ae</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="footer {{ $isRtl ? 'rtl-text' : '' }}">
                            <p class="footer-text">{{ __('Best regards,') }}<br>{{ __('The glowlabs Team') }}</p>
                            <p class="footer-text">&copy; {{ date('Y') }} glowlabs. {{ __('All rights reserved.') }}</p>
                            <p class="footer-text">{{ __('This email was sent to :email.', ['email' => $user->email]) }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
