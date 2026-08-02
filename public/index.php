<?php
session_start();

$_SESSION['myvar'] = 'Welcome back, YanoDASH!';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='UTF-8'>
        <title>YanoDASH</title>
    </head>
    <body>
        <?php echo $_SESSION['myvar']; ?>
    </body>
</html>