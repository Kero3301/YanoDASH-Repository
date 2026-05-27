<?php
session_start();

require_once '../../../bootstrap/app.php';

load(
    'vendor_autoload',
    'mongodb_client',
    'mongodb_collections'
);

$client = mongodb_client();

$accountsColl = coll('accounts', $client);
$credentialsColl = coll('login_credentials', $client);

// =========================
// CHECK SESSION
// =========================
if (!isset($_SESSION['reset_email'])) {
    header('Location: verify.php?error=invalid_otp');
    exit();
}

$email = $_SESSION['reset_email'];

// =========================
// CHECK OTP INPUT
// =========================
if (!isset($_POST['otp'])) {
    header('Location: verify.php?error=invalid_otp');
    exit();
}

$inputOtp = trim($_POST['otp']);

// =========================
// FIND USER
// =========================
$user = $accountsColl
    ->findOne(['email_address' => $email])
    ->execute();   // ✅ already returns row/array

if (!$user) {
    die("User not found.");
}

$userID = $user['id']; // adjust to your schema

// =========================
// FIND CREDENTIALS
// =========================
$credentials = $credentialsColl
    ->findOne(['user' => $userID])
    ->execute();   // ✅ already returns row/array

if (!$credentials) {
    die("Credentials not found.");
}

// =========================
// CHECK OTP DATA
// =========================
if (
    !isset($credentials['otp_code']) ||
    !isset($credentials['otp_expiry'])
) {
    header('Location: verify.php?error=invalid_otp');
    exit();
}

$storedOTP = (string)$credentials['otp_code'];   // ✅ flat column
$storedExpiry = (int)$credentials['otp_expiry'];
$currentTime = time();

// =========================
// VERIFY OTP
// =========================
if (
    $inputOtp === $storedOTP &&
    $currentTime <= $storedExpiry
) {
    // Remove OTP after successful verification
    $credentialsColl->updateOne(
        ['user' => $userID],
        [
            '$unset' => [
                'otp_code' => "",
                'otp_expiry' => ""
            ]
        ]
    );

    $_SESSION['verified_for_reset'] = true;
    header('Location: reset_password.php');
    exit();
}

// =========================
// EXPIRED
// =========================
if ($currentTime > $storedExpiry) {
    header('Location: verify.php?error=expired');
    exit();
}

// =========================
// INVALID
// =========================
header('Location: verify.php?error=invalid_otp');
exit();
