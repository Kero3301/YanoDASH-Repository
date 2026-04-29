<?php
    session_start();
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php initializePage("Track Request | YanoDASH")?>

    <style>
        .serif {
            font-family: 'Gupter', serif;
        }

        .sans {
            font-family: 'RobotoFlex', sans-serif;
        }

        .form-container { 
            width: 750px; 
            margin: 50px auto; 
            background: #ffffff; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            border-top: 8px solid #2e7d32; 
        }

        .btn-back { 
            display: inline-block; 
            padding: 8px 18px; 
            background-color: #ffffff; 
            color: #333; 
            text-decoration: none; 
            border-radius: 50px; 
            font: 700 13px 'RobotoFlex', sans-serif;
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
            font-size: 16px; 
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

        .step:not(:last-child):after {
            content: "";
            position: absolute;
            left: 14px;
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
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 15px;
            z-index: 1;
            font-size: 12px;
        }

        .step.active .circle { background: #2e7d32; }
        .step.current .circle { background: #1976d2; }

        .step-content h4 {
            margin: 0 0 4px 0;
            font-size: 15px;
            color: #333;
        }

        .step-content p {
            margin: 0;
            font-size: 13px;
            color: #777;
            line-height: 1.4;
        }

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

    <h2 class="serif" style="text-align: center; margin-bottom: 25px; color: #2e7d32;">
        Track Your Document
    </h2>
    
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