<?php
session_start();
require_once '../../../bootstrap/app.php';
load (
    'vendor_autoload',
    'mongodb_client',
    'mongodb_collections'
);

$client = mongodb_client();
$collection = coll('accounts', $client);
$credentialsColl = coll('login_credentials', $client);

$email = $_SESSION['reset_email'];
$inputOtp = $_POST['otp'];

$user = $collection->findOne(['email_address' => $email]);

if (!$user) {
    echo "User not found.";
    exit();
}

$userID = $user->_id;
$credentials = $credentialsColl->findOne(['user' => $userID]);
if (!$credentials) {
    echo "Credentials not found. Please reach out to an admin.";
    exit();
}

$storedOTP = $credentials->otp->code;
$storedExpiry = $credentials->otp->expiry;

// Check OTP
if ($storedOTP == $inputOtp && time() < $storedExpiry) {
    // Clear OTP
    $credentialsColl->updateOne(
        ['user' => $userID],
        ['$set' => ['otp' => ['code' => null, 'expiry' => null]]]
    );

    // Set session for password reset
    $_SESSION['verified_for_reset'] = true;

    // Redirect to reset password page
    header('Location: reset_password.php');
    exit;
} else {
    header('Location: verify.php?error=invalid_otp');
    exit;
}
?>