<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration {{ ucfirst($decision) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e9ecef;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            margin: 10px 0;
        }
        .approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .declined {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .admin-message {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .button:hover {
            background: #0056b3;
        }
        .contact-info {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Marketplace') }}</h1>
        <h2>{{ $userType }} Registration Update</h2>
    </div>

    <div class="content">
        <p>Dear {{ $user->name }},</p>

        @if($decision === 'approved')
            <div class="status-badge approved">✓ APPROVED</div>
            
            <p>Congratulations! We're excited to inform you that your {{ strtolower($userType) }} registration has been <strong>approved</strong>.</p>
            
            <div class="info-box">
                <h3>🎉 Welcome to Our Marketplace!</h3>
                <p>Your account is now active and you have full access to all {{ strtolower($userType) }} features. You can now:</p>
                <ul>
                    @if($registration->user_type === 'vendor')
                        <li>Add and manage your products</li>
                        <li>Process customer orders</li>
                        <li>Access your vendor dashboard</li>
                        <li>Manage your company profile</li>
                        <li>View sales analytics and reports</li>
                    @else
                        <li>Add and manage your services</li>
                        <li>Manage your stock locations</li>
                        <li>Access your provider dashboard</li>
                        <li>Configure delivery settings</li>
                        <li>View performance analytics</li>
                    @endif
                </ul>
            </div>

            @if($adminMessage)
                <div class="admin-message">
                    <h4>📝 Message from our team:</h4>
                    <p>{{ $adminMessage }}</p>
                </div>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/login" class="button">Access Your Dashboard</a>
            </div>

        @else
            <div class="status-badge declined">✗ DECLINED</div>
            
            <p>Thank you for your interest in joining our marketplace. After careful review, we regret to inform you that your {{ strtolower($userType) }} registration has been <strong>declined</strong>.</p>

            @if($adminMessage)
                <div class="admin-message">
                    <h4>📝 Reason for decline:</h4>
                    <p>{{ $adminMessage }}</p>
                </div>
            @endif

            <div class="contact-info">
                <h4>📞 Need Help or Want to Appeal?</h4>
                <p>If you believe this decision was made in error or if you have questions about the requirements, please don't hesitate to contact us:</p>
                <ul>
                    <li><strong>Email:</strong> support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}</li>
                    <li><strong>Phone:</strong> +971-XXX-XXXX</li>
                    <li><strong>Business Hours:</strong> Sunday - Thursday, 9:00 AM - 6:00 PM (UAE Time)</li>
                </ul>
                <p>We're here to help you understand our requirements and guide you through the process.</p>
            </div>

            <div class="info-box">
                <h4>🔄 Reapplication Process</h4>
                <p>You may reapply for {{ strtolower($userType) }} registration after addressing the concerns mentioned above. Please ensure all requirements are met before submitting a new application.</p>
            </div>
        @endif

        <div class="info-box">
            <h4>📋 Registration Details</h4>
            <ul>
                <li><strong>Application Type:</strong> {{ $userType }} Registration</li>
                <li><strong>Submitted:</strong> {{ $registration->created_at->format('F j, Y \a\t g:i A') }}</li>
                <li><strong>Reviewed:</strong> {{ $registration->reviewed_at->format('F j, Y \a\t g:i A') }}</li>
                <li><strong>Reviewed by:</strong> {{ $reviewedBy }}</li>
                <li><strong>Status:</strong> {{ ucfirst($decision) }}</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated message from {{ config('app.name', 'Marketplace') }}.</p>
        <p>If you have any questions, please contact our support team.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Marketplace') }}. All rights reserved.</p>
    </div>
</body>
</html>
