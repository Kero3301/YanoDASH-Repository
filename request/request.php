<?php
session_start();
require_once '../components/head.php';
require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" text="text/css" href="../style.css">
    <?php initializePage("Document Archiving Request | YanoDASH")?>

<style>
.serif {
    font-family: 'Gupter', serif;
}

.sans {
    font-family: 'RobotoFlex', sans-serif;
}

body {
    margin: 0px;
}

/* ✅ MAIN BUTTON (clean system style) */
.button {
    font: bold 15px 'RobotoFlex', sans-serif;
    background: #63071e;
    color: white;

    border: 2px solid #63071e; /* always present → no shake */

    padding: 18px 20px;
    text-align: center;
    text-decoration: none;

    display: block;
    width: 165px;
    margin: 15px auto;

    cursor: pointer;
    border-radius: 15px;

    transition: all 0.25s ease;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    box-sizing: border-box;

    will-change: transform;
}

/* Hover */
.button:hover {
    background: white;
    color: #63071e;
    transform: translateY(-2px);
}

/* Active */
.button:active {
    background: white;
    color: #63071e;
    transform: translateY(0);
}

/* Container */
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 80px);
    padding: 10px;
}

/* Tablet */
@media (min-width: 481px) {
    .button {
        width: 280px;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .container {
        min-height: 70vh;
    }

    .button {
        width: 260px;
    }
}
</style>

</head>

<body>

<?php echo navbar(0); ?>

<div class="container">
    <h1 style="text-align:center; margin-bottom: 30px;">
        Hey there! choose what you want to do
    </h1>

    <a href="archive.php" class="button">Request to Archive</a>
    <a href="track.php" class="button">Track your Request</a>
    <a href="overview.php" class="button">Request Overview</a>
</div>

</body>
</html>