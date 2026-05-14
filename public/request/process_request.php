<?php
session_start();

require_once '../../src/loader.php';
load (
    'authentication',
    'authorization',
    'vendor_autoload',
    'mongodb_client',
    'mongodb_collections'
);

// Check authentication and permissions
if (!is_logged_in() || !can_use_dms($permissions))
    die("You do not have permission to access this resource.");

try {
    $client = mongodb_client(readOnly: false);
    $collection = coll('archive_requests', $client);
    $documentsCollection = coll('documents', $client);
} catch (Exception $e) {
    $_SESSION['error_msg'] = "MongoDB Connection Failed: " . $e->getMessage();
    header("Location: archive.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purpose  = trim($_POST['purpose'] ?? '');
    $notes    = trim($_POST['notes'] ?? '');

    // Look for an existing document in the documents collection to verify if the document actually exists
    $existingDocument = $documentsCollection->findOne(['tracking_code' => $notes]);
    if (!$existingDocument) {
        $_SESSION['error_msg'] = "Invalid document tracking code. No matching document was found in the records.";
        header("Location: archive.php");
        exit();
    }

    $documentTC = $existingDocument->tracking_code;

    // Check if an archive request already exists for this document
    $existingArchiveRequest = $collection->findOne([
        'notes' => $documentTC
    ]);
    if ($existingArchiveRequest) {
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

    $documentCategory = $existingDocument->doc_category;
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
        $targetPath = "../uploads/" . $fileName;
        
        if (!is_dir("../uploads/")) {
            mkdir("../uploads/", 0777, true);
        }
        
        move_uploaded_file($_FILES['docs']['tmp_name'], $targetPath);
    }

    // Generate a public tracking code for this request
    $year = date('Y');
    $count = $collection->countDocuments();
    $tracking_code = sprintf('YD-%s-%03d', $year, $count + 1);

    // Insert into MongoDB
    try {
        $result = $collection->insertOne([
            'docType'       => $docType,
            'purpose'       => $purpose,
            'notes'         => $notes,
            'file'          => $fileName,
            'tracking_code' => $tracking_code,
            'status'        => 'pending',
            'created_at'    => new MongoDB\BSON\UTCDateTime()
        ]);
        
        if ($result->getInsertedCount() > 0) {
            $_SESSION['success_msg'] = "Request processed successfully! Your tracking code is {$tracking_code}. Use this code to check status in Track Request.";
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