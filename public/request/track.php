<?php
    session_start();
    require_once dirname(dirname(__DIR__)). '/src/loader.php';
    load(
        'authentication',
        'authorization',
        'navbar',
    );

    // Temporary testing mode: bypass auth so this page can be used without login.
    // Remove or restore this check after testing.
    // if (!is_logged_in()) {
    //     header('location: '. $app_url. '/auth/login.php');
    //     exit;
    // }

    // if (!can_use_dms($permissions)) 
    //     die("You do not have permission to access this resource.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page("Track Request | YanoDASH")?>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .serif {
            font-family: 'Gupter', serif;
        }

        .sans {
            font-family: 'RobotoFlex', sans-serif;
        }

        /* Mobile-First Approach */
        .form-container { 
            width: 100%;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            border-top: 6px solid #2e7d32;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            max-width: calc(100% - 32px);
        }

        .btn-back { 
            display: inline-block;
            padding: 10px 16px;
            background-color: #ffffff;
            color: #333;
            text-decoration: none;
            border-radius: 50px;
            font: 700 12px 'RobotoFlex', sans-serif;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .btn-back:hover {
            background-color: #5f0000;
            color: white;
            transform: translateY(-2px);
        }

        .form-container h2 {
            text-align: center;
            margin: 0 0 25px 0;
            color: #2e7d32;
            font-size: 24px;
            word-break: break-word;
        }

        .search-group {
            margin-bottom: 20px;
        }

        .search-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        /* .search-input-wrapper {
            display: flex;
            flex-direction: column;
            gap: 10px;
        } */

        .siw {
            margin: auto;
            display: block;
        }

        .search-input-wrapper {
            display: flex;
            flex-direction: column;
            gap: 10px;

            align-items: center;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'RobotoFlex', sans-serif;
            transition: border-color 0.3s ease;
        }

        .search-input-wrapper input:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .btn-track {
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            padding: 12px;
            font-size: 14px;
            font-family: 'RobotoFlex', sans-serif;
        }

        .btn-track:hover {
            background-color: #1b5e20;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-track:active {
            transform: translateY(0);
        }

        .status-timeline {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            position: relative;
        }

        .step:not(:last-child):after {
            content: "";
            position: absolute;
            left: 13px;
            top: 28px;
            bottom: -20px;
            width: 2px;
            background: #e0e0e0;
            z-index: 0;
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
            margin-right: 12px;
            z-index: 1;
            font-size: 12px;
        }

        .step.active .circle { 
            background: #2e7d32; 
        }

        .step.current .circle { 
            background: #1976d2; 
        }

        .step-content h4 {
            margin: 0 0 4px 0;
            font-size: 14px;
            color: #333;
            font-weight: bold;
        }

        .step-content p {
            margin: 0;
            font-size: 12px;
            color: #777;
            line-height: 1.4;
        }

        /* Tablet (481px and up) */
        @media (min-width: 481px) {
            .form-container {
                width: calc(100% - 40px);
                max-width: 550px;
                margin: 30px auto;
                padding: 30px;
                border-radius: 15px;
            }

            .form-container h2 {
                font-size: 26px;
                margin-bottom: 30px;
            }

            .search-group {
                margin-bottom: 25px;
            }

            .search-group label {
                font-size: 15px;
                margin-bottom: 10px;
            }

            .search-input-wrapper {
                flex-direction: row;
                gap: 12px;
                align-items: flex-end;
            }

            .search-input-wrapper input {
                flex: 1;
                padding: 12px;
                font-size: 15px;
            }

            .btn-track {
                width: auto;
                padding: 12px 30px;
                font-size: 15px;
                flex-shrink: 0;
            }

            .step {
                margin-bottom: 25px;
            }

            .step-content h4 {
                font-size: 15px;
            }

            .step-content p {
                font-size: 13px;
            }
        }

        /* Desktop (1024px and up) */
        @media (min-width: 1024px) {
            .form-container {
                width: calc(100% - 60px);
                max-width: 700px;
                margin: 40px auto;
                padding: 40px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            }

            .form-container h2 {
                font-size: 28px;
                margin-bottom: 35px;
            }

            .search-group {
                margin-bottom: 30px;
            }

            .search-group label {
                font-size: 16px;
                margin-bottom: 12px;
            }

            .search-input-wrapper {
                gap: 15px;
            }

            .search-input-wrapper input {
                padding: 13px;
                font-size: 16px;
            }

            .btn-track {
                padding: 13px 35px;
                font-size: 16px;
            }

            .status-timeline {
                margin-top: 30px;
                padding-top: 25px;
            }

            .step {
                margin-bottom: 30px;
            }

            .circle {
                width: 32px;
                height: 32px;
                font-size: 14px;
                margin-right: 18px;
            }

            .step-content h4 {
                font-size: 16px;
            }

            .step-content p {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

<?php echo navbar(0); ?>

<div class="form-container">
    <a href="index.php" class="btn-back">← Back to Menu</a>

    <h2 class="serif">Track Your Archiving Request</h2>
    
    <div class="search-group">
        <label for="track_id">Enter Tracking Code</label>
        <div class="siw">
        <div class="search-input-wrapper">
            <input type="text" id="track_id" placeholder="e.g. AR-2024-00001" required>
            <button class="btn-track">Track Now</button>
        </div>
        </div>
    </div>

    <div class="status-timeline" id="timeline">
        <div class="step active">
            <div class="circle">1</div>
            <div class="step-content">
                <h4>Waiting for Review</h4>
                <p>Admin team reviewing your request</p>
            </div>
        </div>

        <div class="step">
            <div class="circle">2</div>
            <div class="step-content">
                <h4>Being Processed</h4>
                <p>Document is being prepared for archival</p>
            </div>
        </div>

        <div class="step">
            <div class="circle">3</div>
            <div class="step-content">
                <h4>Archived</h4>
                <p>Document has been successfully archived</p>
            </div>
        </div>
    </div>

    <div id="result-container" style="display:none; margin-top: 30px; padding: 20px; background: #f0f8f4; border-radius: 8px; border-left: 4px solid #2e7d32;">
        <h3 id="result-title" style="margin: 0 0 10px 0; color: #2e7d32;"></h3>
        <p id="result-reason" style="margin: 0; color: #555; font-size: 14px;"></p>
    </div>
</div>

<script>
const trackBtn = document.querySelector('.btn-track');
const trackInput = document.querySelector('#track_id');
const timeline = document.querySelector('#timeline');
const resultContainer = document.querySelector('#result-container');
const resultTitle = document.querySelector('#result-title');
const resultReason = document.querySelector('#result-reason');

trackBtn.addEventListener('click', trackDocument);
trackInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') trackDocument();
});

async function trackDocument() {
    const code = trackInput.value.trim();
    
    if (!code) {
        alert('Please enter a tracking code');
        return;
    }

    trackBtn.disabled = true;
    trackBtn.textContent = 'Searching...';

    try {
        const response = await fetch('track_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `tracking_code=${encodeURIComponent(code)}`
        });

        const data = await response.json();

        if (data.found) {
            updateStatus(data);
            resultContainer.style.display = 'block';
            resultTitle.textContent = data.label;
            resultReason.textContent = data.reason;
        } else {
            alert(data.message || 'Tracking code not found');
            resultContainer.style.display = 'none';
        }
    } catch (error) {
        alert('Error: ' + error.message);
    } finally {
        trackBtn.disabled = false;
        trackBtn.textContent = 'Track Now';
    }
}

function updateStatus(data) {
    const steps = timeline.querySelectorAll('.step');
    
    steps.forEach((step, index) => {
        step.classList.remove('active', 'current');
        
        if (index < data.current_step) {
            step.classList.add('active');
        }
        
        if (index === data.current_step - 1) {
            step.classList.add('current');
        }
    });
}
</script>

</body>
</html>