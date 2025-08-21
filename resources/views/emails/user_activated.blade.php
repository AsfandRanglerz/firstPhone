<!DOCTYPE html>
<html>
<head>
    <title>Account Activated - First Phone</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">

        
        <!-- Logo and Header -->
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.png') }}" 
                 alt="{{ config('app.name') }} Logo" 
                 style="height: 80px; margin-bottom: 15px;">
            <h2>Your Account is Activated</h2>
        </div>

        <!-- Greeting -->
        <p style="font-size: 15px; color: #333;">Dear {{ $name ?? 'User' }},</p>

        <!-- Message -->
        <p style="font-size: 15px; color: #333; line-height: 1.6;">
            We’re excited to let you know that your account has been successfully <strong>activated</strong>.  
            You can now log in and start exploring all the features First Phone has to offer.
        </p>

        <!-- Account Details -->
        <h3>Your Account Details:</h3>
        <ul style="font-size: 15px; color: #333; line-height: 1.6;">
            <li><strong>Email:</strong> {{ $email ?? 'N/A' }}</li>
        </ul>

        <!-- Footer -->
        <p style="font-size: 14px; color: #666; line-height: 1.6; text-align: center;">
            If you have any questions or need assistance, feel free to contact our support team anytime. 
        </p>
        <p> 
            Thanks,<br>
            <strong>First Phone Team</strong>
        </p>

</body>
</html>
