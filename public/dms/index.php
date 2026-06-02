<?php
    session_start();
    // error_reporting(0);

    require_once '../../bootstrap/app.php';
    load (
        'authenticator',
        'authorizer',
        'mongodb',
        'document_factory',
        'navbar',
        'document_list',
        'footer',
        'doc_query'
    );

    if (!Authenticator::isLoggedIn()) {
        header('location: '. $app_url. '/auth/login.php?redirect=dms');
        exit;
    }

    if (!Authorizer::canUseDMS($_CURRENTUSER))
        die("You do not have permission to access this resource.");

    $baseQuery = [
        'doc_status' => 'EDITING',
        'area_of_origin' => [
            '$in' => $_CURRENTUSER['PERMISSIONS']['access_domains']
        ]
    ];

    if (Authorizer::isOSCPresident($_CURRENTUSER)) {
        $baseQuery = [
            'doc_status' => 'EDITING'
        ];
    }

    $documentsPerPage = 20;

    $totalDocuments = coll('documents')
        ->countDocuments($baseQuery)
        ->execute();
    $totalPages = (int) max(1, ceil($totalDocuments / $documentsPerPage));
    $currentPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 1,
            'min_range' => 1
        ]
    ]);

    $currentPage = min($currentPage, $totalPages);
    $skip = (int)(($currentPage - 1) * $documentsPerPage);
    // $results = coll('documents')
    //     ->find($baseQuery)
    //     ->sort(['dates.date_added' => -1])
    //     ->skip($skip)
    //     ->limit($documentsPerPage)
    //     ->execute();
    
    $query = fn($_)=> $_
        ->find($baseQuery)
        ->sort(['dates.date_added' => -1])
        ->skip($skip)
        ->limit($documentsPerPage)
        ->execute();

    $all_docs = DocQuery::get($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php initialize_page("DMS Portal | YanoDASH ")?>
    <link rel="stylesheet" href="../css/pages/dmsstyle.css">
</head>
<body>
    <?php echo navbar($_CURRENTUSER);?>

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