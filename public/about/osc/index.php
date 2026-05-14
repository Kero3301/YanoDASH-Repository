<!-- What is OSC? -->
<!-- Assigned Member: Carylle -->

<?php
    session_start();
    
    require_once '../../../src/loader.php';
    load('navbar');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page("About | YanoDASH")?>
        <link rel="stylesheet" href="../../css/pages/about1.css"/>
    </head>
    <body>
        <?php echo navbar(5)?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<div class="header">
    <div class="hero-section">
        <h2 id="title">Obrero Student Council</h2>
        <p class="prph">The University of Southeastern Philippines (USeP) Obrero Student Council (OSC) is a 
            student government organization that encompasses and has jurisdiction over a variety of 
            student affairs and activities in the respective university campus located at 
           <a class="loc" href="https://www.google.com/maps/place/University+of+Southeastern+Philippines/@7.0862388,125.6110128,17z/data=!3m1!4b1!4m6!3m5!1s0x32f96daf5b8f0ce5:0x5643261c936b7994!8m2!3d7.0862335!4d125.6156262!16s%2Fm%2F09gddn2?entry=ttu&g_ep=EgoyMDI2MDUxMS4wIKXMDSoASAFQAw%3D%3D" target="_blank">Bo. Obrero, 8000 Davao City</a>.</p>
    </div>

    <div class="con"> 
        <div class="card ms"> 
            <div class="icon-circle">🎯</div>
            <h3 id="title">Our Mission</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ut risus interdum, 
                gravida leo et, sollicitudin nunc. Morbi convallis massa est. Nunc molestie laoreet 
                massa quis finibus. Duis erat turpis, mattis ac purus vel, tempor convallis ex.</p>
        </div>
        <div class="card ms"> 
            <div class="icon-circle">📖</div>
            <h3 id="title">Our Story</h3>
            <p>Lrem ipsum dolor sit amet, consectetur adipiscing elit. Sed ut risus interdum, 
                gravida leo et, sollicitudin nunc. Morbi convallis massa est. Nunc molestie laoreet 
                massa quis finibus. Duis erat turpis, mattis ac purus vel, tempor convallis ex.</p>
        </div>
        <div class="v card"> 
            <h3 id="title">Core Values</h3>
            <div class="values-grid">
                <div class="val-item"><span></span> Integrity</div>
                <div class="val-item"><span></span> Leadership</div>
                <div class="val-item"><span></span> Transparency</div>
                <div class="val-item"><span></span> Service</div>
            </div>
        </div>
    </div>

    <div class="btn-container">
        <a class="modern-btn" href="../executives/executives.php">Meet the Executives <span>↗</span></a>
    </div>
</div>
    </body>
</html>