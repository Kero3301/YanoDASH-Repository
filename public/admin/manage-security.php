<!-- Pending Archive Requests -->
<!-- Assigned Member: Shannon -->

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
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Security </title>

    <?php initialize_page("Security"); ?>

    <link rel="stylesheet" href="../css/pages/securitystyle.css">
</head>

<body>

<?php echo navbar(); ?>

<main class="security-container">

    <div class="security-header">
        <h1>Security</h1>
        <p>Manage access control and document protection settings</p>
    </div>

    <div class="security-grid">


        <a href="manage_document_security.php" class="security-card">
            <h2>Document Security</h2>
            <p>Control who can view, edit, and manage documents.</p>
        </a>

        <a href="access_logs.php" class="security-card">
            <h2>Access Logs</h2>
            <p>View password activity and document access history.</p>
        </a>

    </div>

</main>

</body>
</html>