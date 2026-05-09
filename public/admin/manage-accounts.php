<?php
    session_start();

    require_once '../../src/loader.php';
    load (
        'authentication',
        'authorization',
        'navbar',
        'footer'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_access_admin_pages($identity)) {
        die("You do not have permission to access this resource.");
    }
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