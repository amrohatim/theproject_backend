<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch License Rejected</title>
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
            border-bottom: 2px solid #dc3545;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 10px;
        }
        .warning-icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 15px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
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
            border-left: 4px solid #dc3545;
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
        .rejection-reason {
            background-color: #f8d7da;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .cta-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #0056b3;
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
        .requirements-list {
            background-color: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">Dala3Chic</div>
            <div class="warning-icon">⚠️</div>
            <div class="title">Branch License Rejected</div>
        </div>

        <div class="content">
            <p>Dear {{ $vendor->name }},</p>
            
            <p>We regret to inform you that your branch license application has been <strong>rejected</strong> after our review process.</p>

            <div class="branch-details">
                <h3 style="margin-top: 0; color: #dc3545;">Branch Details</h3>
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
                    <span class="detail-label">Submitted Date:</span>
                    <span class="detail-value">{{ $license->uploaded_at->format('d-m-Y H:i') }}</span>
                </div>
            </div>

            <div class="rejection-reason">
                <h4 style="margin-top: 0; color: #dc3545;">Reason for Rejection:</h4>
                <p style="margin-bottom: 0; font-weight: 500;">{{ $rejectionReason }}</p>
            </div>

            <div class="requirements-list">
                <h4 style="margin-top: 0; color: #007bff;">Next Steps:</h4>
                <ol style="margin-bottom: 0;">
                    <li>Review the rejection reason carefully</li>
                    <li>Address the issues mentioned in the feedback</li>
                    <li>Update your branch information and license document</li>
                    <li>Resubmit your application for review</li>
                </ol>
            </div>

            <p><strong>Important:</strong> Please ensure that your license document meets all our requirements before resubmitting:</p>
            <ul>
                <li>Valid and current business license</li>
                <li>Clear and readable document (PDF format preferred)</li>
                <li>License dates must be current and not expired</li>
                <li>Business information must match your branch details</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ $resubmissionUrl }}" class="cta-button">Update & Resubmit License</a>
            </div>

            <div class="contact-info">
                <h4 style="margin-top: 0;">Need Help?</h4>
                <p style="margin-bottom: 0;">If you have any questions about the rejection or need assistance with your resubmission, please contact our support team. We're here to help you get approved!</p>
            </div>
        </div>

        <div class="footer">
            <p>We appreciate your interest in joining Dala3Chic!</p>
            <p><strong>The Dala3Chic Team</strong></p>
            <p style="font-size: 12px; color: #999;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
