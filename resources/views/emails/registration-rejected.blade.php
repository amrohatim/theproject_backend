<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Update - Dala3Chic</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #ef4444;
            font-size: 18px;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .reason-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .reason-title {
            color: #dc2626;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .next-steps {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .support-box {
            background-color: #f7fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <i class="fas fa-exclamation"></i>
            </div>
            <h1>Registration Update</h1>
            <p class="subtitle">We need to discuss your application</p>
        </div>

        <!-- Greeting -->
        <p>Hello {{ $user->name }},</p>

        <p>Thank you for your interest in joining Dala3Chic as a
            @if($userType === 'vendor')
                vendor
            @elseif($userType === 'provider')
                service provider
            @endif
            . We have carefully reviewed your registration application.
        </p>

        <p>Unfortunately, we are unable to approve your registration at this time.</p>

        <!-- Reason (if provided) -->
        @if($reason)
            <div class="reason-box">
                <div class="reason-title">Reason for this decision:</div>
                <p style="margin: 0;">{{ $reason }}</p>
            </div>
        @endif

        <!-- What This Means -->
        <h3 style="color: #2d3748;">What This Means</h3>
        <p>This decision doesn't necessarily mean you cannot join Dala3Chic in the future. We encourage you to:</p>

        <ul>
            <li>Review our
                @if($userType === 'vendor')
                    vendor requirements and guidelines
                @else
                    service provider requirements and guidelines
                @endif
            </li>
            <li>Address any concerns mentioned above</li>
            <li>Consider reapplying once you've made the necessary improvements</li>
        </ul>

        <!-- Next Steps -->
        <div class="next-steps">
            <h3 style="margin-top: 0; color: #2d3748;">Next Steps</h3>
            <ol style="margin: 0; padding-left: 20px;">
                <li>Contact our support team if you need clarification on this decision</li>
                <li>Review our requirements and make necessary improvements</li>
                <li>You may reapply after addressing the concerns</li>
                <li>Consider alternative ways to work with Dala3Chic</li>
            </ol>
        </div>

        <!-- Support Information -->
        <div class="support-box">
            <h3 style="margin-top: 0; color: #2d3748;">Need Help or Have Questions?</h3>
            <p>Our support team is here to help you understand this decision and guide you on potential next steps:</p>

            <ul style="margin-bottom: 0;">
                <li><strong>Email:</strong> support@dala3chic.com</li>
                <li><strong>Phone:</strong> +971-xxx-xxxx</li>
                <li><strong>Help Center:</strong> <a href="https://help.dala3chic.com">help.dala3chic.com</a></li>
            </ul>
        </div>

        <!-- Contact Support Button -->
        <div style="text-align: center;">
            <a href="mailto:support@dala3chic.com?subject=Registration Decision Inquiry - {{ $user->name }}"
               class="button">
                Contact Support Team
            </a>
        </div>

        <!-- Alternative Options -->
        <h3 style="color: #2d3748;">Alternative Ways to Engage</h3>
        <p>While we cannot approve your
            @if($userType === 'vendor')
                vendor
            @else
                service provider
            @endif
            registration at this time, you can still:</p>

        <ul>
            <li>Browse and shop on our marketplace as a customer</li>
            <li>Follow us on social media for updates and opportunities</li>
            <li>Subscribe to our newsletter for marketplace insights</li>
            @if($userType === 'vendor')
                <li>Consider applying as a service provider if you offer services</li>
            @else
                <li>Consider applying as a vendor if you sell products</li>
            @endif
        </ul>

        <p>We appreciate your interest in Dala3Chic and hope to potentially work together in the future.</p>

        <p>Best regards,<br>
        The Dala3Chic Team</p>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} Dala3Chic. All rights reserved.</p>
            <p>This email was sent to {{ $user->email }}.</p>
        </div>
    </div>
</body>
</html>