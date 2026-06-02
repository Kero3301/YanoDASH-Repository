<!-- Manage Documents -->
<!-- Assigned Member: Shannon -->

<?php
    require_once '../../../bootstrap/app.php';

    load (
        'vendor_autoload',
        'mongodb',
        'authenticator',
        'authorizer',
        'navbar',
        'doc_query',
        'pagination_controls'
    );   

    if (!Authenticator::isLoggedIn()) {
        header('location: '. $app_url. '/auth/login.php?redirect=dms/manage-documents/index.php');
        exit;
    }

    if (!Authorizer::canUseDMS($_CURRENTUSER))
        die("You do not have permission to access this resource.");

    $baseQuery = [
        'doc_status' => [
                '$in' => ['EDITING', 'PENDING ARCHIVAL']
            ],
        'area_of_origin' => [
            '$in' => $_CURRENTUSER['PERMISSIONS']['access_domains']
        ]
    ];

    if (Authorizer::isOSCPresident($_CURRENTUSER)) {
        $baseQuery = [
            'doc_status' => [
                '$in' => ['EDITING', 'PENDING ARCHIVAL']
            ]
        ];
    }

    $documentsPerPage = 10;
    $totalDocuments = coll('documents')
        ->countDocuments($baseQuery)
        ->execute();

    $totalPages = (int) max(1, ceil($totalDocuments / $documentsPerPage));
    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $currentPage = max(1, min($currentPage, $totalPages));

    $skip = (int)(($currentPage - 1) * $documentsPerPage);

    $query = fn ($_)=> $_
        ->find($baseQuery)
        ->sort(['dates.date_added' => -1])
        ->skip($skip)
        ->limit($documentsPerPage)
        ->execute();

    $documents = DocQuery::get($query);
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Manage Documents | YanoDASH')?>
	<link rel="stylesheet" type="text/css" href="../../css/pages/managestyle.css">
</head>
<body>
	<?php echo navbar($_CURRENTUSER) ?>

    <div class="page-contents no-padding">
	<div class="pch">
		<h1> Manage Documents </h1>
    </div>

    <div class="controls">
        <form method="GET" class="controls-bar" id="filterForm">
            
            <input 
                type="text" 
                name="search" 
                placeholder="Search documents..."
                class="search-box"
            >

            <select 
                name="category" 
                class="category-box" 
                onchange="document.getElementById('filterForm').submit()"
            >
                <?php
                    $categories = [
                        "All Categories",
                        "Activity Designs",
                        "Memorandum",
                        "Financial Statements",
                        "Minutes of Meetings",
                        "Accomplishment Report",
                        "Project Proposal"
                    ];

                    foreach ($categories as $cat) {
                        $selected = ($category === $cat) ? "selected" : "";
                        echo "<option value=\"$cat\" $selected>$cat</option>";
                    }
                ?>
            </select>
        </form>
    </div>

<div class="table-container">
<br>

<div class="table-wrapper">
<table id="docs-table">
    <thead>
        <tr>
            <th>Tracking Code</th>
            <th>Document Title</th>
            <th>Author</th>
            <th>Category</th>
            <th>Date Created</th>
            <th>Version</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach ($documents as $doc): ?>
        <tr>
            <td><?php echo (string)$doc->tracking_code; ?></td>
            <td><?php echo $doc->doc_title ?? ''; ?></td>
            <td><?php echo $doc->author ?></td>
            <td>
                <?php  
                    echo $doc->doc_category; 
                ?>
            </td>
            <td>
                <?php 
                    echo isset($doc->dates['date_added'])
                        ? (new DateTime($doc->dates['date_added']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('M d Y, g:i A')
                        : '';
                ?>
            </td>
            <td>
                <?php echo (int)$doc->current_version; ?>
            </td>
            <td><?php echo $doc->doc_status ?? ''; ?></td>

            <td>
                <a href="editPage.php?doc_id=<?php echo $doc->_id; ?>">
                    <button class="edit">Edit</button>
                </a>

                <a href="deletePage.php?id=<?php echo $doc->_id; ?>"
                   onclick="return confirm('Delete this document?')">
                    <button class="delete">Delete</button>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
    </div>
<br>
    <?php echo pagination_controls($currentPage, $totalPages)?>
    </div>
</body>
</html>