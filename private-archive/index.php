<!-- Private Archive Home Page -->
<!-- Assigned Member: Shannon -->
<?php
    session_start();

    require_once '../utils/doc_query.php';
    ini_set('display_errors', 'Off');

    require_once '../vendor/autoload.php';
    require_once '../utils/loader.php';

    load_components(
        'navbar',
        'document_list',
        'document_modal',
        'page_header'
    );
    load_utils(
        'authentication',
        'authorization'
    );
    load_utils(
        'data/DocEd',
        'document_factory'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms()) {
        die("You do not have permission to access this resource.");
    }

    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));

    $collection_documents = $client->yano_dash->documents_schema;
    $results = $collection_documents->find(
        [
            'doc_status' => 'ARCHIVED',
            'is_publicized' => false
        ]
    );
    $all_docs = get_all($results);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page('Private Archive | YanoDASH')?>    
        <link rel="stylesheet" href="../css/pages/docsss.css"/>
</head>
<body>    
    <?php echo navbar(0); ?>
    <?php echo page_header("Private Archive")?>

    <div id="docs-list-container">
        <div class="docs-grid" id="docsGrid">
            <?php list_all_documents($all_docs)?>
        </div>
        <h2 style="text-align: center">< Page x of y ></h2>
    </div>
    <?php echo document_modal()?>

    <script src="../script/documents-display.js"></script>

</body>
</html>