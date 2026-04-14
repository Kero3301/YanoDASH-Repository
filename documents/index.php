<!-- Documents Page -->
<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <?php initializePage("Documents | YanoDASH")?>
        <link rel="stylesheet" href="index_docs.css"/>
    </head>
    <body>
        <?php echo navbar(1)?>
        <div>
            <h1 class="title"> What documents do you want to check? </h1>
            <div class="button-container">
                <a href="latest_rel.php" class="button"> Latest Releases </a>
                <a href="br_arch.php" class="button"> Browse Archive </a>
            </div>
        </div>
    </body>
</html>