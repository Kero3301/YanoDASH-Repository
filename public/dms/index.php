<?php
    session_start();

    require_once '../../src/loader.php';
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
        <div class="main-wrapper">
        <h1 style="color: maroon; margin-bottom: 32px; text-align: center">Document Management System</h1>

        <h1>Recent Documents</h1><br>
        <div class="document-grid">
            <?php list_all_documents($all_docs)?>
        </div>
    </div>
    </div>
    <?php echo footer()?>
</body>
</html>