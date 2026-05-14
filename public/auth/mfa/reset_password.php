<?php
session_start();
require_once '../../../src/loader.php';
load (
    'vendor_autoload',
    'mongodb_client',
    'mongodb_collections'
);

if (!isset($_SESSION['verified_for_reset']) || !isset($_SESSION['reset_email'])) {
    header('Location: send_otp.php');
    exit;
}

$client = mongodb_client();
$collection = coll('accounts', $client);
$credentialsColl = coll('login_credentials', $client);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else { // Passwords match
        // Find account
        $account = $collection->findOne(['email_address' => $_SESSION['reset_email']]);
        // If account is found
        if ($account) {
            $userID = $account->_id;
            $hashed_password = password_hash($new_password, PASSWORD_ARGON2ID);
            $accountCredentials = $credentialsColl->findOne(['user' => $userID]);
            if (!$accountCredentials) {
                echo "Credentials not found.";
                exit;
            }
            
            $credentialsColl->updateOne(
                ['user' => $userID],
                ['$set' => ['password_hash' => $hashed_password]]
            );

            $reset_finished = true;
            $success_message = "Password reset successful! </br></br> <a href='../login.php'>Login here</a>";

            // Clear session
            unset($_SESSION['verified_for_reset']);
            unset($_SESSION['reset_email']);

            // echo "Password reset successful! <a href='../login.php'>Login here</a>";
            // exit;
        } 
        // else {
        //     exit;
        // }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../../css/pages/resetPass.css">
</head>
<body>
<div class="container">
    <?php if (isset($reset_finished) && $reset_finished): ?>
        <div class="success-box">
            <h3>Success!</h3>
            <p><?php echo $success_message; ?></p>
        </div>
    <?php else: ?>
        <form method="POST">
            <h3>Reset Password</h3>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="New Password" class="box" required>
                <span class="toggle-icon" onclick="toggleVisibility('password')">⊘</span>
            </div>

            <div class="password-wrapper">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="box" required>
                <span class="toggle-icon" onclick="toggleVisibility('confirm_password')">⊘</span>
            </div>
            <br>
            <button type="submit" class="btn">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
    <script>
    function toggleVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling;
        
        if (input.type === "password") {
            input.type = "text";
            icon.textContent = "⊚"; 
        } else {
            input.type = "password";
            icon.textContent = "⊘";
        }
    }
    </script>
</div>
</body>
</html>