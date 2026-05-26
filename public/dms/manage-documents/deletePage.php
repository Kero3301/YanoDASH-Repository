<?php
session_start();
require_once '../../../bootstrap/app.php';
load('mongodb_client', 'mongodb_collections', 'authentication', 'authorization');

// 1. Security Check
if (!is_logged_in() || !can_use_dms($permissions)) {
    die("Unauthorized access.");
}

// 2. Validate the ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=missing_id');
    exit;
}

try {
    $client = mongodb_client();
    $collection = coll('documents', $client);

    // 3. Convert string ID to MongoDB ObjectId
    $targetId = new MongoDB\BSON\ObjectId($_GET['id']);

    // 4. Execute deletion
    $result = $collection->deleteOne(['_id' => $targetId]);

    if ($result->getDeletedCount() === 1) {
        // Success! Redirect back to index
        header('Location: index.php?status=deleted');
    } else {
        header('Location: index.php?status=not_found');
    }

} catch (Exception $e) {
    // This catches invalid ID formats or connection issues
    error_log($e->getMessage());
    header('Location: index.php?status=error');
}
exit;