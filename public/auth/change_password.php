<?php
require_once '../../bootstrap/app.php';
load('mongodb');

$userId = $identity['user_id'] ?? null;
if (!$userId) die("User not found");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];
    $passwordsEqual = $newPassword === $confirmNewPassword;

    $credentials = coll('login_credentials')
        ->findOne(['user' => new MongoDB\BSON\ObjectId($userId)])
        ->execute();
    if (empty($credentials)) redirect_with_message("Account corrupted.");

    $storedPassword = $credentials['password_hash'];
    if (!password_verify($currentPassword, $storedPassword)) redirect_with_message("Incorrect current password!");
    if (!$passwordsEqual) redirect_with_message("Passwords don't match!");
    if (strlen($newPassword) < 8 || strlen($confirmNewPassword) < 8) redirect_with_message("New password must be at least 8 characters.");
    if (password_verify($confirmNewPassword, $storedPassword)) redirect_with_message("New password can't be same as old password!");

    $passwordHash = password_hash($confirmNewPassword, PASSWORD_ARGON2ID);
    $result = coll('login_credentials')
        ->updateOne(
            ['user' => new MongoDB\BSON\ObjectId($userId)],
            [
                '$set' => ['password_hash' => $passwordHash]
            ]
        )
        ->execute();
    if (!empty($result)) redirect_with_message("Password changed successfully!");
    else redirect_with_message("Sorry, something went wrong.");
} else redirect_with_message("Sorry, something went wrong.");

function redirect_with_message($message) {
    $_SESSION['msg']['passwordChangeMsg'] = $message;
    header('Location: ../account/my-account.php');
    exit;
}

?>