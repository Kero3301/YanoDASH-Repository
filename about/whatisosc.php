<!-- What is OSC? -->
<!-- Assigned Member: Carylle -->

<?php
    session_start();
    
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initializePage("About | YanoDASH")?>
        <link rel="stylesheet" href="../css/about1.css"/>
    </head>
    <body>
        <?php echo navbar(5)?>
    <div>
        <h2 id="title"> Obrero Student Council</h2> </br>
        <p class="prph"> The University of Southeastern Philippines (USeP) Obrero Student Council (OSC) is a 
            student government organization that encompasses and has jurisdiction over a variety of 
            student affairs and activities in the respective university campus located at 
            Bo. Obrero, 8000 Davao City. </p>
    </div>
    <div class="con"> 
        <div class="ms"> 
            <h3 id="title"> Our Mission</h3> </br>
            <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ut risus interdum, 
                gravida leo et, sollicitudin nunc. Morbi convallis massa est. Nunc molestie laoreet 
                massa quis finibus. Duis erat turpis, mattis ac purus vel, tempor convallis ex.</p>
        </div>
        <div class="ms"> 
            <h3 id="title"> Our Story</h3> </br>
            <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ut risus interdum, 
                gravida leo et, sollicitudin nunc. Morbi convallis massa est. Nunc molestie laoreet 
                massa quis finibus. Duis erat turpis, mattis ac purus vel, tempor convallis ex.</p>
        </div>
        <div class="v"> 
            <h3 id="title"> Core Values</h3>
            <div class="container">
                <div> Integrity</div>
                <div> Leadership</div>
                <div> Transparency</div>
                <div> Service</div>
            </div>
        </div>
    </div>

    </body>
</html>