<?php
session_start();

// Force UTC time globally to perfectly match the generation file
date_default_timezone_set('UTC'); 

require_once '../../../bootstrap/app.php';

load(
    'vendor_autoload',
    'mongodb_client',
    'mongodb_collections'
);

// =========================
// 1. CHECK SESSION
// =========================
if (!isset($_SESSION['reset_email'])) {
    header('Location: verify.php?error=invalid_otp');
    exit();
}

$email = $_SESSION['reset_email'];

// =========================
// 2. CHECK OTP INPUT
// =========================
if (!isset($_POST['otp']) || empty(trim($_POST['otp']))) {
    header('Location: verify.php?error=invalid_otp');
    exit();
}

$inputOtp = trim((string)$_POST['otp']);

// =========================
// 3. FIND USER
// =========================
try {
    $user = coll('accounts')
        ->findOne(['email_address' => $email])
        ->execute();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    header('Location: verify.php?error=server_error');
    exit();
}

if (!$user) {
    header('Location: verify.php?error=invalid_otp');
    exit();
}

$userArray = (array)$user;

// =========================
// 4. VALIDATE OTP FIELDS
// =========================
if (!isset($userArray['otp']) || !isset($userArray['otp_expiry'])) {
    header('Location: verify.php?error=invalid_otp');
    exit();
}

// =========================
// 5. EXTRACT VALUES
// =========================
$storedOTP    = trim((string)$userArray['otp']);
$storedExpiry = (int)$userArray['otp_expiry'];
$currentTime  = time();

// =========================
// 6. CHECK EXPIRY
// =========================
if ($currentTime > $storedExpiry) {
    header('Location: verify.php?error=expired');
    exit();
}

// =========================
// 7. VERIFY OTP
// =========================
if ($inputOtp === $storedOTP) {
    
    // Clear OTP keys after successful verification
    try {
        coll('accounts')
            ->updateOne(
                ['_id' => $userArray['_id']],
                ['$unset' => ['otp' => '', 'otp_expiry' => '']]
            )
            ->execute();
    } catch (Exception $e) {
        error_log("Update error: " . $e->getMessage());
    }

    $_SESSION['verified_for_reset'] = true;
    header('Location: reset_password.php');
    exit();
}

// =========================
// 8. FALLBACK INVALID
// =========================
header('Location: verify.php?error=invalid_otp');
exit();