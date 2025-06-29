<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Verification - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 16px;
        }

        .info-section {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .info-value {
            color: #6b7280;
            word-break: break-all;
            font-family: monospace;
            background: white;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }

        .btn {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
            margin: 10px 5px;
        }

        .btn:hover {
            background: #2563eb;
        }

        .btn-success {
            background: #10b981;
        }

        .btn-success:hover {
            background: #059669;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-info {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Verification Test</h1>
            <p class="subtitle">Development Testing Tool</p>
        </div>

        <div class="alert alert-info">
            <strong>Note:</strong> This page is only available in development mode. In production, users would receive an actual email with the verification link.
        </div>

        <div class="info-section">
            <div class="info-label">Email Address:</div>
            <div class="info-value">{{ $email }}</div>
        </div>

        <div class="info-section">
            <div class="info-label">Verification Type:</div>
            <div class="info-value">{{ $type }}</div>
        </div>

        <div class="info-section">
            <div class="info-label">Token:</div>
            <div class="info-value">{{ $token }}</div>
        </div>

        <div class="info-section">
            <div class="info-label">Expires At:</div>
            <div class="info-value">{{ $expires_at }}</div>
        </div>

        <div class="info-section">
            <div class="info-label">Verification URL:</div>
            <div class="info-value">{{ $verification_url }}</div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ $verification_url }}" class="btn btn-success">
                Click to Verify Email
            </a>
            <a href="/register/vendor/step2" class="btn">
                Back to Registration
            </a>
        </div>

        <div class="footer">
            <p>This is a development testing tool. In production, the verification link would be sent via email.</p>
        </div>
    </div>
</body>
</html>
