<?php
session_start();

require_once dirname(dirname(__DIR__)). '/src/loader.php';

// Load utilities FIRST before using any functions
load (
    'authentication',
    'authorization',
    'navbar',
    'footer'
);

// Check if user is logged in
if (!is_logged_in()) {
   header('location: '. $app_url. '/auth/login.php');
   exit;
}

// Check if user has permission to use DMS
if (!can_use_dms($permissions))
    die("You do not have permission to access this resource.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        initialize_page("Request Document | YanoDASH");
    ?>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background: #f3f5f8;
        font-family: 'RobotoFlex', sans-serif;
    }

    .serif {
        font-family: 'Gupter', serif;
    }

    .sans {
        font-family: 'RobotoFlex', sans-serif;
    }

    .form-container {
        width: min(100%, 760px);
        margin: 24px auto;
        padding: 24px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 14px 35px rgba(0,0,0,0.08);
        border-top: 8px solid #2e7d32;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background-color: #ffffff;
        color: #333;
        text-decoration: none;
        border-radius: 999px;
        font-family: 'RobotoFlex', sans-serif;
        font-weight: bold;
        font-size: 14px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.25s ease;
        margin-bottom: 24px;
    }

    .btn-back:hover {
        background-color: #5f0000;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.12);
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 8px;
        color: #333;
        font-family: 'RobotoFlex', sans-serif;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 14px;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-sizing: border-box;
        font-size: 15px;
        font-family: 'RobotoFlex', sans-serif;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 4px rgba(46,125,50,0.08);
    }

    .file-input-wrapper {
        padding: 16px;
        border-radius: 12px;
        background-color: #f9fff9;
        text-align: center;
    }

    input[type="file"]::file-selector-button {
        background-color: #2e7d32;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        margin-right: 10px;
        font-weight: bold;
    }

    .btn-submit {
        background-color: #2e7d32;
        color: white;
        border: none;
        padding: 16px 20px;
        border-radius: 50px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        transition: background 0.3s ease, transform 0.2s ease;
        font-size: 16px;
        box-shadow: 0 6px 15px rgba(46, 125, 50, 0.18);
    }

    @media (max-width: 768px) {
        .form-container {
            width: calc(100% - 32px);
            margin: 18px auto;
            padding: 20px;
        }

        .btn-back {
            width: 100%;
            justify-content: center;
            padding: 12px 18px;
        }

        .form-group label {
            font-size: 13px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            font-size: 14px;
            padding: 12px;
        }

        .btn-submit {
            padding: 14px 18px;
            font-size: 15px;
        }
    }

    @media (max-width: 480px) {
        .form-container {
            width: calc(100% - 24px);
            padding: 18px;
            border-radius: 14px;
        }

        .btn-back {
            padding: 12px 16px;
        }

        .form-group label {
            margin-bottom: 6px;
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
        Request New Document Archiving
    </h2>
    
    <form action="process_request.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="notes">Document Tracking Code</label>
            <textarea id="notes" name="notes" rows="1" placeholder="Please refer to the document's tracking code"></textarea>
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

        <button type="submit" class="btn-submit">Submit Request</button>
    </form>

</div>

</body>
</html>