<?php
    session_start();

    require_once dirname(__DIR__). '/utils/loader.php'; 
    load_utils(
        'authentication',
        'authorization'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_access_admin_pages()) {
        die("You do not have permission to access this resource.");
    }
?>