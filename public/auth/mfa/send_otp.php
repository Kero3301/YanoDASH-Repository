<?php

session_start();

require_once '../../../bootstrap/app.php';

load(
    'vendor_autoload',
    'mongodb_client',
    'mongodb_collections',
    'mailing'
);

$TEST_MODE = false;

// Connect to MongoDB
$client = mongodb_client();

$accountsColl = coll('accounts', $client);
$credentialsColl = coll('login_credentials', $client);

// =========================
// CHECK EMAIL INPUT
// =========================
if (!isset($_POST['email'])) {
    die("Email is required.");
}

$email = trim($_POST['email']);

// =========================
// FIND USER
// =========================
$user = $accountsColl->findOne([
    'email_address' => $email
]);

// Check if user exists
if (!$user) {
    die("User not found.");
}

// Get MongoDB Object ID
$userID = $user->_id;

// =========================
// GENERATE OTP
// =========================
$otp = rand(100000, 999999);

// 5 minutes expiry
$expiry = time() + 300;

// =========================
// SAVE OTP
// =========================
$credentialsColl->updateOne(
    ['user' => $userID],
    [
        '$set' => [
            'otp' => [
                'code' => (string)$otp,
                'expiry' => (int)$expiry
            ]
        ]
    ]
);

// Save session
$_SESSION['reset_email'] = $email;

// =========================
// SEND EMAIL
// =========================
if ($TEST_MODE) {
    echo "TEST MODE OTP: $otp <br>";
    echo "<a href='verify.php'>Go to Verify Page</a>";
} else {
    send_simple_email(
        $email,
        "[YanoDASH] Your 6-Digit OTP Code",
        "Hi! Your OTP code is: $otp\n\nThis code expires in 5 minutes."
    );

    header("Location: verify.php");
    exit();
}
