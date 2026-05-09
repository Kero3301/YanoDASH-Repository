<?php
session_start();

// MANUALLY SET SESSION FOR TESTING - MUST BE BEFORE AUTHENTICATION CHECKS
// $_SESSION['userID'] = 'test_user_id';
// $_SESSION['username'] = 'test_admin';
// $_SESSION['role'] = 'admin';

require_once dirname(dirname(__DIR__)). '/src/loader.php';

// Load utilities FIRST before using any functions
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

if (!can_use_dms($identity))
    die("You do not have permission to access this resource.");

// OPTIONAL: Add a debug message to confirm bypass is working
// echo "<!-- Development Mode: Authentication Bypassed -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    // Make sure initializePage function exists
    if (function_exists('initializePage')) {
        initializePage("Request Document | YanoDASH");
    } else {
        echo "<title>Request Document | YanoDASH</title>";
    }
    ?>

<style>
/* Your existing styles remain the same */
body {
    font-family: 'RobotoFlex', sans-serif;
}

.serif {
    font-family: 'Gupter', serif;
}

.sans {
    font-family: 'RobotoFlex', sans-serif;
}

.form-container.serif {
    font-family: 'Gupter', serif;
}

.form-container {
    max-width: 750px;
    margin: 50px auto;
    background: #ffffff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-top: 8px solid #2e7d32;
}

.btn-back {
    display: inline-block;
    padding: 10px 25px;
    background-color: #ffffff;
    color: #333;
    text-decoration: none;
    border-radius: 50px;
    font-family: 'RobotoFlex', sans-serif;
    font-weight: bold;
    font-size: 14px;
    border: 1px solid #ddd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    margin-bottom: 25px;
}

.btn-back:hover {
    background-color: #5f0000;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
    font-family: 'RobotoFlex', sans-serif;
}

.form-group input, 
.form-group select, 
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-sizing: border-box;
    font-size: 14px;
    font-family: 'RobotoFlex', sans-serif;
}

.file-input-wrapper {
    padding: 15px;
    border-radius: 8px;
    background-color: #f9fff9;
    text-align: center;
}

input[type="file"]::file-selector-button {
    background-color: #2e7d32;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 10px;
    font-weight: bold;
}

.btn-submit {
    background-color: #2e7d32;
    color: white;
    border: none;
    padding: 16px 30px;
    border-radius: 50px;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    transition: background 0.3s;
    font-size: 16px;
    box-shadow: 0 4px 10px rgba(46, 125, 50, 0.2);
}

.btn-submit:hover {
    background-color: #1b5e20;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(46, 125, 50, 0.3);
}

@media (max-width: 768px) {
    .form-container {
        width: 80%;
        max-width: 650px;
        padding: 30px;
        margin: 50px auto;
    }
}

@media (max-width: 1024px) {
    .form-container {
        width: 80%;
        max-width: 650px;
        padding: 30px;
        margin: 50px auto;
    }
}
</style>
</head>

<body>

<?php echo navbar(0); ?>

<!-- Add this message display section -->
<?php if (isset($_SESSION['success_msg'])): ?>
    <div style="max-width: 750px; margin: 20px auto; background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745; text-align: center;">
        <?php 
        echo htmlspecialchars($_SESSION['success_msg']);
        unset($_SESSION['success_msg']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
    <div style="max-width: 750px; margin: 20px auto; background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545; text-align: center;">
        <?php 
        echo htmlspecialchars($_SESSION['error_msg']);
        unset($_SESSION['error_msg']);
        ?>
    </div>
<?php endif; ?>

<div class="form-container">

    <a href="index.php" class="btn-back">Back to Menu</a>

    <h2 class="serif" style="text-align: center; margin-bottom: 30px; color: #2e7d32;">
        Request a New Document
    </h2>
    
    <form action="process_request.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="doc_type">Document Type</label>
            <select id="doc_type" name="doc_type">
                <option value="Voting Registration">Voting Registration</option>
                <option value="Required attendance">Required attendance</option>
                <option value="Budget event">Budget event</option>
                <option value="Other">Others</option>
            </select>
        </div>

        <div class="form-group">
            <label for="purpose">Purpose</label>
            <input type="text" id="purpose" name="purpose" placeholder="e.g. Inform colleges" required>
        </div>

        <div class="form-group">
            <label for="docs">Supporting Documents (Optional)</label>
            <div class="file-input-wrapper">
                <input type="file" id="docs" name="docs" accept=".jpg,.png,.pdf">
                <p class="sans" style="font-size: 12px; color: #666; margin-top: 8px;">
                    Max file size: 5MB (JPG, PNG, PDF)
                </p>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Document ID</label>
            <textarea id="notes" name="notes" rows="1" placeholder="Please refer to the document ID"></textarea>
        </div>

        <button type="submit" class="btn-submit">Submit Request</button>
    </form>

</div>

</body>
</html>