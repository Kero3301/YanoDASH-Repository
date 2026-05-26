<?php
    session_start();

    require_once dirname(dirname(__DIR__)). '/bootstrap/app.php';
    load (
        'authentication',
        'csrf_token'
    );

    if (is_logged_in()) {
        header('location: '. $app_url. '/account/my-account.php');
        exit;
    }

    $csrf_token_valid = csrf_protect();
    if (!$csrf_token_valid) {
        $_SESSION['errorMsg'] = "We couldn't verify your request. Please try again.";
        header('location: login.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') die();
    $email = (string) trim($_POST['email'] ?? '');
    $password = (string) trim($_POST['password'] ?? '');

    $loginResult = login_user($email, $password);

    if (!$loginResult->success) {
        $_SESSION['errorMsg'] = $loginResult->message;
        header('location: login.php');
        exit;
    } else {
        header('location: '. $app_url);
        exit;
    }
?>