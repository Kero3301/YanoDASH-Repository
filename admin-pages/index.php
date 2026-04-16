<!-- Admin Space Index -->
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
    <title> Admin Space </title>
    <link rel="stylesheet" type="text/css" href="../css/indexstyle.css">
</head>
<body>    

    <?php echo navbar(0); ?>

    <header class="title">
        <h1> Where do you want to go? </h1>
    </header>

    <main>
        <div class="button-container">
            <a href="archive-rq.php" class="button button1">Pending Archive Requests</a>
            <a href="upload.php" class="button button2">Create/Upload Document</a>
            <a href="manage.php" class="button button3">Manage Documents</a>
            <a href="security.php" class="button button4">Security</a>
        </div>
    </main>

</body>
</html>