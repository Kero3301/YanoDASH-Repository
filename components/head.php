<?php
    /* A function to simplify and standardize the initialization of web pages by setting up 
       common properties automatically, such as the page title, charset, viewport, stylesheets,
       and icon in order to create consistency across pagess and reduce copy-paste redundancy.

       Use it like this inside your page's <head></head> tag:

       <?php initializePage("Your page title goes here")?>
    */
    function initializePage(string $title) {
        $sanitizedTitle = htmlspecialchars($title);
        
        echo <<< HTML
            <title>$sanitizedTitle</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" href="/yanodash-repository/images/favicon.png">
            <link rel="stylesheet" type="text/css" href="/yanodash-repository/style.css">
            <link rel="stylesheet" type="text/css" href="/yanodash-repository/css/fonts.css">
        HTML;
    }
?>