<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
     <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.png') }}" 
                 alt="{{ config('app.name') }} Logo" 
                 style="height: 80px; margin-bottom: 15px;">
        </div>
    <h2>Password Reset OTP</h2>

    <p>
        Use the OTP below to reset your password:
    </p>

    <div style="text-align: center; margin: 20px 0;">
        <span style="font-size: 24px; font-weight: bold; color: #021642;">{{ $otp }}</span>
    </div>

    <p>
        If you did not request this OTP, please ignore this email.
    </p>

    <p>
        Thanks,<br>
        <strong>First Phone Team</strong>
    </p>
</body>
</html>
