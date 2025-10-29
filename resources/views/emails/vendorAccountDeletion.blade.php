<!DOCTYPE html>
<html>
<head>
    <title>Account Deleted - First Phone</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <div style="text-align:center; margin-bottom: 20px;">
        <img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.png') }}" 
             alt="First Phone Logo" 
             style="height: 100px; margin-bottom: 20px;">
        <h3><strong>Vendor Account Deleted</strong></h3>
    </div>

    <p>Dear {{ $user->name ?? 'Vendor' }},</p>

    <p>Your <strong>First Phone</strong> account has been permanently deleted from our system.</p>

    <p>If this was done by mistake, please reach out to us at 
        <a href="mailto:support@firstphone.pk">support@firstphone.pk</a>.
    </p>

    <p>We appreciate your time and contribution to the First Phone platform.</p>

    <p><strong>First Phone Team</strong></p>
</body>
</html>
