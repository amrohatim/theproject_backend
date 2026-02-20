<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch License Approved</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #28a745;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 15px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 25px;
        }
        .branch-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .admin-message {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .cta-button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #218838;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">glowlabs</div>
            <div class="success-icon">✅</div>
            <div class="title">Branch License Approved!</div>
        </div>

        <div class="content">
            <p>Dear {{ $vendor->name }},</p>
            
            <p>Congratulations! We are pleased to inform you that your branch license has been <strong>approved</strong> and is now active.</p>

            <div class="branch-details">
                <h3 style="margin-top: 0; color: #28a745;">Branch Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Branch Name:</span>
                    <span class="detail-value">{{ $branch->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value">{{ $branch->address }}</span>
                </div>
                @if($branch->emirate)
                <div class="detail-row">
                    <span class="detail-label">Emirate:</span>
                    <span class="detail-value">{{ $branch->emirate }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Business Type:</span>
                    <span class="detail-value">{{ $branch->business_type }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">License Start Date:</span>
                    <span class="detail-value">{{ $license->start_date->format('d-m-Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">License End Date:</span>
                    <span class="detail-value">{{ $license->end_date->format('d-m-Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Approved Date:</span>
                    <span class="detail-value">{{ $license->verified_at->format('d-m-Y H:i') }}</span>
                </div>
            </div>

            @if($adminMessage)
            <div class="admin-message">
                <h4 style="margin-top: 0; color: #28a745;">Message from Admin:</h4>
                <p style="margin-bottom: 0;">{{ $adminMessage }}</p>
            </div>
            @endif

            <p>Your branch is now active and you can:</p>
            <ul>
                <li>Add products and services to this branch</li>
                <li>Manage branch information and settings</li>
                <li>Start receiving orders and bookings</li>
                <li>Access all vendor dashboard features</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ $dashboardUrl }}" class="cta-button">Access Your Dashboard</a>
            </div>

            <div class="contact-info">
                <h4 style="margin-top: 0;">Need Help?</h4>
                <p style="margin-bottom: 0;">If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing glowlabs!</p>
            <p><strong>The glowlabs Team</strong></p>
            <p style="font-size: 12px; color: #999;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
