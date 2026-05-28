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
    // 3. Convert string ID to MongoDB ObjectId
    $targetId = new MongoDB\BSON\ObjectId($_GET['id']);

    // 4. Execute deletion
    $result = coll('documents')
        ->deleteOne(['_id' => $targetId])
        ->execute();

    if (!empty($result) && QueryBuilder::getDeletedCount($result) === 1) {
        $relatedVersionsCount = coll('document_versions')
            ->countDocuments(['doc_id' => $targetId])
            ->execute();
        // 5. Clean up any related document versions linked to this document
        if ($relatedVersionsCount > 0) {
            $result2 = coll('document_versions')
                ->deleteMany(['doc_id' => $targetId])
                ->execute();
        }
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