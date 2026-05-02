<?php
    session_start();

    require_once '../utils/loader.php';
    load_components(
        'navbar',
        'document_card',
        'footer'
    );
    load_utils(
        'authentication',
        'authorization'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms())
        die("You do not have permission to access this resource.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page("DMS Portal | YanoDASH ")?>
    <link rel="stylesheet" href="../css/pages/dmsstyle.css">
</head>
<body>
    <?php echo navbar();?>

    <div class="page-contents no-padding">
        <div class="main-wrapper">
        <h1 style="color: maroon; margin-bottom: 32px; text-align: center">Document Management System</h1>

        <div class="document-grid">
            <?php 
                echo document_card(
                    title: "CIC Local Council - Meeting Minutes",
                    thumbnailPath: "../images/thumbnails/image_c37232.png",
                    tag: "Meeting Minutes",
                    tagclass: "gsp",
                    date: "2026-05-10 | 11:53 AM",
                    description: "Meeting regarding the sponsorship for the upcoming event."
                );
            ?>

            <?php 
                echo document_card(
                    title: "CT Local Council",
                    thumbnailPath: "../images/thumbnails/image_c37231.png",
                    tag: "Event Proposal",
                    tagclass: "research",
                    date: "2026-04-29 | 11:53 AM",
                    description: "Proposal for the collaboration with Hudyaka 20XX."
                );
            ?>

            <?php 
                echo document_card(
                    title: "Obrero Student Council",
                    thumbnailPath: "../images/thumbnails/image_c372323.png",
                    tag: "Notice of Meeting",
                    tagclass: "essay",
                    date: "2026-04-29 | 11:53 AM",
                    description: "Emergency Meeting for the upcoming Hudyaka 20XX."
                );
            ?>

            <?php 
                echo document_card(
                    title: "CEd Local Council",
                    thumbnailPath: "../images/thumbnails/image_c37232.png",
                    tag: "Accomplishment Report",
                    tagclass: "technical",
                    date: "2026-04-29 | 11:53 AM",
                    description: "Accomplishment report for the successful exhibit."
                );
            ?>


        </div>
    </div>
    </div>
    <?php echo footer()?>
</body>
</html>