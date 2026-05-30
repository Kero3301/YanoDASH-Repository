<?php
session_start();
require_once '../../../bootstrap/app.php';
load (
    'authentication',
    'authorization',
    'vendor_autoload',
    'mongodb',
    'text_utils'
);

if (!is_logged_in() || !can_use_dms($permissions))
    die("You do not have permission to access this resource.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    # Get the posted doc_id
    $docID = $_POST['doc_id'];
    $_id = new MongoDB\BSON\ObjectId($docID);

    # Find the related document on the database based on its docID
    $document = coll('documents')
        ->findOne(['_id' => $_id])
        ->execute();

    if (empty($document)) {
        $_SESSION['errmsg'] = "Sorry, the original document was no longer found in the database.";
        header('Location: editPage.php');
        exit();
    }

    # Get current data about the document in the database
    $tracking_code = $document['tracking_code'];
    $current_version = $document['current_version'];

    # Get highest version of document
    $highestVersion = 0;

    $versions = coll('document_versions')
        ->find(['doc_id' => $_id])
        ->execute();

    foreach ($versions as $v) {
        $highestVersion = max(
            $highestVersion,
            $v['version_number']
        );
    }

    # Get the updated data
    $new_title = $_POST['doc_title'];
    $new_category = $_POST['category'];
    $selectedVersion = $_POST['selected_version'] ?? '';


    # Default doc update info, assuming no new file is uploaded
    $docUpdate = [
        '$set' => [
            'doc_title' => $new_title,
            'doc_category' => $new_category
        ]
    ];

    $newVersionUploaded = false;


    # If a new file is included/uploaded, create a new document version and link it to the document
    if (isset($_FILES['new_file']) && $_FILES['new_file']['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['new_file']['name'], PATHINFO_EXTENSION);

        # Increment version
        $new_version = $highestVersion + 1;

        # Set the new filename based on tracking code, new version, and file extension
        $new_filename = $tracking_code . "_v" . $new_version . "_" . normalize_title_for_download($new_title) . "." . $extension;

        # Specify the directory where new files will go
        $upload_path = '../../../uploads/' . $new_filename;

        if (move_uploaded_file($_FILES['new_file']['tmp_name'], $upload_path)) {
            # Define current date as date added
            $now = new DateTime("now", new DateTimeZone("UTC"));
            $date_added = new MongoDB\BSON\UTCDateTime($now->getTimestamp() * 1000);
            $filePath = "/uploads/$new_filename";

            # Create and insert new version for the document
            $version = [
                'doc_id' => $_id,
                'version_number' => $new_version,
                'file_path' => $filePath,
                'date_added' => $date_added
            ];
            $res = coll('document_versions')
                ->insertOne($version)
                ->execute();
            if (!empty($res)) {
                $newVersionUploaded = true;
                $docUpdate['$set']['current_version'] = $new_version;
            }
        } 
    }
  
    if (!$newVersionUploaded && !empty($selectedVersion)) {
        $docUpdate['$set']['current_version']
            = (int)$selectedVersion;
    }

   
    $res2 = coll('documents')
        ->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($docID)],
            $docUpdate
        )
        ->execute();

    if (!empty($res2)) header("Location: $app_url/dms/manage-documents?success=new_version");
}

?>