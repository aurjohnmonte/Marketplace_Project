<!-- resources/views/emails/otp.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
</head>
<body>
    <h2>Hello!</h2>

    <p>Thank you for registering at <strong>Craftify</strong>.</p>

    <p>Here is your one-time password (OTP) to continue your process:</p>

    <h1 style="color: #2c3e50;">{{ $otp }}</h1>

    <p>Please enter this code in the verification screen. This code will expire shortly for your security.</p>

    <p>Best regards,<br>
    CRAFTIFY </p>
</body>
</html>
