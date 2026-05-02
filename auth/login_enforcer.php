<?php
    if (!isset($_SESSION['username']) && !isset($_SESSION['role'])) {
        header("location: /yanodash-repository/auth/login.php");
        exit;
    }
?>