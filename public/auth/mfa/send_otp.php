<?php

session_start();

// Force UTC time globally
date_default_timezone_set('UTC'); 

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
])->execute(); // Make sure ->execute() is called if your wrapper needs it

// Check if user exists
if (!$user) {
    die("User not found.");
}

$userArray = (array)$user;
$userID = $userArray['_id'];

// =========================
// GENERATE OTP (5-Minute Window)
// =========================
$otp = rand(100000, 999999);
$expiry = time() + 300; // 5 minutes in seconds from right now

// =========================
// SAVE OTP (Saved flatly to match the database view)
// =========================
$accountsColl->updateOne(
    ['_id' => $userID],
    [
        '$set' => [
            'otp' => (string)$otp,
            'otp_expiry' => (int)$expiry
        ]
    ]
)->execute();

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