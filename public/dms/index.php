<?php
    session_start();
    error_reporting(0);


    require_once '../../bootstrap/app.php';
    load (
        'authentication',
        'authorization',
        'mongodb_client',
        'mongodb_collections',
        'document_factory',
        'navbar',
        'document_list',
        'footer'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms($permissions))
        die("You do not have permission to access this resource.");

    $client = mongodb_client();
    $collection_documents = coll('documents', $client);

    $baseQuery = [
        'doc_status' => 'EDITING',
        'area_of_origin' => [
            '$in' => $permissions['access_domains']
        ]
    ];

    if (is_president($identity, $permissions)) {
        $baseQuery = [
            'doc_status' => 'EDITING'
        ];
    }

    $documentsPerPage = 15;

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
<html lang="en">
<head>
    <?php initialize_page("DMS Portal | YanoDASH ")?>
    <link rel="stylesheet" href="../css/pages/dmsstyle.css">
</head>
<body>
    <?php echo navbar();?>

    <div class="page-contents no-padding">
        <div class="pch">
        <h1 style="margin-bottom: 32px; text-align: center">Document Management System</h1>
        </div>

        <div class="main-wrapper">
        <h2 style="text-decoration: underline red; text-align: center">Recent Documents</h2><br>
        <a href="add-document/" class="btn" style="width: 200px; margin: auto; display: block; background: black; color: white">Add a New Document</a>
        <br><br>
        <div class="document-grid">
            <?php if (count($all_docs) === 0):?>
                <div class="no-document-found-wrapper">
                    <div class="no-document-found-indicator">
                        <div class="no-document-found-logo"></div>
                        <h2 class="subtitle" style="user-select: none">No documents found</h2>
                    </div>
                </div>
            
            <?php else: ?>
                <?php list_all_documents($all_docs)?>
            <?php endif?>
        </div>
    </div>
    </div>
    <?php echo footer()?>
</body>
</html>