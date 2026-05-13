<?php
session_start();
require_once dirname(dirname(__DIR__)). '/src/loader.php';
load('authentication', 'authorization', 'mongodb_client');

header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['found' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['found' => false, 'message' => 'Method not allowed']);
    exit;
}

$tracking_code = trim($_POST['tracking_code'] ?? '');

if ($tracking_code === '') {
    echo json_encode(['found' => false, 'message' => 'Tracking code is required']);
    exit;
}

try {
    $client = mongodb_client(true);
    $collection = $client->yano_dash->archive_requests;

    $request = $collection->findOne(['tracking_code' => $tracking_code]);

    if ($request === null) {
        echo json_encode(['found' => false, 'message' => 'Document not found']);
        exit;
    }

    $status = $request['status'] ?? 'pending';
    $status_map = [
        'pending' => [
            'step' => 1,
            'label' => 'Waiting for Review',
            'reason' => 'Your request is still in the queue for admin review.'
        ],
        'in_review' => [
            'step' => 2,
            'label' => 'Being Processed',
            'reason' => 'Your document is currently being prepared for archival.'
        ],
        'completed' => [
            'step' => 3,
            'label' => 'Archived',
            'reason' => 'Your document has been archived successfully.'
        ]
    ];

    if (!isset($status_map[$status])) {
        $status = 'pending';
    }

    $info = $status_map[$status];

    echo json_encode([
        'found' => true,
        'tracking_code' => $tracking_code,
        'status' => $status,
        'current_step' => $info['step'],
        'label' => $info['label'],
        'reason' => $info['reason'],
        'doc_type' => $request['doc_type'] ?? 'Unknown',
        'purpose' => $request['purpose'] ?? ''
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['found' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}
?>