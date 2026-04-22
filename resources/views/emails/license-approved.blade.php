@php
    $isRtl = app()->getLocale() === 'ar';

    // Get message content from the template file
    $templatePath = resource_path('views/messages_when_approval.md');
    $content = file_get_contents($templatePath);

    // Parse the content based on license type and language
    $language = $isRtl ? 'AR' : 'EN';
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
        $messageBody = "Hello {$user->name},\n\nWe are thrilled to inform you that your glowlabs {$licenseType} registration has been approved! You are now officially part of the glowlabs marketplace.\n\nYou can access your dashboard and manage your account using your email and password at: https://glowlabs.ae/login";
    }
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Approved - glowlabs</title>
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

        .cta-wrap {
            padding: 0 0 56px;
            text-align: center;
        }

        .cta-button {
            display: inline-block;
            background-color: #a46bc1;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 16px;
            line-height: 24px;
            font-weight: 700;
            border-radius: 8px;
            padding: 12px 48px;
            box-shadow: 0 3px 9px rgba(0, 0, 0, 0.09);
            min-width: 283px;
            text-align: center;
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

            .cta-button {
                display: block;
                width: 100%;
                min-width: 0;
                box-sizing: border-box;
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
                            <h1 class="headline">{{ __('Congratulations!') }}</h1>
                        </td>
                    </tr>

                    <tr>
                        <td class="message-block {{ $isRtl ? 'rtl-text' : '' }}">
                            <p class="subtext">{{ __('Your license has been approved') }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="info-card {{ $isRtl ? 'rtl-text' : '' }}">
                                <p class="info-text">{{ __('Welcome to the glowlabs :type Community!', ['type' => ucfirst($licenseType)]) }}</p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="info-card {{ $isRtl ? 'rtl-text' : '' }}">
                                <p class="info-title">{{ __('Approval Details') }}</p>
                                <p class="info-text">{!! nl2br(e($messageBody)) !!}</p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="cta-wrap">
                            <a href="https://glowlabs.ae/login" class="cta-button">{{ __('Access Your Dashboard') }}</a>
                        </td>
                    </tr>

                    @if($adminMessage)
                    <tr>
                        <td>
                            <div class="info-card {{ $isRtl ? 'rtl-text' : '' }}">
                                <p class="info-title">{{ __('Message from Admin:') }}</p>
                                <p class="info-text">{{ $adminMessage }}</p>
                            </div>
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td>
                            <div class="info-card {{ $isRtl ? 'rtl-text' : '' }}">
                                <p class="info-title">{{ __('Support Information') }}</p>
                                <p class="info-text" style="margin-bottom: 12px;">{{ __('If you have any questions or need assistance, our support team is here to help:') }}</p>
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
