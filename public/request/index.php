<?php
    session_start();
    
    require_once dirname(dirname(__DIR__)). '/bootstrap/app.php';
    load (
        'authentication',
        'authorization',
        'navbar',
        'footer'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms($permissions)) {
        die("You do not have permission to access this resource.");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" text="text/css" href="../css/style.css">
    <?php initialize_page("Document Archiving Request | YanoDASH")?>

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

/* Request Box Container */
.request-box {
    background-color: #f4f4f4;
    border-top: 6px solid #63071e;
    border-radius: 16px;
    padding: 60px 40px;
    width: 100%;
    max-width: 400px;
    box-sizing: border-box;
}

.request-box h1 {
    text-align: center;
    margin-top: 0;
    margin-bottom: 40px;
    font-family: 'Gupter', serif;
    font-size: 28px;
}

/* Tablet */
@media (min-width: 481px) {
    .button {
        width: 280px;
    }
    
    .request-box {
        padding: 60px 50px;
        max-width: 450px;
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
    
    .request-box {
        padding: 60px 60px;
        max-width: 500px;
    }
}
</style>

</head>

<body>

<?php echo navbar(0); ?>

<div class="page-contents no-padding">
    <div class="container">
        <div class="request-box">
            <h1>Hey there! Choose what you want to do</h1>

            <a href="archive.php" class="button">Request to Archive</a>
            <a href="track.php" class="button">Track your Request</a>
        </div>
    </div>
</div>

<?php echo footer();?>

</body>
</html>