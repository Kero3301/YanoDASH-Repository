<?php
    require_once '../../bootstrap/app.php';

    session_start();
    session_unset();
    session_destroy();
    header("location: $app_url");
?>