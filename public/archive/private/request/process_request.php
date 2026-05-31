<?php
session_start();

require_once '../../bootstrap/app.php';
load (
    'authentication',
    'authorization',
    'vendor_autoload',
    'mailing',
    'mongodb',
    'text_utils'
);

// Check authentication and permissions
if (!is_logged_in() || !can_use_dms($permissions) || $identity['user_id'] === null)
    die("You do not have permission to access this resource.");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purpose  = trim($_POST['purpose'] ?? '');
    $notes    = trim($_POST['notes'] ?? '');

    // Look for an existing document in the documents collection to verify if the document actually exists
    $existingDocument = coll('documents')->findOne(['tracking_code' => $notes])->execute();
    if (empty($existingDocument)) {
        $_SESSION['error_msg'] = "Invalid document tracking code. No matching document was found in the records.";
        header("Location: archive.php");
        exit();
    }

    $documentTC = $existingDocument['tracking_code'];

    // Check if an archive request already exists for this document
    $existingArchiveRequest = coll('archive_requests')->findOne([
        'notes' => $documentTC
    ])->execute();
    if (!empty($existingArchiveRequest)) {
        $_SESSION['error_msg'] = "An archive request for this document is already underway.";
        header("Location: archive.php");
        exit();
    }

    // Check if document is already archived or publicized
    if (
        isset($existingDocument['doc_status']) &&
        in_array(
            strtoupper($existingDocument['doc_status']),
            ['ARCHIVED', 'PUBLICIZED']
        )
    ) {
        $_SESSION['error_msg'] = "That document has already been archived.";
        header("Location: archive.php");
        exit();
    }

    $documentCategory = $existingDocument['doc_category'];
    $fileName = null;

    // Assign document type / category automatically on the server.
    // This value is never shown in the frontend form.
    $patterns = [
        'Activity Design' => '/activity|design|program/i',
        'Memorandum' => '/memorandum|memo/i',
        'Minutes of Meeting' => '/minutes|meeting|mom/i',
        'Notice of Meeting' => '/notice|announcement|advisory/i',
        'Project Proposal' => '/proposal|project|plan/i',
        'Financial Statement' => '/financial|statement|budget|finance|fund|expense/i',
        'Accomplishment Report' => '/accomplishment|report|summary|completion/i',
        'Merchandise-related document' => '/merchandise|product|item|inventory/i',
    ];

    // Try to match pattern
    $docType = null;
    foreach ($patterns as $type => $pattern) {
        if (preg_match($pattern, $purpose)) {
            $docType = $type;
            break;
        }
    }
    
    // FIXED: If no pattern matched, check if user provided a custom doc type
    if (!$docType && !empty($_POST['custom_doc_type'])) {
        $docType = htmlspecialchars(trim($_POST['custom_doc_type']));
    } elseif (!$docType) {
        // Fallback to using purpose as docType
        $docType = 'Custom: ' . htmlspecialchars($purpose); 
    }

    // Handle File Upload
    if (!empty($_FILES['docs']['name'])) {
        $fileName = time() . "_" . basename($_FILES['docs']['name']);
        $targetPath = "../../uploads/" . $fileName;
        
        if (!is_dir("../../uploads/")) {
            mkdir("../../uploads/", 0777, true);
        }
        
        move_uploaded_file($_FILES['docs']['tmp_name'], $targetPath);
    }

    // Generate a public tracking code for this request
    $year = date('Y');
    $month = date('m');
    $code = generate_six_char_code();
    $tracking_code = 'AR-'. $year. '-'. $month. '-'. $code;
    $userID = $identity['user_id'];

    // Insert into MongoDB
    try {
        $result = coll('archive_requests')->insertOne([
            'docType'       => "test",
            'purpose'       => $purpose,
            'notes'         => $notes,
            'file'          => $fileName,
            'tracking_code' => $tracking_code,
            'status'        => 'pending',
            'requested_by'  => new MongoDB\BSON\ObjectId($userID),
            'created_at'    => new MongoDB\BSON\UTCDateTime()
        ])->execute();

        $success = false;
        if (QueryBuilder::getInsertedId($result) !== null) {
            $update = coll('documents')
                ->updateOne(
                    ['tracking_code' => $notes],
                    [
                        '$set' => ['doc_status' => 'PENDING ARCHIVAL']
                    ]
                )
                ->execute();
            if (!empty($update)) $success = true;
        }
        
        if ($success) {
            $_SESSION['success_msg'] = "Request created successfully! Your tracking code is {$tracking_code}. Use this code to check status in Track Request.";
            send_simple_email("ddpyu01202401015@usep.edu.ph", "[YanoDASH] Your archive request's tracking code", "Your archive request's tracking code is {$tracking_code}. Use it in the Track Request page to track the status of your request. Thank you!");
        } else {
            $_SESSION['error_msg'] = "Failed to create request. Please try again.";
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = "Database error: " . $e->getMessage();
    }

    header("Location: archive.php");
    exit();
}
?>