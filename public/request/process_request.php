<?php
session_start();

require_once '../../src/loader.php';
load (
    'authentication',
    'authorization',
    'vendor_autoload',
    'mongodb_client'
);

if (!is_logged_in() || !can_use_dms($identity))
    die("You do not have permission to access this resource.");

try {
    $client = mongodb_client(readOnly: false);
    $collection = $client->yano_dash->sample_mongodb_data;
} catch (Exception $e) {
    $_SESSION['error_msg'] = "MongoDB Connection Failed: " . $e->getMessage();
    header("Location: archive.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docType = $_POST['doc_type'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $notes   = $_POST['notes'] ?? '';
    $fileName = null;

    // Handle File Upload
    if (!empty($_FILES['docs']['name'])) {
        $fileName = time() . "_" . basename($_FILES['docs']['name']);
        $targetPath = "../uploads/" . $fileName;
        
        if (!is_dir("../uploads/")) {
            mkdir("../uploads/", 0777, true);
        }
        
        move_uploaded_file($_FILES['docs']['tmp_name'], $targetPath);
    }

    // Insert into MongoDB
    try {
        $result = $collection->insertOne([
            'docType'    => $docType,
            'purpose'    => $purpose,
            'notes'      => $notes,
            'file'       => $fileName,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);
        
        if ($result->getInsertedCount() > 0) {
            $_SESSION['success_msg'] = "Request processed successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to save to database.";
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = "Database error: " . $e->getMessage();
    }

    header("Location: archive.php");
    exit();
}
?>