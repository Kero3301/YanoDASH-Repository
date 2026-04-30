<?php
    function init() {
        if (session_status() !== PHP_SESSION_ACTIVE) 
            session_start();
    }

    function generate_token() {
        init();

        $_SESSION['_csrfToken'] = bin2hex(random_bytes(32));
        $_SESSION['_csrfTokenTime'] = time();

        return $_SESSION['_csrfToken'];
    }

    function get_token() {
        init();

        if (empty($_SESSION['_csrfToken']))
            return generate_token();
        
        return $_SESSION['_csrfToken'];
    }

    function verify_token($token, $ttl = 3600) {
        init();

        if (empty($_SESSION['_csrfToken']) || empty($token))
            return false;

        if (isset($_SESSION['_csrfTokenTime'])) {
            if ((time() - $_SESSION['_csrfTokenTime']) > $ttl) {
                regenerate_token();
                return false;
            }
        }

        $isValid = hash_equals($_SESSION['_csrfToken'], $token);
        if ($isValid) regenerate_token();

        return $isValid;
    }

    function regenerate_token() {
        init();

        unset($_SESSION['_csrfToken'], $_SESSION['_csrfTokenTime']);
        return generate_token();
    }

    function csrf_input_field() {
        $token = htmlspecialchars(get_token(), ENT_QUOTES, 'UTF-8');
        return <<< HTML
            <input type="hidden" name="_csrfToken" value="$token">
        HTML;
    }

    function csrf_protect() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrfToken'] ?? '';

            if (!verify_token($token)) {
                http_response_code(403);
                die('CSRF validation failed.');
            }
        }
    }
?>