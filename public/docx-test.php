<?php
require_once '../bootstrap/app.php';
load ('docx_previewer');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page(".docx Test | YanoDASH")?>
    </head>
    <body>
        <div style="height: 100vh; display: flex; justify-content: center; align-items: center">
        <?php
            $file = 'demo/doc2.docx';
            echo docx_previewer($file);
        ?>
        </div>
    </body>
</html>