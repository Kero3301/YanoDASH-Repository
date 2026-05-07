<?php
    session_start();

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
        'data/DocEd',
        'document_factory'
    );
    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));

    $collection_documents = $client->yano_dash->documents_schema;
    $results = $collection_documents->find(
        [
            'doc_status' => 'ARCHIVED',
            'is_publicized' => true
        ],
        [
            'sort' => ['date_added' => -1], // newest first
            'limit' => 3
        ]
    );
    $all_docs = get_all($results);
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page("Latest Releases | YanoDASH")?>
    <link rel="stylesheet" href="../css/pages/docsss.css"/>
</head>
<body>
    <?php echo navbar(1) ?>
    <?php echo page_header("Latest Releases")?>

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