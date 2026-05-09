<?php
    session_start();
    require_once '../../src/loader.php';
    
    load (
        'navbar',
        'footer'
    );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page("Contact Us | YanoDASH");?>
    <link rel="stylesheet" href="../css/pages/contactstyle.css">
</head>
<body>
    <?php echo navbar()?>
    <div class="about-wrapper">
        <div class="about-card">
            <div class="about-left">
                    <img src="../images/YanoDASH Logo Semifinal Draft.png" alt="Large Logo" class="large-about-logo">
            </div>
            
            <div class="about-right">
                <h1>WHO WE ARE</h1>
                <p>
                    The Obrero Student council is an official student council in 
                    the University of Southeastern Philippines Main Campus. Driven with passion
                    and dreams, the group of student leaders are determined to serve the Campus
                    with guidance, leadership, and with hospitality.
                </p>
                <p>
                    Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.
                </p>
                <a href="https://www.facebook.com/obrerosc" class="upload-btn contact-btn" target="_blank" style="display: inline-block; padding: 10px;">
    <img src="Facebook_logo_(square).png" alt="Facebook" style="width: 30px; vertical-align: middle;">
</a>
            </div>
        </div>
    </div>
    <?php echo footer() ?>
</body>
</html>