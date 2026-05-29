<!-- Manage Documents -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();

    require_once '../../../bootstrap/app.php';

    load (
        'vendor_autoload',
        'mongodb_client',
        'mongodb_collections',
        'authentication',
        'authorization',
        'navbar'
    );   

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms($permissions))
        die("You do not have permission to access this resource.");

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
    $results = coll('documents')
        ->find($baseQuery)
        ->sort(['dates.date_added' => -1])
        ->skip($skip)
        ->limit($documentsPerPage)
        ->execute();

    $documents = $results;


    // $client = mongodb_client();

    // $collection = coll('documents', $client);

    // $search = $_GET['search'] ?? '';
    // $category = $_GET['category'] ?? '';

    // $query = [];

    // if (!empty($search)) {
    //     $query['doc_title'] = [
    //         '$regex' => $search,
    //         '$options' => 'i'
    //     ];
    // }

    // if (!empty($category) && $category !== 'All Categories') {
    //     $query['doc_categories'] = $category;
    // }

    // $documents = $collection->find($query);
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Manage Documents | YanoDASH')?>
	<link rel="stylesheet" type="text/css" href="../../css/pages/managestyle.css">
</head>
<body>
	<?php echo navbar(0) ?>

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

<table>
    <thead>
        <tr>
            <th>Tracking Code</th>
            <th>Document Title</th>
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
            <td><?php echo (string)$doc['tracking_code']; ?></td>
            <td><?php echo $doc['doc_title'] ?? ''; ?></td>
            <td>
                <?php  
                    echo $doc['doc_category']; 
                ?>
            </td>
            <td>
                <?php 
                    echo isset($doc['dates']['date_added'])
                        ? (new DateTime($doc['dates']['date_added']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('M d Y, g:i A')
                        : '';
                ?>
            </td>
            <td>
                <?php echo (int)$doc['current_version']; ?>
            </td>
            <td><?php echo $doc['doc_status'] ?? ''; ?></td>

            <td>
                <a href="editPage.php?doc_id=<?php echo $doc['_id']; ?>">
                    <button class="edit">Edit</button>
                </a>

                <a href="deletePage.php?id=<?php echo $doc['_id']; ?>"
                   onclick="return confirm('Delete this document?')">
                    <button class="delete">Delete</button>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>
    </div>
</body>
</html>