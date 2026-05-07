<?php
session_start();

require_once '../vendor/autoload.php';

// MongoDB Atlas Connection
$atlasConnection = "mongodb+srv://yanoDash_RW:yanodash35278@cluster0.psn5zxh.mongodb.net/?appName=Cluster0";

try {
    $client = new MongoDB\Client($atlasConnection);
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