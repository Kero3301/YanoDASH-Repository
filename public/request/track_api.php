<?php
session_start();
require_once dirname(dirname(__DIR__)). '/bootstrap/app.php';
load('authentication', 'authorization', 'mongodb_client', 'mongodb_collections');

header('Content-Type: application/json');

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

// Temporary test bypass: use this tracking code to simulate a completed request.
if ($tracking_code === 'TEST-BYPASS') {
    echo json_encode([
        'found' => true,
        'tracking_code' => $tracking_code,
        'status' => 'completed',
        'current_step' => 3,
        'label' => 'Archived (test)',
        'reason' => 'Bypassed tracking flow for testing',
        'doc_type' => 'Budget event',
        'purpose' => 'Test bypass'
    ]);
    exit;
}

try {
    $client = mongodb_client(readOnly: true);
    $collection = coll('archive_requests', $client);

    $request = $collection->findOne(['tracking_code' => $tracking_code]);

    if ($request === null) {
        echo json_encode(['found' => false, 'message' => 'Tracking code not found']);
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
        'doc_type' => $request['doc_type'] ?? $request['docType'] ?? 'Unknown',
        'purpose' => $request['purpose'] ?? ''
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['found' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}
?>