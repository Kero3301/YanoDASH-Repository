<?php
    require_once '../../components/head.php';
    require_once '../../components/navbar.php';
    require_once '../../components/footer.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php initializePage("What is the OSC? | YanoDASH");?>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php echo navbar()?>

    <div class="about-wrapper">
        <div class="about-card">
            <div class="about-left">
                <a href="https://www.facebook.com/obrerosc" target="_blank" class="about-logo-link">
                    <img src="YanoDASH Logo Semifinal Draft.png" alt="Large Logo" class="large-about-logo">
                </a>
                <h2>YanoDASH</h2>
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
                <button class="upload-btn contact-btn">CONTACT US</button>
            </div>
        </div>
    </div>

</body>
</html>