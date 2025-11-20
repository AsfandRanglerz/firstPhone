<!DOCTYPE html>
<html>

<head>
    <title>Your OTP Code</title>
</head>

<body style="font-family: Arial, sans-serif;">
    <div
        style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div style="text-align: center;">
<img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.jpg') }}" 
     alt="First Phone Logo"
     style="max-width: 320px; width: 100%; height: auto; display: block; margin: 0 auto 20px;">

            <h2 style="color: #00b962; margin-bottom: 20px;">Your One Time Password (OTP)</h2>
        </div>

        <p>Hi {{ $name ?? 'User' }},</p>

        <p>Your One Time Password (OTP) is:</p>

        <div style="font-size: 28px; font-weight: bold; color: #333; text-align: center; margin: 20px 0;">
            {{ $otp}}
        </div>

        <p>This OTP is valid for the next 50 seconds. Please use it promptly to complete your verification.</p>

        <p>If you did not request this OTP, please ignore this email.</p>

        <p>Best regards,<br>The FirstPhone Team</p>
    </div>
</body>

</html>
