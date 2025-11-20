<!DOCTYPE html>
<html>
<head>
    <title>Vendor Registration Request - First Phone</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <div style="text-align:center; margin-bottom: 20px;">
<img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.jpg') }}" 
     alt="First Phone Logo"
     style="max-width: 320px; width: 100%; height: auto; display: block; margin: 0 auto 20px;">

        <h3><strong>Vendor Registration Request Received</strong></h3>
    </div>

        <p>Hi <strong>{{ $vendor->name ?? 'Vendor' }}</strong>,</p>

        <p>We’ve received your registration request and our team is currently reviewing your details.</p>

        <p>You’ll receive another email once your account has been approved and activated.</p>

        <div style="background-color:#f3f6ff; border-left:4px solid #021642; padding:12px 15px; margin:20px 0; border-radius:6px;">
            <p style="margin:0; font-size:14px; color:#333;">
                Please keep an eye on your inbox for updates from the First Phone team.
            </p>
        </div>

        <p>We appreciate your patience and look forward to partnering with you.</p>

        <p style="margin-top:25px;"><strong>First Phone Team</strong></p>
</body>
</html>
