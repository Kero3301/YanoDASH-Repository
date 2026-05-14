<!-- Private Archive Home Page -->
<!-- Assigned Member: Shannon -->
<?php
    session_start();

    require_once '../../src/loader.php';
    load (
        'vendor_autoload',
        'mongodb_client',
        'mongodb_collections',
        'authentication',
        'authorization',
        'doc_ed',
        'doc_query',
        'document_factory',
        'navbar',
        'document_list',
        'document_modal',
        'page_header'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms($permissions)) {
        die("You do not have permission to access this resource.");
    }

    $client = mongodb_client();
    
    $collection_documents = coll('documents', $client);

    $query = buildQuery($_SESSION['auth'], $_SESSION['access'], 'private');

    $results = $collection_documents->find($query);
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