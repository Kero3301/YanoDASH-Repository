<?php
    session_start();

    require_once '../../src/loader.php'; 
    load (
        'user_profile_service',
        'authentication',
        'authorization',
        'navbar'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_access_admin_pages($permissions)) {
        die("You do not have permission to access this resource.");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page('Admin Space | YanoDASH')?>
    </head>
    <body>
        <?php echo navbar()?>
        <div class="page-contents">
            <h1>Admin Space</h1>
            <?php
                $fullname = full_name($profile);
                $email = $identity['email'];
            ?>
            <p>Welcome, <b><?= "$fullname</b> ($email)" ?>!</p>
            <a class="btn" href="manage-accounts.php">Manage Accounts</a>
            <a class="btn" href="manage-security.php">Manage Security</a>
        </div>
    </body>
</html>