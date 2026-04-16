<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< HEAD
    <link rel="stylesheet" text="text/css" href="../style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Track Request | YanoDASH</title>

    <style>
        * {
            box-sizing: border-box; /* Ensures padding doesn't break widths */
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .form-container {   
            width: 90%;
            max-width: 450px;
            margin: 20px auto;
            padding: 25px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
            border-top: 5px solid #2e7d32;
        }
=======
    <?php initializePage("Track Request | YanoDASH")?>
    <!-- <meta charset="UTF-8">
    <title>Track Request | YanoDASH</title> -->

    <style>
        /* body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #ffffff;
        } */

        .form-container { width: 750px; 
                          margin: 50px auto; 
                          background: #ffffff; 
                          padding: 40px; 
                          border-radius: 15px; 
                          box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
                          border-top: 8px solid #2e7d32; 
                        }
>>>>>>> 22dbbd76b30e94d9f5f2102cff14ea8052fafa20

        .btn-back { 
            display: inline-block; 
            padding: 8px 18px; 
            background-color: #ffffff; 
            color: #333; 
            text-decoration: none; 
            border-radius: 50px; 
            font: 700 13px Arial, sans-serif;
            border: 1px solid #ddd; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            transition: all 0.3s ease; 
            margin-bottom: 20px; 
        }

        .btn-back:hover {
            background-color: #5f0000;
            color: white;
            transform: translateY(-2px);
        }

        .search-group {
            margin-bottom: 25px;
        }

        .search-group label {
            display: block;
            font-family: Arial, sans-serif;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .search-input-wrapper {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .search-input-wrapper input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 16px; /* Prevents auto-zoom on iOS */
        }

        .btn-track {
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            padding: 12px;
            font-size: 14px;
        }

        .btn-track:hover {
            background-color: #1b5e20;
        }

        /* Timeline Styles */
        .status-timeline {
            margin-top: 25px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
            position: relative;
        }

        /* The vertical line connecting steps */
        .step:not(:last-child):after {
            content: "";
            position: absolute;
            left: 14px; /* Centered exactly under the 28px circle */
            top: 30px;
            bottom: -20px;
            width: 2px;
            background: #e0e0e0;
        }

        .circle {
            width: 28px;
            height: 28px;
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            flex-shrink: 0; /* Prevents circle from squishing on mobile */
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 15px;
            z-index: 1;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .step.active .circle { background: #2e7d32; }
        .step.current .circle { background: #1976d2; }

        .step-content h4 {
            margin: 0 0 4px 0;
            font-family: Arial, sans-serif;
            font-size: 15px;
            color: #333;
        }

        .step-content p {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #777;
            line-height: 1.4;
        }

        /* Tablet (481px–768px) */
        @media (min-width: 481px) {
            .form-container {
                width: 80%;
                max-width: 600px;
                margin: 50px auto;
                padding: 30px;
            }

            .search-input-wrapper {
                flex-direction: row;
            }

            .btn-track {
                width: auto;
                padding: 0 25px;
                border-radius: 8px;
            }
        }

        /* Desktop (1024px+) */
        @media (min-width: 1024px) {
            .form-container {
                max-width: 700px;
                padding: 40px;
            }
        }
    </style>
</head>
<body>

<?php echo navbar(0); ?>

<div class="form-container">
    <a href="request.php" class="btn-back">← Back to Menu</a>

    <h2 style="text-align: center; font-family: Arial, sans-serif; margin-bottom: 25px; color: #2e7d32;">Track Your Document</h2>
    
    <div class="search-group">
        <label for="track_id">Enter Document ID</label>
        <div class="search-input-wrapper">
            <input type="text" id="track_id" placeholder="e.g. YD-2024-001">
            <button class="btn-track">Track Now</button>
        </div>
    </div>

    <div class="status-timeline">
        <div class="step active">
            <div class="circle">✓</div>
            <div class="step-content">
                <h4>Request Received</h4>
                <p>System has acknowledged your request.</p>
            </div>
        </div>

        <div class="step current">
            <div class="circle">2</div>
            <div class="step-content">
                <h4>Under Evaluation</h4>
                <p>Your document is being reviewed by the admin.</p>
            </div>
        </div>

        <div class="step">
            <div class="circle">3</div>
            <div class="step-content">
                <h4>Ready / Archived</h4>
                <p>Status will update once processing is complete.</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>