<?php
session_start();

if (!isset($_SESSION['reset_email'])) {
    die("Unauthorized access.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../../css/pages/verifyOTP.css">
</head>
<body>

<div class="container">

    <form class="frm" action="verify_process.php" method="POST">

        <h3>Enter OTP</h3>

        <label for="otp">6‑Digit Code</label>
        <input
            id="otp"
            class="otp-input"
            type="text"
            name="otp"
            required
            maxlength="6"
            pattern="\d{6}"
            inputmode="numeric"
            autocomplete="off"
        >

        <br>

        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'invalid_otp'): ?>
                <p style="color:red; font-size:12px;">Invalid OTP.</p>
            <?php elseif ($_GET['error'] === 'expired'): ?>
                <p style="color:red; font-size:12px;">OTP has expired.</p>
            <?php else: ?>
                <p style="color:red; font-size:12px;">An error occurred. Please try again.</p>
            <?php endif; ?>
        <?php endif; ?>

        <button type="submit" class="btn">Verify</button>

    </form>

</div>

</body>
</html>
