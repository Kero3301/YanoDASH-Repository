<?php
    session_start();
    require_once '../../../src/loader.php';
    load (
        'vendor_autoload',
        'mongodb_client',
        'mongodb_collections',
        'mailing'
    );

    $TEST_MODE = false;

    $client = mongodb_client();
    $collection = coll('accounts', $client);
    $credentialsColl = coll('login_credentials', $client);

    $email = $_POST["email"];

    // Check if user exists
    $user = $collection->findOne(['email_address' => $email]);

    if (!$user) {
        echo 'Email not found.';
        exit;
    }

    $userID = $user->_id;
    $creds = $credentialsColl->findOne(['user' => $userID]);

    if (!$creds) {
        echo 'Credentials for user are corrupted. Please reach out to an admin.';
        exit;
    }

    //Generate OTP
    $otp = rand(100000, 999999);
    $expiry = time() + 300; // singko minutes
    // Save the OTP to DB
    $credentialsColl->updateOne(
        ['user' => $userID],
        ['$set' => ['otp' => ['code' => $otp, 'expiry' => $expiry]]]
    );
    // Save email in session 
    $_SESSION['reset_email'] = $email;

    //Testing 
    if ($TEST_MODE) {
        echo "TEST MODE: Your OTP is $otp <br>";
        echo "<a href='verify.php'>Go to Verify Page</a>";
    } else {
        send_simple_email($email, "[YanoDASH] Your 6-Digit OTP Code", "Hi, enter this code to verify your request: $otp");
        echo "<a href='verify.php'>Go to Verify Page</a>";
    }
?>