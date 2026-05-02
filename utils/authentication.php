<?php
    require dirname(__DIR__). '/vendor/autoload.php'; 
    require_once dirname(__DIR__). '/utils/loader.php';
    load_utils('schema_validator');

    function is_logged_in(): bool {
        return isset($_SESSION['auth']);
    }
    
    function login_user(string $email, string $password): LoginResult {
        $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));
            
        $collection_accounts = $client->yano_dash->account_schema;
        $collection_loginCredentials = $client->yano_dash->login_credentials_schema;
        $collection_accessLevels = $client->yano_dash->access_levels_schema;

        if (trim($email) === '' || trim($password) === '') 
            return new LoginResult(false, "Credentials cannot be blank.");

        $accountQueryResult = $collection_accounts->findOne([
            'email_address' => $email
        ]);
        if (!$accountQueryResult) 
            return new LoginResult(false, "Incorrect credentials.");

        if (!baseline_schema_validate($accountQueryResult, 'ACCOUNTS')) 
            return new LoginResult(false, "Account corrupted. Please contact an admin.");
        
        $storedUID = $accountQueryResult->_id;
        
        $loginCredsQueryResult = $collection_loginCredentials->findOne([
            'user' => $storedUID
        ]);
        if (!$loginCredsQueryResult || !baseline_schema_validate($loginCredsQueryResult, 'LOGIN_CREDENTIALS')) 
            return new LoginResult(false, "Account corrupted. Please contact an admin.");

        $accessLevelQueryResult = $collection_accessLevels->findOne([
            '_id' => $accountQueryResult->access_level_id
        ]);
        if (!$accessLevelQueryResult || !baseline_schema_validate($accessLevelQueryResult, 'ACCESS_LEVELS'))
            return new LoginResult(false, "User's access level could not be verified.");

        $storedPassword = $loginCredsQueryResult->password_hash;

        if (password_verify($password, $storedPassword)) {
            $storedName = json_decode(json_encode($accountQueryResult->name), true);            
            $storedStudentIDNumber = $accountQueryResult->student_id_number ?? "(N/A)";
            $storedOrganization = $accountQueryResult->organization;
            $storedPosition = $accountQueryResult->position;
            $storedEmail = $accountQueryResult->email_address;
            $storedAccessLevel = $accessLevelQueryResult->level;
            $storedScopeOfAccess = json_decode(json_encode($accessLevelQueryResult->scope_of_access), true);
            $storedAccessDomains = json_decode(json_encode($accountQueryResult->access_domains), true);

            if (session_status() !== PHP_SESSION_ACTIVE) session_start();

            session_regenerate_id(true);
            $_SESSION['auth'] = [
                'uid' => (string) $storedUID,
                'name' => $storedName,
                'studentIDNumber' => $storedStudentIDNumber,
                'organization' => $storedOrganization,
                'position' => $storedPosition,
                'email' => $storedEmail,
                'access_level' => $storedAccessLevel,
                'scope_of_access' => $storedScopeOfAccess,
                'access_domains' => $storedAccessDomains ?? []
            ];
            return new LoginResult(true);
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