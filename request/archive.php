<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php initializePage("Request Document | YanoDASH")?>
    <!-- <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Document | YanoDASH</title> -->

    <style>
        /* body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #ffffff;
        } */

        .form-container {
            max-width: 750px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-top: 8px solid #2e7d32; /* Green accent from your menu suggestion */
        }

        /* UI Styled Back Button */
        .btn-back {
            display: inline-block;
            padding: 10px 25px;
            background-color: #ffffff;
            color: #333;
            text-decoration: none;
            border-radius: 50px;
            font-family: Arial, sans-serif;
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
            font-family: Arial, sans-serif;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            box-sizing: border-box;
            font-size: 14px;
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
    </style>
</head>
<body>

<?php echo navbar(0); ?>

<div class="form-container">
    <a href="request.php" class="btn-back">Back to Menu</a>

    <h2 style="text-align: center; font-family: 'Times New Roman'; margin-bottom: 30px; color: #2e7d32;">Request a New Document</h2>
    
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
                <p style="font-size: 12px; color: #666; margin-top: 8px; font-family: Arial;">Max file size: 5MB (JPG, PNG, PDF)</p>
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