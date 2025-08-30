<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }
        .otp-code {
            background-color: #007bff;
            color: white;
            font-size: 32px;
            font-weight: bold;
            padding: 20px;
            border-radius: 8px;
            letter-spacing: 5px;
            margin: 20px 0;
            display: inline-block;
        }
        .info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            font-size: 12px;
            color: #666;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verification Code</h1>
        
        <p>You have requested a verification code. Please use the code below:</p>
        
        <div class="otp-code">{{ $code }}</div>
        
        <div class="info">
            <p><strong>Important:</strong></p>
            <p>• This code is valid for {{ isset($data['expires_at']) ? $data['expires_at']->diffForHumans(null, true) : '5 minutes' }}</p>
            <p>• Do not share this code with anyone</p>
            <p>• Enter this code exactly as shown</p>
        </div>
        
        @if(isset($data['type']) && $data['type'] !== 'default')
            <p><small>Verification type: {{ ucfirst(str_replace('_', ' ', $data['type'])) }}</small></p>
        @endif
        
        <div class="footer">
            <p>If you did not request this verification code, please ignore this email or contact support if you have concerns.</p>
        </div>
    </div>
</body>
</html>
