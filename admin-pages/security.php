<!-- Pending Archive Requests -->
<!-- Assigned Member: Shannon -->

<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Security </title>

    <?php initializePage("Security"); ?>

    <link rel="stylesheet" href="../css/securitystyle.css">
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