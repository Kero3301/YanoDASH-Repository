<?php
session_start();

require_once '../../src/loader.php';
load (
    'vendor_autoload',
    'mongodb_client',
    'mongodb_collections',
    'doc_ed',
    'document_factory',
    'navbar',
    'document_list',
    'document_modal',
    'page_header',
    'footer',
    'pagination_controls'
);


    $client = mongodb_client();

    $collection_documents = $client->yano_dash->documents_schema;
    $results = $collection_documents->find(
        [
            'doc_status' => 'ARCHIVED',
            'is_publicized' => true
        ],
        [
            'sort' => ['date_added' => -1],
            'limit' => 3
        ]
    );
    $all_docs = get_all($results);
$client = mongodb_client();
$collection_documents = coll('documents', $client);

$baseQuery = [
    'doc_status' => 'PUBLICIZED',
];

$documentsPerPage = 8;

$totalDocuments = $collection_documents->countDocuments($baseQuery);

$totalPages = (int) max(1, ceil($totalDocuments / $documentsPerPage));

$currentPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
    'options' => [
        'default' => 1,
        'min_range' => 1
    ]
]);

$currentPage = min($currentPage, $totalPages);

$skip = (int)(($currentPage - 1) * $documentsPerPage);

$results = $collection_documents->find(
    $baseQuery,
    [
        'sort' => ['dates.date_added' => -1],
        'skip' => $skip,
        'limit' => $documentsPerPage
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
    </div>

    <?php echo footer()?>
    <?php echo document_modal()?>
   

    <script src="../script/documents-display.js"></script>

</body>
</html>