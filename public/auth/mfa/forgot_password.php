<?php 
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="../script/control-actions.js" defer></script>
    <link rel="stylesheet" href="../../css/pages/forgotpass.css">
</head>
<body>
    <div class="container">
    <h2>Forgot Password</h2>
    <p class="desc">Enter your email to receive a one-time code for password reset.</p>

    <form class="frm" action="send_otp.php" method="POST">
        <!-- <label for="email"> </label><br> -->
        <input type="email" id="email" name="email" class="box"required><br><br>
        <button type="submit" class="btn">Send OTP</button>
    </form>

    <p class="desc">If you are testing, the OTP will be shown on-screen.</p>
    </div>
</body>
</html>