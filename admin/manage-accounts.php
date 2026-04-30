<?php
    session_start();

    require_once '../auth/login_enforcer.php';

    if ($_SESSION['role'] !== 'admin') {
        die('You are not allowed to access this resource.');
    }

    require_once '../components/head.php';
    require_once '../components/navbar.php';
    require_once '../components/footer.php'
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page('Manage Accounts | YanoDASH'); ?>
    </head>
    <body>
        <?php echo navbar(); ?>
        <div class="page-contents" style="background: white">
            <h1 class="pagetitle" style="text-align: center">
                Manage Accounts
            </h1>
            <p><b>Lorem Ipsum</b><br>Lorem ipsum dolor sit amet consectetur adipiscing elit</p>
        </div>
        <?php echo footer(); ?>
    </body>
</html>