<?php
    session_start();

    $allowed_roles = ['admin', 'editor'];

    if(!isset($_SESSION['auth'])) {
        header("Location: login.php?error=please_login");
        exit();
    }

    if(!in_array($_SESSION['auth']['access_level'], $allowed_roles)) {
        header("Location: add-document.php?error=access_denied");
        exit();
    }


?>