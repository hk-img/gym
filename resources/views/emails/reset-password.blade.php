<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px;">
        <h2>Hello,</h2>
        <p>{{ $messageText }}</p>

        <a href="{{ $resetUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">
            Reset Password
        </a>

        <p style="margin-top: 20px;">If you didn’t request this, you can ignore this email.</p>
        <br>
        <p>— GYM</p>
    </div>
</body>
</html>