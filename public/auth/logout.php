<?php
    require_once '../../src/loader.php';

    session_start();
    session_unset();
    session_destroy();
    header("location: $app_url");
?>