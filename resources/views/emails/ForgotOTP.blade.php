<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
     <div style="text-align: center; margin-bottom: 20px;">
<img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.jpg') }}" 
     alt="First Phone Logo"
     style="max-width: 320px; width: 100%; height: auto; display: block; margin: 0 auto 20px;">

        </div>
    <h2 style="color: #021642; text-align: center;">Reset Your Password</h2>

   <p style="text-align: center;">
        We received a request to reset your password for your <strong>{{ config('app.name') }}</strong> account.
        Please use the OTP below to proceed with resetting your password:
    </p>

    <div style="text-align: center; margin: 20px 0;">
        <span style="font-size: 24px; font-weight: bold; color: #021642;">{{ $otp }}</span>
    </div>

    {{-- <p>
        If you did not request this OTP, please ignore this email.
    </p>

    <p>
        Thanks,<br>
        <strong>First Phone Team</strong>
    </p> --}}
</body>
</html>
