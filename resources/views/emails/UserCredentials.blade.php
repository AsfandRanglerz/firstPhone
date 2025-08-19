<!DOCTYPE html>
<html>
<head>
    <title>Welcome to First Phone</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <div style="text-align:center; margin-bottom: 20px;">
        <img src="{{ asset('public/admin/assets/images/FirstPhone-Logo.png') }}" 
             alt="{{ config('app.name') }} Logo" 
             style="height: 100px; margin-bottom: 20px;">
        <h3><strong>Welcome to <span style="color: #021642;">First Phone</span></strong></h3>
    </div>

    <p>Dear {{ $name ?? 'User' }},</p>

    <p>Your account has been successfully created.</p>

    <h3>Your Account Details:</h3>
    <ul>
        <li><strong>Email:</strong> {{ $email ?? 'N/A' }}</li>
        <li><strong>Phone:</strong> {{ $phone ?? 'N/A' }}</li>


    <p>Please keep this information safe and secure. Do not share your login credentials with anyone.</p>

    <p>If you have any questions or need assistance, feel free to contact our support team anytime.</p>

    <p>Thanks,<br><strong>First Phone</strong></p>
</body>
</html>
