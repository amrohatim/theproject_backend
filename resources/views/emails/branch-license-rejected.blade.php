<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch License Rejected</title>
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
            padding: 0 0 36px;
        }

        .greeting {
            margin: 0 0 12px;
            font-size: 24px;
            line-height: 32px;
            color: #4f5a68;
            font-weight: 400;
        }

        .details-card {
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .details-title {
            margin: 0 0 12px;
            font-size: 16px;
            line-height: 24px;
            font-weight: 700;
            color: #4f5a68;
        }

        .detail-row {
            padding: 7px 0;
            border-bottom: 1px solid #ececf1;
            font-size: 14px;
            line-height: 20px;
        }

        .detail-row:last-child {
            border-bottom: 0;
        }

        .detail-label {
            font-weight: 700;
            color: #4f5a68;
            width: 42%;
            vertical-align: top;
        }

        .detail-value {
            color: #4f5a68;
            width: 58%;
            word-break: break-word;
        }

        .info-card {
            background-color: #ffffff;
            border: 1px solid #d4d5d6;
            border-radius: 8px;
            padding: 14px 16px;
            margin: 0 0 24px;
        }

        .info-card-title {
            margin: 0 0 8px;
            font-size: 14px;
            line-height: 20px;
            font-weight: 700;
            color: #4f5a68;
        }

        .info-card-text {
            margin: 0;
            font-size: 14px;
            line-height: 20px;
            color: #4f5a68;
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

        .important-text {
            margin: 0 0 12px;
            font-size: 14px;
            line-height: 20px;
            color: #4f5a68;
            font-weight: 700;
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

            .cta-button {
                display: block;
                width: 100%;
                min-width: 0;
                box-sizing: border-box;
            }

            .detail-label,
            .detail-value {
                display: block;
                width: 100%;
            }

            .detail-label {
                margin-bottom: 2px;
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
                            <p class="greeting">Hi {{ $vendor->name }},</p>
                            <h1 class="headline">Branch License Rejected.</h1>
                        </td>
                    </tr>

                    <tr>
                        <td class="message-block">
                            <p class="subtext">We reviewed your branch license application, but it does not meet approval requirements yet. Please review the details below and resubmit after making the needed updates.</p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="details-card">
                                <p class="details-title">Branch Details</p>
                                <table role="presentation" width="100%">
                                    <tr class="detail-row">
                                        <td class="detail-label">Branch Name:</td>
                                        <td class="detail-value">{{ $branch->name }}</td>
                                    </tr>
                                    <tr class="detail-row">
                                        <td class="detail-label">Address:</td>
                                        <td class="detail-value">{{ $branch->address }}</td>
                                    </tr>
                                    @if($branch->emirate)
                                    <tr class="detail-row">
                                        <td class="detail-label">Emirate:</td>
                                        <td class="detail-value">{{ $branch->emirate }}</td>
                                    </tr>
                                    @endif
                                    <tr class="detail-row">
                                        <td class="detail-label">Business Type:</td>
                                        <td class="detail-value">{{ $branch->business_type }}</td>
                                    </tr>
                                    <tr class="detail-row">
                                        <td class="detail-label">Submitted Date:</td>
                                        <td class="detail-value">{{ $license->uploaded_at->format('d-m-Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="info-card">
                                <p class="info-card-title">Reason for Rejection</p>
                                <p class="info-card-text">{{ $rejectionReason }}</p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="info-card">
                                <p class="info-card-title">Next Steps</p>
                                <ol class="info-list">
                                    <li>Review the rejection reason carefully.</li>
                                    <li>Address the issues mentioned in the feedback.</li>
                                    <li>Update your branch information and license document.</li>
                                    <li>Resubmit your application for review.</li>
                                </ol>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="info-card">
                                <p class="important-text">Important requirements before resubmission:</p>
                                <ul class="info-list">
                                    <li>Valid and current business license.</li>
                                    <li>Clear and readable document (PDF format preferred).</li>
                                    <li>License dates must be current and not expired.</li>
                                    <li>Business information must match your branch details.</li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="cta-wrap">
                            <a href="{{ $resubmissionUrl }}" class="cta-button">Update &amp; Resubmit License</a>
                        </td>
                    </tr>

                    <tr>
                        <td class="footer">
                            <p class="footer-text">If you did not sign up for this account you can ignore this email and the account will be deleted.</p>
                            <p class="footer-text">&copy; {{ date('Y') }} Glowlabs Company. All rights reserved. You received this email because you create a branch in our platform.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
