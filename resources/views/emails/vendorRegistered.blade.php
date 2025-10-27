<!DOCTYPE html>
<html>
<head>
    <title>Vendor Account Approved - First Phone</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <div style="text-align:center; margin-bottom: 20px;">
        <img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.png') }}" 
             alt="First Phone Logo" 
             style="height: 100px; margin-bottom: 20px;">
        <h3><strong>Welcome to <span style="color: #021642;">First Phone</span></strong></h3>
    </div>

    <p>Dear {{ $vendor->name ?? 'Vendor' }},</p>

    <p>Congratulations! Your vendor account has been <strong>approved</strong>.</p>

    <p>You can now log in to your vendor dashboard and start adding mobiles, managing listings, and viewing orders.</p>

    <h3>Your Login Details:</h3>
    <ul>
        <li><strong>Email:</strong> {{ $vendor->email ?? 'N/A' }}</li>
        <li><strong>Phone:</strong> {{ $vendor->phone ?? 'N/A' }}</li>
    </ul>

    <p>If you face any issues, feel free to contact our support team.</p>

    <p>Thanks,<br><strong>First Phone Team</strong></p>
</body>
</html>
