<!-- Private Archive Home Page -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();

    require_once '../utils/loader.php';
    load_components(
        'navbar',
        'document_card'
    );
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page('Private Archive | YanoDASH')?>    
    <link rel="stylesheet" type="text/css" href="../css/pages/priv-ar-style.css">
</head>
<body>    
    <?php echo navbar(0); ?>

    <header class="title">
        <h1> Private Archive </h1>
    </header>

    <main class="archive-container">
        <div class="document-grid">

            <?php 
                echo document_card(
                    title: "OSC Budget Allocation 2026",
                    thumbnailPath: "#",
                    tc: "OSC-BUD-2026-001",
                    tag: "Financial Statement",
                    tagclass: "technical",
                    date: "2026-02-20 | 11:49 PM",
                    description: "Approved budget distribution for student programs",
                    readOnly: true
                );
            ?>

            <?php 
                echo document_card(
                    title: "General Assembly Minutes",
                    thumbnailPath: "#",
                    tc: "OSC-GA-2026-002",
                    tag: "Meeting Minutes",
                    tagclass: "gsp",
                    date: "2026-03-29 | 10:30 AM",
                    description: "Summary of discussions from the latest assembly",
                    readOnly: true
                );
            ?>

            <?php 
                echo document_card(
                    title: "Event Proposal: Intramurals 2026",
                    thumbnailPath: "#",
                    tc: "OSC-EVT-2026-003",
                    tag: "Activity Design",
                    tagclass: "essay",
                    date: "2026-03-29 | 11:39 AM",
                    description: "Proposal for university intramural event",
                    readOnly: true
                );
            ?>

            <?php 
                echo document_card(
                    title: "Partnership Agreement Draft",
                    thumbnailPath: "#",
                    tc: "OSC-AGR-2026-004",
                    tag: "Agreement",
                    tagclass: "technical",
                    date: "2026-01-09 | 4:15 PM",
                    description: "Draft agreement with external sponsors",
                    readOnly: true
                );
            ?>

            <?php 
                echo document_card(
                    title: "Student Complaint Report",
                    thumbnailPath: "#",
                    tc: "OSC-REP-2026-005",
                    tag: "Other",
                    tagclass: "gsp",
                    date: "2026-01-15 | 8:09 AM",
                    description: "Compiled concerns submitted by students",
                    readOnly: true
                );
            ?>

            <?php 
                echo document_card(
                    title: "Security & Event Protocols",
                    thumbnailPath: "#",
                    tc: "OSC-SEC-2026-006",
                    tag: "Guidelines",
                    tagclass: "technical",
                    date: "2026-02-15 | 8:15 PM",
                    description: "Guidelines for managing large student events",
                    readOnly: true
                );
            ?>
        </div>
    </main>

</body>
</html>