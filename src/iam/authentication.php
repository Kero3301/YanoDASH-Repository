<?php
require_once 'identity_resolver.php';
require_once dirname(__DIR__, 2). '/vendor/autoload.php'; 
require_once dirname(__DIR__). '/database/mongodb_client.php';
require_once dirname(__DIR__). '/utils/schema_validator.php';

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
    
    # Client and collections
    $client = mongodb_client();
    $collection_accounts = $client->yano_dash->account_schema;
    $collection_loginCredentials = $client->yano_dash->login_credentials_schema;

    # Query
    $condition_account = ['email_address' => $email];
    $account = $collection_accounts->findOne($condition_account);
    if (!$account) return new LoginResult(false, "Incorrect credentials.");
    if (!baseline_schema_validate($account, 'ACCOUNTS'))
        return new LoginResult(false, "Account corrupted, please contact an admin.");
    $userID = $account->_id;
    $condition_login_creds = ['user' => $userID];
    $credentials = $collection_loginCredentials->findOne($condition_login_creds);
    if (!$credentials || !baseline_schema_validate($credentials, 'LOGIN_CREDENTIALS'))
        return new LoginResult(false, "Account corrupted, please contact an admin.");

    # Password verification
    $storedPassword = $credentials->password_hash;
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