<?php
    session_start();
    require_once '../../src/loader.php';
    load (
        'vendor_autoload',
        'mongodb_client',
        'doc_ed',
        'document_factory',
        'navbar',
        'document_list',
        'document_modal',
        'page_header',
        'pagination_controls'
    );

    $client = mongodb_client();

    $collection_documents = $client->yano_dash->documents_schema;

    $documentsPerPage = 16;
    $totalDocuments = $collection_documents->countDocuments([
        'is_publicized' => true
    ]);
    $totalPages = (int) max(1, ceil($totalDocuments / $documentsPerPage));
    $currentPage = isset($_GET['page'])
        ? (int) $_GET['page']
        : 1;

    $currentPage = max(1, min($currentPage, $totalPages));

    $skip = ($currentPage - 1) * $documentsPerPage;

    $results = $collection_documents->find(
        ['is_publicized' => true],
        [
            'skip' => $skip,
            'limit' => $documentsPerPage
        ]
    );

    $all_docs = get_all($results);
?>
<!DOCTYPE html>
<html>
<head>
    <?php initialize_page("All Documents | YanoDASH")?>
    <link rel="stylesheet" href="../css/pages/docsss.css"/>
</head>
<body>
    <?php echo navbar()?>
    <?php echo page_header("Documents")?>

    <div id="docs-list-container">
        <div class="docs-grid" id="docsGrid">
            <?php list_all_documents($all_docs)?>
        </div>
        <?php echo pagination_controls($currentPage, $totalPages)?>
    </div>
    <?php echo document_modal()?>

    <script src="../script/documents-display.js"></script>
</body>
</html>