<?php
session_start();
require_once '../../bootstrap/app.php';
load('mongodb_collections');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action        = strtolower(trim($_POST['action'] ?? ''));
        $tracking_code = $_POST['tracking_code'] ?? ''; 
        $requestID     = $_POST['archive_id'];
        $redirect      = $_POST['redirect_to'] ?? 'archive-rq.php';

        if ($action === 'reject') {
            $new_status = 'EDITING';
        } else {
            $new_status = 'ARCHIVED';
        }

        $request = coll('archive_requests')
            ->findOne(['_id' => $requestID])
            ->execute();
        $document = coll('documents')
            ->findOne(['tracking_code' => $request['notes']])
            ->execute();
        $extractedRequestID = new MongoDB\BSON\ObjectId($request['_id']);
        $extractedDocumentID = new MongoDB\BSON\ObjectId($document['_id']);

        if ($action === 'reject') {
            coll('archive_requests')
                ->updateOne(
                    ['_id' => $extractedRequestID],
                    [
                        '$set' => ['status' => 'rejected']
                    ]
                )
                ->execute();
            coll('documents')
                ->updateOne(
                    ['_id' => $extractedDocumentID],
                    [
                        '$set' => ['doc_status' => 'EDITING']
                    ]
                )
                ->execute();
        } else {
            coll('archive_requests')
                ->updateOne(
                    ['_id' => $extractedRequestID],
                    [
                        '$set' => ['status' => 'approved']
                    ]
                )
                ->execute();
            coll('documents')
                ->updateOne(
                    ['_id' => $extractedDocumentID],
                    [
                        '$set' => ['doc_status' => 'ARCHIVED']
                    ]
                )
                ->execute();
        }

        // if (!empty($docFilter)) {
        //     $db->documents->updateOne($docFilter, ['$set' => ['doc_status' => $new_status]]);
        // }

        // if (empty($tracking_code) && !empty($doc_id)) {
        //     $temp = $db->documents->findOne(['_id' => new ObjectId($doc_id)]);
        //     $tracking_code = $temp['tracking_code'] ?? '';
        // }

        // if (!empty($tracking_code)) {
        //     $db->archive_requests->deleteOne(['document_tc' => $tracking_code]);
        // }

        header("Location: " . $redirect);
        exit;

    } catch (Exception $e) {
        die("ERROR: " . $e->getMessage());
    }
}