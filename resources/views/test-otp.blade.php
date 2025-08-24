<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Test</title>
    <style>
        .otp-input-container {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .otp-digit-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 600;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            background: white;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        
        .otp-digit-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 50px;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        p {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>OTP Test - 6 Digits</h1>
        <p>This should show 6 input fields for OTP verification</p>
        
        <div class="otp-input-container">
            <input type="text" class="otp-digit-input" maxlength="1" placeholder="0">
            <input type="text" class="otp-digit-input" maxlength="1" placeholder="0">
            <input type="text" class="otp-digit-input" maxlength="1" placeholder="0">
            <input type="text" class="otp-digit-input" maxlength="1" placeholder="0">
            <input type="text" class="otp-digit-input" maxlength="1" placeholder="0">
            <input type="text" class="otp-digit-input" maxlength="1" placeholder="0">
        </div>
        
        <p>All 6 digits should be visible and properly spaced.</p>
    </div>
</body>
</html>
