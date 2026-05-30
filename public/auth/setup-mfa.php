<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../bootstrap/app.php';

load(
    'vendor_autoload',
    'mongodb',
    'mailing'
);

/**
 * 1. RESET LOGIC
 * If the user performs a standard page load/refresh (GET), 
 * we clear the 2FA session so they start from Step 1.
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    unset($_SESSION['otp_email'], $_SESSION['otp_code'], $_SESSION['otp_expiry']);
}

$error = null;
$success = false;

/**
 * 2. STEP 1: Email Submission (POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Generate and store OTP details
        $otp = (string)random_int(100000, 999999);
        
        $_SESSION['otp_email'] = $email;
        $_SESSION['otp_code'] = $otp;
        $_SESSION['otp_expiry'] = time() + 300; // 5 minute window

        // TODO: Integration with your mailer here
        send_simple_email($email, "[YanoDASH] Your 6-Digit OTP Code", "Hi, enter this code to confirm and enable email 2FA for your account: $otp");
    } else {
        $error = "Please enter a valid email address.";
    }
}

/**
 * 3. STEP 2: OTP Verification (POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $otp_input = $_POST['otp'];
    $enteredOtp = is_array($otp_input) ? implode('', $otp_input) : '';

    $isValid = isset($_SESSION['otp_code'], $_SESSION['otp_expiry']) &&
               time() < $_SESSION['otp_expiry'] &&
               $enteredOtp === $_SESSION['otp_code'];

    if ($isValid) {
        // Clean up session after success
        unset($_SESSION['otp_email'], $_SESSION['otp_code'], $_SESSION['otp_expiry']);

        $client = mongodb_client();
        $accs = coll('accounts', $client);
        $creds = coll('login_credentials', $client);
        $uid = new MongoDB\BSON\ObjectId($identity['user_id']);

        $result = $creds->updateOne(
            ['user' => $uid],              
            ['$set' => [
                'otp' => [
                    'code' => null, 
                    'expiry' => null
                ]
            ]]
        );
        if ($result) $success = true;
    } else {
        $error = "Invalid or expired OTP code.";
    }
}

$hasEmailInSession = isset($_SESSION['otp_email']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page("Setup Email 2FA"); ?>
    <style>
        .otp-container {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }

        .otp {
            width: 45px;
            height: 55px;
            font-size: 24px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .error {
            color: #d93025;
            background: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .success-message {
            text-align: center;
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="page-contents">

    <?php if ($success): ?>
        <div class="success-message">
            <h2>✅ 2FA Setup Successful!</h2>
            <p>Your email has been verified and two-factor authentication is active.</p>
            <a href="/yanodash-repository/public/account/my-account.php" class="btn">Return to Dashboard</a>
        </div>
    <?php else: ?>

        <form method="POST" autocomplete="off">

            <?php if (!$hasEmailInSession): ?>
                <!-- STEP 1: EMAIL INPUT -->
                <h2>Setup Email Two-factor Authentication</h2>
                <p>We will send a 6-digit verification code to your email.</p>

                <?php if ($error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p><br>
                <?php endif; ?>

                <input style="width:300px; padding: 10px;" type="email" name="email" placeholder="name@example.com" required>
                <button type="submit" class="btn">Send Code</button>

            <?php else: ?>
                <!-- STEP 2: OTP INPUT -->
                <h2>Verify your Email</h2>
                <p>Enter the code sent to <strong><?= htmlspecialchars($_SESSION['otp_email']) ?></strong></p>

                <?php if ($error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <div class="otp-container">
                    <?php for($i=0; $i<6; $i++): ?>
                        <input type="text" 
                               maxlength="1" 
                               name="otp[]" 
                               class="otp" 
                               pattern="\d*" 
                               inputmode="numeric" 
                               required 
                               autocomplete="one-time-code">
                    <?php endfor; ?>
                </div>

                <button type="submit" class="btn">Verify & Complete</button>
                <p><small>Exiting this page will restart the setup process.</small></p>

            <?php endif; ?>

        </form>
    <?php endif; ?>

</div>

<script>
/**
 * OTP Input Handling: Auto-focus and Backspace logic
 */
const inputs = document.querySelectorAll(".otp");

inputs.forEach((input, index) => {
    // Handle typing numbers
    input.addEventListener("input", (e) => {
        if (e.inputType === "deleteContentBackward") return;
        
        if (input.value && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });

    // Handle backspace
    input.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && !input.value && index > 0) {
            inputs[index - 1].focus();
        }
    });
});

// Clipboard Paste Support
document.querySelector(".otp-container")?.addEventListener("paste", (e) => {
    e.preventDefault();
    const data = e.clipboardData.getData("text").trim().split("");
    
    inputs.forEach((input, i) => {
        if (data[i]) {
            input.value = data[i];
        }
    });
    
    // Focus the last filled input or the last input overall
    const lastIdx = Math.min(data.length, inputs.length) - 1;
    if (lastIdx >= 0) inputs[lastIdx].focus();
});
</script>

</body>
</html>