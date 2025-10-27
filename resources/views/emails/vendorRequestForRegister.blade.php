<!DOCTYPE html>
<html>
<head>
    <title>Vendor Registration Request - First Phone</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <div style="text-align:center; margin-bottom: 20px;">
        <img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.png') }}" 
             alt="First Phone Logo" 
             style="height: 100px; margin-bottom: 20px;">
        <h3><strong>Vendor Registration Request Received</strong></h3>
    </div>

    <p>Dear {{ $vendor->name ?? 'Vendor' }},</p>

    <p>Thank you for showing interest in joining <strong>First Phone</strong> as a vendor.</p>

    <p>Your registration request has been received and is currently under review by our admin team.</p>

    <p>Once your account is approved, you will receive another email with further instructions.</p>

    <p>We appreciate your patience and look forward to working with you.</p>

    <p>Thanks,<br><strong>First Phone Team</strong></p>
</body>
</html>
