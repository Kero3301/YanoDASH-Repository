<?php
    session_start();

    require_once 'components/head.php';
    require_once 'components/navbar.php';
    require_once 'components/document_card.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initializePage('Test');?>
    </head>
    <body>
        <?php echo navbar();?>
        <div class="page-contents">
            <?php echo documentCard(tagclass: "gsp", date: "2026-04-26 | 11:53 AM", author: "Johnny Bravo", dept: "OSC Office of the General Secretary", title: "Hudyaka 2026", tag: "ACCOMPLISHMENT REPORT");?>
            <?php echo documentCard(description: "Meeting regarding the sponsorship for the upcoming event.", tc: "DEF-1011-12134", date: "2026-03-10 | 11:05 AM", author: "John Doe", dept: "CICLC", title: "CIC Local Council - Meeting Minutes", tag: "MEETING MINUTES", thumbnailPath: "/yanodash-repository/dms/home/image_c37232.png");?>
            <?php echo documentCard(tc: "GHI-5161-71819", tagclass: "research", author: "Jane Doe", dept: "OSC Office of the General Treasurer", title: "Hudyaka 2025 Financial Statement", tag: "FINANCIAL STATEMENT", thumbnailPath: "/yanodash-repository/dms/home/image_c37234.png");?>

            </div
    </body>
</html>