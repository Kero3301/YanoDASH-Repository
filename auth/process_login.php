<?php
    session_start();

    require __DIR__. '/../vendor/autoload.php';
    require_once '../utils/security/csrf_token.php';

    csrf_protect();

    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') die();

    $email = (string) trim($_POST['email'] ?? '');
    $password = (string) trim($_POST['password'] ?? '');
    $role = null;

    $validCredentials = false;

    $collection_accounts = $client->yano_dash->account_schema;
    $collection_loginCredentials = $client->yano_dash->login_credentials_schema;
    $collection_accessLevels = $client->yano_dash->access_levels_schema;

    $result = $collection_accounts->findOne([
        'email_address' => $email
    ]);

    $fetchedEmail = $result?->email_address;
    if ($fetchedEmail !== null) {
        define('UID', $result->_id);

        $result2 = $collection_loginCredentials->findOne([
            'user' => UID
        ]);

        $result3 = $collection_accessLevels->findOne([
            '_id' => $result->access_level_id
        ]);

        $result_email = $result->email_address;
        $result_pw = $result2->password_hash;
        $result_access_level = $result3->level;

        if (password_verify($password, $result_pw)) {
            $validCredentials = true;
            $email = $result_email;
            $role = $result_access_level;
        }
    }

    if ($validCredentials) {
        $_SESSION['name'] = [
            "firstName" => $result->name->first_name,
            "middleName" => $result->name->middle_name,
            "lastName" => $result->name->last_name
        ];
        $_SESSION['fullname'] = $_SESSION['name']['firstName']. " ". $_SESSION['name']['lastName'];
        $_SESSION['initials'] = strtoupper(($_SESSION['name']['firstName'])[0]). strtoupper(($_SESSION['name']['lastName'])[0]);
        $_SESSION['username'] = $email;
        $_SESSION['role'] = $role;
        $_SESSION['position'] = ($result->organization. ' ' ?? ''). ($result->position);
        $_SESSION['badge'] = match ($result->organization) {
            default => 'Organization Member',
            '' => 'Regular Student',
            'CICLC' => 'Local Council Officer',
            'Obrero Student Council' => 'OSC Officer'
        };

        header("location: /yanodash-repository/");
    } else {
        $_SESSION['errorMsg'] = "Incorrect credentials.";
        header("location: login.php");
        exit;
    }
?>