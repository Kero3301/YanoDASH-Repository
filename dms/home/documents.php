<?php
    session_start();

    require_once '../../components/head.php';
    require_once '../../components/navbar.php';
    require_once '../../components/footer.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php initializePage("DMS Portal | YanoDASH ")?>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php echo navbar();?>

    <div class="page-contents no-padding">
        <div class="main-wrapper">
        <h1 style="color: maroon; margin-bottom: 32px;">Document Management System</h1>

        <div class="document-grid">
            
            <div class="doc-card">
                <div class="doc-thumbnail">
                    <img src="image_c37232.png" alt="Document Preview">
                    <span class="tag gsp">Meeting Minutes</span>
                </div>
                <div class="doc-content">
                    <h3>CIC Local Council - Meeting minutes</h3>
                    <p class="doc-date">📅 March 10, 2026 | 11:53 AM</p>
                    <p class="doc-brief">Meeting regarding the sponsorship for the upcoming event.</p>
                </div>
            </div>

            <div class="doc-card">
                <div class="doc-thumbnail">
                    <img src="image_c37231.png" alt="Document Preview">
                    <span class="tag research">Event Proposal</span>
                </div>
                <div class="doc-content">
                    <h3>CT Local Council</h3>
                    <p class="doc-date">📅 April 29, 2026 | 11:53 AM</p>
                    <p class="doc-brief">Proposal for the collaboration with hudyaka 20XX.</p>
                </div>
            </div>

            <div class="doc-card">
                <div class="doc-thumbnail">
                    <img src="image_c372323.png" alt="Document Preview">
                    <span class="tag essay">Notice Of Meeting</span>
                </div>
                <div class="doc-content">
                    <h3>Obrero Student Council</h3>
                    <p class="doc-date">📅 April 29, 2026 | 11:53 AM</p>
                    <p class="doc-brief">Emergency Meeting for the upcoming Hudyaka 20XX.</p>
                </div>
            </div>

            <div class="doc-card">
                <div class="doc-thumbnail">
                    <img src="image_c37232.png" alt="Document Preview">
                    <span class="tag technical">Accomplishment Report</span>
                </div>
                <div class="doc-content">
                    <h3>Ced Local Council</h3>
                    <p class="doc-date">📅 April 29, 2026 | 11:53 AM</p>
                    <p class="doc-brief">Accomplishment report for the successful exhibit.</p>
                </div>
            </div>

        </div>
    </div>
    </div>
    <?php echo footer()?>
</body>
</html>