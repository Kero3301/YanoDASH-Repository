<!-- Private Archive Home Page -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();
    
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Admin Space </title>
    <link rel="stylesheet" type="text/css" href="../css/priv-ar-style.css">
</head>
<body>    

    <?php echo navbar(0); ?>

    <header class="title">
        <h1> Private Archive </h1>
    </header>

    <main class="archive-container">
        <div class="document-grid">

            <div class="doc-card">
                <div class="doc-preview"></div>
                <div class="doc-info">
                    <h3>OSC Budget Allocation 2026</h3>
                    <p>Document ID: OSC-BUD-2026-001</p>
                    <p>Approved budget distribution for student programs</p>
                </div>

                <div class="doc-actions">
                    <button class="view-btn" data-id="OSC-BUD-2026-001">View</button>
                    <button class="delete-btn" data-id="OSC-BUD-2026-001">Delete</button>
                </div>
            </div>

            <div class="doc-card">
                <div class="doc-preview"></div>
                <div class="doc-info">
                    <h3>General Assembly Minutes</h3>
                    <p>Document ID: OSC-GA-2026-002</p>
                    <p>Summary of discussions from the latest assembly</p>
                </div>

                <div class="doc-actions">
                    <button class="view-btn" data-id="OSC-BUD-2026-001">View</button>
                    <button class="delete-btn" data-id="OSC-BUD-2026-001">Delete</button>
                </div>
            </div>

            <div class="doc-card">
                <div class="doc-preview"></div>
                <div class="doc-info">
                    <h3>Event Proposal: Intramurals 2026</h3>
                    <p>Document ID: OSC-EVT-2026-003</p>
                    <p>Proposal for university intramural event</p>
                </div>

                <div class="doc-actions">
                    <button class="view-btn" data-id="OSC-BUD-2026-001">View</button>
                    <button class="delete-btn" data-id="OSC-BUD-2026-001">Delete</button>
                </div>
            </div>

            <div class="doc-card">
                <div class="doc-preview"></div>
                <div class="doc-info">
                    <h3>Partnership Agreement Draft</h3>
                    <p>Document ID: OSC-AGR-2026-004</p>
                    <p>Draft agreement with external sponsors</p>
                </div>

                <div class="doc-actions">
                    <button class="view-btn" data-id="OSC-BUD-2026-001">View</button>
                    <button class="delete-btn" data-id="OSC-BUD-2026-001">Delete</button>
                </div>
            </div>

            <div class="doc-card">
                <div class="doc-preview"></div>
                <div class="doc-info">
                    <h3>Student Complaint Report</h3>
                    <p>Document ID: OSC-REP-2026-005</p>
                    <p>Compiled concerns submitted by students</p>
                </div>

                <div class="doc-actions">
                    <button class="view-btn" data-id="OSC-BUD-2026-001">View</button>
                    <button class="delete-btn" data-id="OSC-BUD-2026-001">Delete</button>
                </div>
            </div>

            <div class="doc-card">
                <div class="doc-preview"></div>
                <div class="doc-info">
                    <h3>Security & Event Protocols</h3>
                    <p>Document ID: OSC-SEC-2026-006</p>
                    <p>Guidelines for managing large student events</p>
                </div>

                <div class="doc-actions">
                    <button class="view-btn" data-id="OSC-BUD-2026-001">View</button>
                    <button class="delete-btn" data-id="OSC-BUD-2026-001">Delete</button>
                </div>
            </div>

        </div>
    </main>

</body>
</html>