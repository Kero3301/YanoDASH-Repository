<!-- Home Page -->
<!-- Assigned Member: Dwight -->
<?php
    session_start();

    require_once '../src/loader.php';
    load (
        'navbar', 
        'main_section', 
        'footer'
    );
?>

<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page("YanoDASH")?>
        <link rel="stylesheet" href="css/elements.css">
        <style>
        </style>
    </head>
    <body>
        <?php echo navbar()?>
        <div class="contents">
        
        <div class="page-contents no-padding">
                <?php 
            echo main_section(
                "sec1",
                "Official documents, at your fingertips.",
                "Browse documents publicized by the University of 
                Southeastern Philippines Obrero Student Council. 
                Stay always in the know with our public Archive, 
                managed and maintained by the OSC Executives 
                behind the scenes. Whether you are a student leader, 
                organization member, or just a curious Ka-Yano, 
                valuable information awaits!",
                "images/backgrounds/headerimg.jpeg",
                "rgba(236, 53, 47, 0.7)",
                linksList: [
                    ['Browse Public Archive ↗', APP_URL. '/documents/br_arch.php', 'bpa'],
                    ['Latest Releases ↗', APP_URL. '/documents/latest_rel.php', 'lr']
                ]
            );

            echo main_section(
                "stats-section",
                "Numbers tell a story.",
                "Are you curious about the current endeavors of 
                YanoDASH? Our Statistics page offers a data-driven 
                glimpse into official site activities and the status 
                of OSC's commitment to student trust through 
                transparency.",
                "images/backgrounds/stats-section-bg.jpg",
                "rgba(0, 96, 50, 0.8)",
                2,
                linksList: [
                    ['View Statistics ↗', '#', 'vs']
                ]
            );

            echo main_section(
                "dms-section",
                "Fast, precise document management.",
                "Use YanoDASH's full-featured document management system 
                (DMS) to upload, edit, manage, archive, and secure official 
                documents from your respective department or council. Control 
                who gets access by setting View Passwords, secured with the 
                cutting-edge Argon2id algorithm, the winner of the 2015 
                Password Hashing Competition.",
                "images/backgrounds/dms-section-bg.webp",
                "rgba(147, 169, 241, 0.6)",
                linksList: [
                    ['Open YanoDASH DMS ↗', APP_URL. '/dms/', 'oyddms']
                ]
            ); 
            ?>    
            </div>
        
        </div>
        </div>        
        
        <?php echo footer(); ?> 
    </body>
</html>