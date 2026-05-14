<?php session_start(); ?>

<DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>
    <link rel="stylesheet" href="../../css/pages/verifyOTP.css">
</head>
<body>
<div class="container">
<form class="frm"action="verify_process.php" method="POST">
    <h3>Enter OTP</h3>
    <input class="otp-input" type="text" name="otp" required maxlength="6"> </br>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_otp'): ?>
    <p style="color: red; font-size: 12px;">Invalid or expired OTP. Please try again.</p>
<?php endif; ?>

    <button type="submit" class="btn">Verify</button>
</form>
</div>
</body>
</html>