<?php
    session_start();
    require_once '../../../src/loader.php';
    load('authentication', 'authorization', 'navbar');

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    $message = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document_file'])) {
        // Dynamic credentials from the active session
        $user = urlencode($_SESSION['user_email'] ?? ''); 
        $pass = urlencode($_SESSION['user_password'] ?? ''); 
        $dbName = "yano_dash";
        
        try {
            $uri = "mongodb://{$user}:{$pass}@localhost:27017/{$dbName}?authSource=admin";
            $client = new MongoDB\Client($uri);
            $collection = $client->$dbName->documents;

            // Target the 'Uploads' folder in the Repository root
            $uploadDir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'Uploads' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileName = time() . "_" . basename($_FILES["document_file"]["name"]);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES["document_file"]["tmp_name"], $targetPath)) {
                $collection->insertOne([
                    'document_name' => $_POST['docname'],
                    'category'      => $_POST['categories'],
                    'file_name'     => $fileName,
                    'uploaded_by'   => $_SESSION['user_email'],
                    'upload_date'   => new MongoDB\BSON\UTCDateTime()
                ]);
                $message = "<span style='color: #2ecc71;'>✔ Upload Successful!</span>";
            }
        } catch (Exception $e) {
            $message = "<span style='color: #e74c3c;'>Error: " . $e->getMessage() . "</span>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page("Upload Document")?>
    <link rel="stylesheet" href="../../css/pages/adddocstyles.css">
    <style>
        .status-msg { margin-top: 15px; font-weight: bold; padding: 10px; border-radius: 5px; }
        .success { color: #2ecc71; background: rgba(46, 204, 113, 0.1); }
        .error { color: #e74c3c; background: rgba(231, 76, 60, 0.1); }
    </style>
</head>
<body>
    <?php echo navbar()?>

    <div class="container">
        <h2 style="font-family: 'Gupter'; font-weight: normal">New Document</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="section">
                <p style="color: white; font-family: 'RobotoFlex'">Document Name </p>
                <input type="text" name="docname" placeholder="Enter document title" required><br><br>
                
                <p style="color: white;">Category </p>
                <select name="categories" id="categories">
                    <option value="Financial">Financial</option>
                    <option value="Activity Design">Activity Design</option>
                    <option value="Minutes">Minutes</option>
                    <option value="Other">Other</option>
                </select>
                <br><br>

                <p style="color: white;">File Selection </p>
                <label for="fileUpload" class="custom-file-upload">Select PDF/DOCX</label>
                <input type="file" name="document_file" id="fileUpload" accept=".pdf,.doc,.docx,.txt" required>
                
                <div id="fileInfo" class="file-info">No file chosen</div>

                <button type="submit" class="upload-btn">Start Upload</button>
                
                <?php if ($message): ?>
                    <div class="status-msg"><?php echo $message; ?></div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('fileUpload');
        const fileInfo = document.getElementById('fileInfo');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileInfo.textContent = "Selected: " + this.files[0].name;
            } else {
                fileInfo.textContent = "No file chosen";
            }
        });
    </script>
</body>
</html>