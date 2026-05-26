<?php
require_once 'identity_resolver.php';
require_once dirname(__DIR__, 2). '/vendor/autoload.php'; 
require_once dirname(__DIR__). '/database/mongodb_collections.php';

# Check and verify if the user is logged in
function is_logged_in(): bool {
    $userID = $_SESSION['user_id'] ?? null;
    if (!$userID) return false;
    return resolve_identity($userID) !== null;
}

# Login user based on a provided email and password, and return a login result for status messaging
function login_user(string $email, string $password): LoginResult {
    # Initial validation
    if (trim($email) === '' || trim($password) === '')
        return new LoginResult(false, "Credentials cannot be blank.");
    
    $account = coll('accounts')
        ->findOne(['email_address' => $email])
        ->execute();
    if (empty($account)) return new LoginResult(false, "Incorrect credentials.");

    $userID = $account['_id'];
    $credentials = coll('login_credentials')
        ->findOne(['user' => $userID])
        ->execute();
    if (empty($credentials)) return new LoginResult(false, "Account corrupted, please contact an admin.");

    # Password verification
    $storedPassword = $credentials['password_hash'];
    if (password_verify($password, $storedPassword)) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = (string) $userID;
        return new LoginResult(true, "Login successful.");
    } else return new LoginResult(false, "Incorrect credentials.");
}

class LoginResult {
    public bool $success;
    public string $message;

    public function __construct(bool $success, string $message = "") {
        $this->success = $success;
        $this->message = $message;
    }
}
?>