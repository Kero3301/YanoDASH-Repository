<?php session_start(); ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_otp'): ?>
    <p style="color: red;">Invalid or expired OTP. Please try again.</p>
<?php endif; ?>

<form action="verify_process.php" method="POST">
    <h3>Enter OTP</h3>
    <input type="text" name="otp" required maxlength="6">
    <button type="submit">Verify</button>
</form>