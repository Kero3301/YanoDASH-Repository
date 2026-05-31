<?php
require_once 'IAMContextResolver.php';
require_once dirname(__DIR__, 2). '/vendor/autoload.php'; 
require_once dirname(__DIR__). '/database/mongodb.php';

final class Authenticator {
    # Check and verify if the user is logged in
    public static function isLoggedIn(): bool {
        if (!isset($_SESSION['user_id'])) return false;

        global $_CURRENTUSER;
        if (!isset($_CURRENTUSER)) return false;

        return IAMContextValidator::validate($_CURRENTUSER);
    }

    # Login user based on a provided email and password, and return a login result for status messaging
    public static function loginUser(string $email, string $password): LoginResult {
        # Initial validation
        if (trim($email) === '' || trim($password) === '')
            return new LoginResult(false, "Credentials cannot be blank.");

        # Queries
        $account = QueryRunner::tryWithCollections([
            ($C1='accounts')
                => fn ($C1)=> $C1->findOne(['email_address' => $email])->execute()
        ])->getResults($C1);
        if (empty($account)) return new LoginResult(false, "Incorrect credentials.");
        $userID = oid($account['_id']);
        $credentials = QueryRunner::tryWithCollections([
            ($C2='login_credentials')
                => fn ($C2)=> $C2->findOne(['user' => $userID])->execute()
        ])->getResults($C2);
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