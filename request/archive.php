<?php
    session_start();

    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php initializePage("Request Document | YanoDASH")?>

<style>
/* ✅ GLOBAL FONT SYSTEM */
body {
    font-family: 'RobotoFlex', sans-serif;
}

.serif {
    font-family: 'Gupter', serif;
}

.sans {
    font-family: 'RobotoFlex', sans-serif;
}

/* FORM CONTAINER */
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

/* BACK BUTTON (UNCHANGED except font consistency fix optional) */
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

/* FORM FIELDS */
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

/* FILE UPLOAD */
.file-input-wrapper {
    padding: 15px;
    border-radius: 8px;
    background-color: #f9fff9;
    text-align: center;
}

/* FILE BUTTON (unchanged) */
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

/* SUBMIT BUTTON (UNCHANGED as requested) */
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

/* RESPONSIVE */
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

<div class="form-container">

    <a href="request.php" class="btn-back">Back to Menu</a>

    <h2 class="serif" style="text-align: center; margin-bottom: 30px; color: #2e7d32;">
        Request a New Document
    </h2>
    
    <form action="process_request.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="doc_type">Document Type</label>
            <select id="doc_type" name="doc_type" required>
                <option value="" disabled selected>-- Please choose a document type --</option>
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
            <textarea id="notes" name="notes" rows="1" placeholder="Please refer to the document ID" required></textarea>
        </div>

        <button type="submit" class="btn-submit">Submit Request</button>
    </form>

</div>

</body>
</html>