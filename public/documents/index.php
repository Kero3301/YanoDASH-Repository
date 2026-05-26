<!-- Documents Page -->
<?php
    session_start();

    require_once '../../bootstrap/app.php';
    load (
        'navbar',
        'footer'
    );
?>

<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page("Documents | YanoDASH")?>
        <link rel="stylesheet" href="../css/pages/index_docs.css"/>
    </head>
    <body>
         <div class ="header">
        <?php echo navbar(1)?>
       
        <div class="container">
            
            <h1 class="title"> What documents do you want to check? </h1>
            <div class="button-container">
                <a href="latest_rel.php" class="button"> Latest Releases </a>
                <a href="br_arch.php" class="button"> Browse Archive </a>
            </div>
        </div>
        
        <?php echo footer()?>
        </div>
    </body>
</html>