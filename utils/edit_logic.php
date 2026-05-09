<?php
session_start();
require_once 'directory.php';
require_once dirname(__DIR__). '/vendor/autoload.php';

$client = new MongoDB\Client(getenv('YANODASH_RW_DBU_URI'));
$collection_documents = $client->yano_dash->documents_schema;
$collection_documentVersions = $client->yano_dash->document_versions;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    # Get the posted doc_id
    $docID = $_POST['doc_id'];
    $_id = new MongoDB\BSON\ObjectId($docID);

    # Find the related document on the database based on its docID
    $document = $collection_documents->findOne([
        '_id' => $_id
    ]);

    # Get current data about the document in the database
    $tracking_code = $document->tracking_code;
    $current_version = $document->current_version_id;

    # Get the updated data
    $new_title = $_POST['doc_title'];

    # Default doc update info, assuming no new file is uploaded
    $docUpdate = [
        '$set' => [
            'doc_title' => $new_title
        ]
    ];

    # If a new file is included/uploaded, create a new document version and link it to the document
    if (isset($_FILES['new_file']) && $_FILES['new_file']['error'] === UPLOAD_ERR_OK) {
        $new_version = $current_version + 1;
        $extension = pathinfo($_FILES['new_file']['name'], PATHINFO_EXTENSION);

        # Set the new filename based on tracking code, new version, and file extension
        $new_filename = $tracking_code . "_v" . $new_version . "." . $extension;

        # Specify the directory where new files will go
        $upload_path = '../uploads/' . $new_filename;

        if (move_uploaded_file($_FILES['new_file']['tmp_name'], $upload_path)) {
            # Increment version

            # Define current date as date added
            $now = new DateTime("now", new DateTimeZone("UTC"));
            $date_added = new MongoDB\BSON\UTCDateTime(
                $now->getTimestamp() * 1000
            );

            # Create and insert new version for the document
            $version = [
                'doc_id' => $_id,
                'version_number' => $new_version,
                'file_path' => $upload_path,
                'date_added' => $date_added
            ];
            $res = $collection_documentVersions->insertOne($version);
            if ($res) {
                # Change the doc update info to include the version
                $docUpdate = [
                    '$set' => [
                        'doc_title' => $new_title,
                        'current_version_id' => $new_version
                    ]
                ];
            }
        } 
    }
    // } else {

    //     header("Location: $app_url/dms/manage-documents?success=metadata_updated");
    // }

    $res2 = $collection_documents->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($docID)],
        $docUpdate
    );

    if ($res2) header("Location: $app_url/dms/manage-documents?success=new_version");
}

?>