<?php
include '../components/head.php';
require_once '../components/head.php';
require_once '../components/navbar.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" text="text/css" href="../style.css">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document</title>

<style>
body {
   margin: 0px;
}

.button {
    font: bold 15px Arial;
    border: none;
    color: white;
    padding: 18px 20px;
    text-align: center;
    text-decoration: none;
    display: block;
    width: 165px;
    margin: 15px auto;
    cursor: pointer;
    border-radius: 15px;
    transition: 0.2s ease;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

/* Hover effects */
.button:hover {
    opacity: 0.85;
    transform: scale(1.02);
}

.button:active {
    transform: scale(0.98);
}

/* Colors */
.button1 { background: #2E7D32; }
.button2 { background: #1976D2; }
.button3 { background: #ff7b00; }

/* Container */
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 80px);
    padding: 10px;
}


/* Tablet (481px–768px) */
@media (min-width: 481px) {
    .button {
        width: 280px;
    }
}

/* Large Desktop (1024px and up) */
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

    <a href="archive.php" class="button button1">Request to Archive</a>
    <a href="track.php" class="button button2">Track your Request</a>
    <a href="overview.php" class="button button3">Request Overview</a>
</div>

</body>
</html>