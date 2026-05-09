<?php
    session_start();

    require_once '../../src/loader.php';
    load (
        'authentication',
        'authorization',
        'navbar',
        'document_list',
        'footer'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms($identity))
        die("You do not have permission to access this resource.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page("DMS Portal | YanoDASH ")?>
    <link rel="stylesheet" href="../css/pages/dmsstyle.css">
</head>
<body>
    <?php echo navbar();?>

    <div class="page-contents no-padding">
        <div class="main-wrapper">
        <h1 style="color: maroon; margin-bottom: 32px; text-align: center">Document Management System</h1>

        <div class="document-grid">
            <h1>No documents</h1>
        </div>
    </div>
    </div>
    <?php echo footer()?>
</body>
</html>