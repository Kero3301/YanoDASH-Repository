<?php
    session_start();
    require_once '../../../src/loader.php';
    load('vendor_autoload', 'authentication', 'authorization', 'navbar', 'mongodb_client', 'mongodb_collections');

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
        
        $doc_title = $_POST['docname'];
        $doc_category = $_POST['categories'];
        $author = new MongoDB\BSON\ObjectId($identity['user_id']);
        $area_of_origin = $identity['department'];
        $yr = date('Y');
        $tcn = 'YD-'. date("Ymd"). date("His");

        try {
            $client = mongodb_client();
            $collection = coll('documents', $client);

            // Target the 'Uploads' folder in the Repository root
            $uploadDir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileName = time() . "_" . basename($_FILES["document_file"]["name"]);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES["document_file"]["tmp_name"], $targetPath)) {
                $result = $collection->insertOne([
                    "doc_title" => $doc_title,
                    "doc_category" => $doc_category,
                    "doc_tags" => [],
                    "author" => $author,
                    "area_of_origin" => $area_of_origin,
                    "doc_status" => "EDITING",
                    "tracking_code" => "$tcn",
                    "dates" => [
                        "date_added" => new MongoDB\BSON\UTCDateTime(),
                        "date_finalized" => null,
                        "date_archived" => null
                    ],
                    "current_version" => 1
                ]);
                if ($result) echo $result->getInsertedId();
                if ($result) $message = "<span style='color: #2ecc71;'>✔ Upload Successful!</span>";
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
                <select class="sct" name="categories" id="categories">
                    <option value="Activity Design">Activity Design</option>
                    <option value="Memorandum">Memorandum</option>
                    <option value="Minutes of Meeting">Minutes of Meeting</option> 
                    <option value="Notice of Meeting">Notice of Meeting</option> 
                    <option value="Project Proposal">Project Proposal </option>
                    <option value="Financial Statement">Financial Statement </option>
                    <option value="Accomplishment Report">Accomplishment Report </option>
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