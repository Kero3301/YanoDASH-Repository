<?php
session_start();
require_once '../../bootstrap/app.php';
use MongoDB\BSON\ObjectId;

$client = mongodb_client();
$db = $client->yano_dash;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action        = strtolower(trim($_POST['action'] ?? ''));
        $doc_id        = $_POST['doc_id'] ?? '';
        $tracking_code = $_POST['tracking_code'] ?? ''; 
        $redirect      = $_POST['redirect_to'] ?? 'archive-rq.php';

        if ($action === 'reject') {
            $new_status = 'EDITING';
        } else {
            $new_status = 'ARCHIVED';
        }

        $docFilter = [];
        if (!empty($doc_id)) {
            $docFilter = ['_id' => new ObjectId($doc_id)];
        } elseif (!empty($tracking_code)) {
            $docFilter = ['tracking_code' => $tracking_code];
        }

        if (!empty($docFilter)) {
            $db->documents->updateOne($docFilter, ['$set' => ['doc_status' => $new_status]]);
        }

        if (empty($tracking_code) && !empty($doc_id)) {
            $temp = $db->documents->findOne(['_id' => new ObjectId($doc_id)]);
            $tracking_code = $temp['tracking_code'] ?? '';
        }

        if (!empty($tracking_code)) {
            $db->archive_requests->deleteOne(['document_tc' => $tracking_code]);
        }

        header("Location: " . $redirect);
        exit;

    } catch (Exception $e) {
        die("ERROR: " . $e->getMessage());
    }
}