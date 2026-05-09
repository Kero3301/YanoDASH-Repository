<?php
    /* A function to simplify and standardize the initialization of web pages by setting up 
       common properties automatically, such as the page title, charset, viewport, stylesheets,
       and icon in order to create consistency across pagess and reduce copy-paste redundancy.

       Use it like this inside your page's <head></head> tag:

       <?php initialize_page("Your page title goes here")?>
    */
    require_once dirname(dirname(__DIR__)). '/loader.php';

    function initialize_page(string $title) {
        global $app_url;
        $sanitizedTitle = htmlspecialchars($title);

        echo <<< HTML
            <title>$sanitizedTitle</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" href="$app_url/images/favicon.png">
            <link rel="stylesheet" type="text/css" href="$app_url/css/style.css">
            <link rel="stylesheet" type="text/css" href="$app_url/css/fonts.css">
            <script src="$app_url/script/form-validation.js" defer></script>
        HTML;
    }
?>