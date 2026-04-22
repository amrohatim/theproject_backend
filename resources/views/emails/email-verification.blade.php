<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - glowlabs</title>
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

        .message-block {
            padding: 0 0 32px;
        }

        .greeting {
            margin: 0 0 12px;
            font-size: 24px;
            line-height: 32px;
            color: #4f5a68;
            font-weight: 400;
        }

        .info-card {
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
        }

        .code-box {
            margin: 0;
            background-color: #a46bc1;
            border-radius: 8px;
            text-align: center;
            padding: 16px 12px;
            font-size: 32px;
            line-height: 36px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #4f5a68;
            font-family: "Courier New", Courier, monospace;
        }

        .info-list {
            margin: 0;
            padding: 0 0 0 18px;
        }

        .info-list li {
            margin: 0 0 8px;
            font-size: 14px;
            line-height: 20px;
            color: #4f5a68;
        }

        .info-list li:last-child {
            margin-bottom: 0;
        }

        .cta-wrap {
            padding: 0 0 56px;
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

            .code-box {
                font-size: 26px;
                line-height: 30px;
                letter-spacing: 5px;
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
                        <td style="padding-bottom: 48px;">
                            <p class="greeting">Hi {{ $user->name }},</p>
                            <h1 class="headline">Email Verification.</h1>
                        </td>
                    </tr>

                    <tr>
                        <td class="message-block">
                            <p class="subtext">
                                @if($userType === 'vendor')
                                    Welcome to glowlabs Vendor Platform.
                                @elseif($userType === 'provider')
                                    Welcome to glowlabs Provider Platform.
                                @elseif($userType === 'merchant')
                                    Welcome to glowlabs Merchant Platform.
                                @else
                                    Welcome to glowlabs.
                                @endif
                                Please verify your email address to complete registration.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="info-card">
                                <p class="info-title">Your Verification Code</p>
                                <p class="code-box">{{ $verificationCode }}</p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="info-card">
                                <p class="info-title">How to verify your email</p>
                                <ol class="info-list">
                                    <li>Copy the verification code above.</li>
                                    <li>Return to the registration page.</li>
                                    <li>Enter the code in the verification field.</li>
                                    <li>Click "Verify Email" to complete your registration.</li>
                                </ol>
                            </div>
                        </td>
                    </tr>

                    @if($user->id > 0)
                    <tr>
                        <td>
                            <div class="info-card">
                                <p class="info-text">Or click the button below to verify automatically:</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="cta-wrap">
                            <a href="{{ route(match($userType) {
                                'vendor' => 'vendor.email.verify',
                                'provider' => 'provider.email.verify',
                                'merchant' => 'merchant.email.verify',
                                default => 'vendor.email.verify'
                            }, ['user_id' => $user->id, 'code' => $verificationCode]) }}" class="cta-button">Verify Email Address</a>
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td class="cta-wrap">
                            <div class="info-card" style="margin-bottom: 0;">
                                <p class="info-text">Please return to the registration page and enter the verification code above to continue.</p>
                            </div>
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td>
                            <div class="info-card">
                                <p class="info-title">Security Notice</p>
                                <p class="info-text">This verification code will expire in 24 hours. If you did not request this verification, please ignore this email or contact our support team.</p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="info-card">
                                <p class="info-text">
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
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="footer">
                            <p class="footer-text">If you have any questions or need assistance, please do not hesitate to contact our support team.</p>
                            <p class="footer-text">&copy; {{ date('Y') }} glowlabs. All rights reserved.</p>
                            <p class="footer-text">This email was sent to {{ $user->email }}. If you did not request this verification, please ignore this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
