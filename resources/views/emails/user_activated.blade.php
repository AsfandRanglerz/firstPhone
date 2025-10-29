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
            <h2>Account Activation</h2>
        </div>

         <p style="font-size: 15px; color: #333;">Hi <strong>{{ $name ?? 'User' }}</strong>,</p>

        <!-- Message -->
        <p style="font-size: 15px; color: #333; line-height: 1.6;">
            Great news! Your account has been successfully <strong>activated</strong>.  
            You can now log in and enjoy all the features First Phone has to offer — from browsing listings to managing your account easily.
        </p>

        <!-- Support -->
        <p style="font-size: 14px; color: #555; line-height: 1.6;">
            Need help? Our team is always ready to assist you.  
            Contact us anytime at 
            <a href="mailto:support@firstphone.pk" style="color: #021642; text-decoration: none; font-weight: bold;">support@firstphone.pk</a>.
        </p>

        <!-- Footer -->
        <p style="font-size: 14px; color: #666; margin-top: 30px;">
            Best regards,<br>
            <strong style="color: #021642;">The First Phone Team</strong>
        </p>

</body>
</html>
