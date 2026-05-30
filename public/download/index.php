<?php
require_once '../../bootstrap/app.php';

load(
    'authentication',
    'authorization',
    'vendor_autoload',
    'mongodb',
    'text_utils'
);

if (!isset($_GET['file_id'])) {
    http_response_code(400);
    exit('Missing version file ID');
}

try {
    global $identity;
    global $permissions;

    $versionID = new MongoDB\BSON\ObjectId($_GET['file_id']);
    $originalFile = coll('document_versions')
        ->findOne(['_id' => $versionID])
        ->execute();
    if (empty($originalFile)) {
        http_response_code(404);
        exit('404 Document file not found');
    }

    $originalDocID = $originalFile['doc_id'];
    $originalDoc = coll('documents')
        ->findOne(['_id' => new MongoDB\BSON\ObjectId($originalDocID)])
        ->execute();
    if (empty($originalDoc)) {
        http_response_code(404);
        exit('404 Original document not found');
    }
    $areaOfOrigin = (string) $originalDoc['area_of_origin'];

    if (!can_access($identity, $permissions, ['domain' => [$areaOfOrigin]])) {
        http_response_code(403);
        exit('403 Forbidden access to document resource');
    }

    $filePath = ROOT . $originalFile['file_path'];
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);

    if (!file_exists($filePath)) {
        http_response_code(404);
        exit('404 File missing');
    }

    $originalDocTitle = $originalDoc['doc_title'];
    $filename = normalize_title_for_download($originalDocTitle). '-v'. $originalFile['version_number']. '.'. $extension;

    $mime = mime_content_type($filePath);

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-cache');
    header('Pragma: public');

    readfile($filePath);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    exit('500 Server error');
}