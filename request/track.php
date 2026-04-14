<?php
require_once '../components/head.php';
require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Request | YanoDASH</title>

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #ffffff;
        }

        .form-container { width: 600px; 
                          margin: 50px auto; 
                          background: #ffffff; 
                          padding: 40px; 
                          border-radius: 15px; 
                          box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
                          border-top: 8px solid #2e7d32; 
                        }

 .btn-back { display: inline-block; 
             padding: 10px 25px; 
             background-color: #ffffff; 
             color: #333; 
             text-decoration: none; 
             border-radius: 50px; 
             font:700 14px Arial,sans-serif;
             border: 1px solid #ddd; 
             box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
             transition: all 0.3s ease; 
             margin-bottom: 25px; }

        .btn-back:hover {
            background-color: #5f0000;
            color: white;
            transform: translateY(-2px);
        }

        .search-group {
            margin-bottom: 30px;
        }

        .search-group label {
            display: block;
            font-family: Arial, sans-serif bold;
            margin-bottom: 8px;
            color: #333;
        }

        .search-input-wrapper {
            display: flex;
            gap: 10px;
        }

        .search-input-wrapper input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: Arial, sans-serif;
        }

        .btn-track {
            background-color: #2e7d32;
            color: white;
            border: none;
            padding: 0 30px;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-track:hover {
            background-color: #1b5e20;
        }

        .status-timeline {
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 30px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
            position: relative;
        }

        .step:not(:last-child):after {
            content: "";
            position: absolute;
            left: 17px;
            top: 35px;
            bottom: -30px;
            width: 2px;
            background: #e0e0e0;
        }

        .circle {
            width: 35px;
            height: 35px;
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 20px;
            z-index: 1;
            font-family: Arial, sans-serif;
        }

        .step.active .circle {
            background: #2e7d32;
        }

        .step.current .circle {
            background: #1976d2; 
        }

        .step-content h4 {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .step-content p {
            margin: 5px 0 0;
            font-family: Arial, sans-serif;
            color: #777;
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php echo navbar(0); ?>

<div class="form-container">
    <a href="request.php" class="btn-back">Back to Menu</a>

    <h2 style="text-align: center; font-family: 'Sans-serif'; margin-bottom: 30px; color: #2e7d32;">Track Your Document</h2>
    
    <div class="search-group">
        <label for="track_id">Enter Document ID</label>
        <div class="search-input-wrapper">
            <input type="text" id="track_id" placeholder="e.g. YD-2024-001">
            <button class="btn-track">Track</button>
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