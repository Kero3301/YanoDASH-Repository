<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <p>Enter your email to receive a one-time code for password reset.</p>

    <form action="send_otp.php" method="POST">
        <label for="email">Email</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <button type="submit">Send OTP</button>
    </form>

    <p>If you are testing, the OTP will be shown on-screen.</p>
</body>
</html>