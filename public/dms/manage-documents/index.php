<!-- Manage Documents -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();

    require_once '../../../src/loader.php';
    require_once '../../utils/doc_query.php'; 

    load (
        'vendor_autoload',
        'authentication',
        'authorization',
        'navbar'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms($identity))
        die("You do not have permission to access this resource.");

    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));
    $db = $client->yano_dash;
    $collection = $db->documents_schema;

    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';

    $securityQuery = buildQuery($_SESSION['auth'], $_SESSION['auth'], 'dms');

    $userFilters = [];
    if (!empty($search)) {
        $userFilters['doc_title'] = [
            '$regex' => $search,
            '$options' => 'i'
        ];
    }
    if (!empty($category) && $category !== 'All Categories') {
        $userFilters['doc_categories'] = ['$in' => [$category]];
    }

    if (empty($userFilters)) {
        $finalQuery = $securityQuery;
    } else {
        $finalQuery = [
            '$and' => [
                $securityQuery,
                $userFilters
            ]
        ];
    }

    $documents = $collection->find($finalQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Manage Documents | YanoDASH')?>
	<link rel="stylesheet" type="text/css" href="../../css/pages/managestyle.css">
</head>
<body>
	<?php echo navbar(0) ?>
	<header class="title">
		<h1> Manage Documents </h1>
	</header>

    <div class="controls">
        <form method="GET" class="controls-bar">

            <input 
                type="text" 
                name="search" 
                placeholder="Search documents..."
                value="<?php echo htmlspecialchars($search); ?>"
                class="search-box"
            >

            <!-- RIGHT: CATEGORY -->
            <select name="category" class="category-box" onchange="this.form.submit()">
                <?php
                    $categories = [
                        "All Categories",
                        "Activity Design",
                        "Memorandum",
                        "Financial Statement",
                        "Meeting Minutes",
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

    <!-- TABLE -->
<div class="table-container">
<br>

<table>
    <thead>
        <tr>
            <th>Tracking Code</th>
            <th>Document Title</th>
            <th>Category</th>
            <th>Date Uploaded</th>
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
            <td>
                <?php  
                    echo isset($doc->doc_categories)
                        ? implode(", ", (array)$doc->doc_categories)
                        : ''; 
                ?>
            </td>
            <td>
                <?php 
                    echo isset($doc->dates->date_added)
                        ? $doc->dates->date_added->toDateTime()->format('Y-m-d')
                        : '';
                ?>
            </td>
            <td>
                <?php echo (int)$doc->current_version_id; ?>
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

</body>
</html>