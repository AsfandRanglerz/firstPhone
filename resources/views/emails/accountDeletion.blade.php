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
        <h3><strong>Account Deleted</strong></h3>
    </div>

    <p>Dear {{ $vendor->name ?? 'Customer' }},</p>

    <p>We would like to inform you that your <strong>First Phone</strong> vendor account has been permanently deleted from our system.</p>

    <p>If this action was unintentional or you believe this was a mistake, please contact our support team at 
    <a href="mailto:support@firstphone.pk">support@firstphone.pk</a> within 7 days for assistance.</p>

    <p>We appreciate the time and effort you invested with us and wish you the best in your future endeavors.</p>

    <p>Thanks,<br><strong>First Phone Team</strong></p>
</body>
</html>
