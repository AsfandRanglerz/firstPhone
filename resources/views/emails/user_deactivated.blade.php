<!DOCTYPE html>
<html>

<head>
    <title>Account Deactivated - First Phone</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    
        <!-- Logo and Header -->
        <div style="text-align:center; margin-bottom: 20px;">
            <img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.png') }}"
                alt="{{ config('app.name') }} Logo"
                style="height: 80px; margin-bottom: 15px;">
            <h2>Account Deactivation Notice</h2>
        </div>

        <!-- Body Content -->
        <p style="font-size: 14px; color: #333;">Dear {{ $name ?? 'User' }},</p>

        <p style="font-size: 14px; color: #333;">
            We regret to inform you that your account has been <strong>deactivated</strong> by the administrator.
        </p>

        @if (!empty($reason))
            <p style="font-size: 14px; color: #333;">
                <strong>Reason:</strong> {{ $reason }}
            </p>
        @endif

        <p>
            If you believe this is a mistake or would like further clarification, please contact our support team.
        </p>

        <!-- Footer -->
        <p>
            Thanks,<br>
            <strong>First Phone Team</strong>
        </p>
</body>

</html>
