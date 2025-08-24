<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Approved - Dala3Chic</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            color: #10b981;
            font-size: 18px;
            margin-bottom: 30px;
            font-weight: bold;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .provider-banner {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        }
        .vendor-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .provider-button {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .features {
            background-color: #f7fafc;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        .vendor-features {
            border-left-color: #667eea;
        }
        .provider-features {
            border-left-color: #f093fb;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .next-steps {
            background-color: #edf2f7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .step {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .step-number {
            background: #10b981;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-right: 15px;
        }
        .vendor-step {
            background: #667eea;
        }
        .provider-step {
            background: #f093fb;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo {{ $userType === 'provider' ? 'provider-logo' : 'vendor-logo' }}">
                <i class="fas fa-check"></i>
            </div>
            <h1>Congratulations!</h1>
            <p class="subtitle">Your registration has been approved</p>
        </div>

        <!-- Celebration Banner -->
        <div class="celebration-banner {{ $userType === 'provider' ? 'provider-banner' : 'vendor-banner' }}">
            🎉 Welcome to the Dala3Chic {{ ucfirst($userType) }} Community! 🎉
        </div>

        <!-- Greeting -->
        <p>Hello {{ $user->name }},</p>

        <p>We're excited to inform you that your
            @if($userType === 'vendor')
                vendor registration
            @elseif($userType === 'provider')
                service provider registration
            @endif
            has been <strong>approved</strong>! You are now officially part of the Dala3Chic marketplace.
        </p>

        <!-- What's Next -->
        <div class="next-steps">
            <h3 style="margin-top: 0; color: #2d3748;">What's Next?</h3>

            @if($userType === 'vendor')
                <div class="step">
                    <div class="step-number vendor-step">1</div>
                    <span>Access your vendor dashboard to set up your store</span>
                </div>
                <div class="step">
                    <div class="step-number vendor-step">2</div>
                    <span>Add your first products and services</span>
                </div>
                <div class="step">
                    <div class="step-number vendor-step">3</div>
                    <span>Configure your delivery and payment options</span>
                </div>
                <div class="step">
                    <div class="step-number vendor-step">4</div>
                    <span>Start receiving orders from customers</span>
                </div>
            @else
                <div class="step">
                    <div class="step-number provider-step">1</div>
                    <span>Access your provider dashboard to set up your services</span>
                </div>
                <div class="step">
                    <div class="step-number provider-step">2</div>
                    <span>Create detailed service listings</span>
                </div>
                <div class="step">
                    <div class="step-number provider-step">3</div>
                    <span>Set your availability and pricing</span>
                </div>
                <div class="step">
                    <div class="step-number provider-step">4</div>
                    <span>Start connecting with customers who need your services</span>
                </div>
            @endif
        </div>

        <!-- Dashboard Button -->
        <div style="text-align: center;">
            <a href="{{ $dashboardUrl }}"
               class="button {{ $userType === 'provider' ? 'provider-button' : 'vendor-button' }}">
                Access Your Dashboard
            </a>
        </div>

        <!-- Features -->
        <div class="features {{ $userType === 'provider' ? 'provider-features' : 'vendor-features' }}">
            <h3 style="margin-top: 0; color: #2d3748;">What You Get Access To:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                @if($userType === 'vendor')
                    <li>Complete store management system</li>
                    <li>Product catalog and inventory management</li>
                    <li>Order processing and fulfillment tools</li>
                    <li>Customer communication platform</li>
                    <li>Sales analytics and reporting</li>
                    <li>Marketing and promotional tools</li>
                @else
                    <li>Service listing and portfolio management</li>
                    <li>Booking and appointment system</li>
                    <li>Client communication tools</li>
                    <li>Service delivery tracking</li>
                    <li>Performance analytics</li>
                    <li>Professional networking opportunities</li>
                @endif
            </ul>
        </div>

        <!-- Support Information -->
        <p>If you have any questions or need assistance getting started, our support team is here to help:</p>

        <ul>
            <li><strong>Email:</strong> support@dala3chic.com</li>
            <li><strong>Phone:</strong> +971-xxx-xxxx</li>
            <li><strong>Help Center:</strong> <a href="https://help.dala3chic.com">help.dala3chic.com</a></li>
        </ul>

        <p>We're thrilled to have you as part of our growing marketplace community. Together, we'll create amazing experiences for customers across the UAE.</p>

        <p>Welcome aboard!</p>

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