<!-- Manage Documents -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();

    require_once '../../utils/loader.php';
   
    load_components('navbar');
    load_utils(
        'authentication',
        'authorization'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms())
        die("You do not have permission to access this resource.");

    require_once '../../vendor/autoload.php';

    use MongoDB\Client;

    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));

    $db = $client->yano_dash;
    $collection = $db->documents_schema;

    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';

    $query = [];

    if (!empty($search)) {
        $query['title'] = [
            '$regex' => $search,
            '$options' => 'i'
        ];
    }

    if (!empty($category) && $category !== 'All Categories') {
        $query['category'] = $category;
    }

    // fetch documents
    $documents = $collection->find($query);
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Manage Documents | YanoDASH')?>
	<link rel="stylesheet" type="text/css" href="../../css/pages/managestyle.css">
</head>
<body>
	<?php echo navbar(0) ?>
	<header class="title"> <!-- start of header -->
		<h1> Manage Documents </h1>
	</header> <!-- end of header -->

    <div class="controls">
        <form method="GET" class="controls-bar">

            <!-- LEFT: SEARCH -->
            <input 
                type="text" 
                name="search" 
                placeholder="Search documents..."
                value="<?php echo htmlspecialchars($search); ?>"
                class="search-box"
            >

            <!-- RIGHT: CATEGORY -->
            <select name="category" class="category-box">
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

    <!-- TABLE -->
<div class="table-container">
<br>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Document Title</th>
            <th>Category</th>
            <th>Date Uploaded</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach ($documents as $doc): ?>
        <tr>
            <td><?php echo (string)$doc->_id; ?></td>
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
            <td><?php echo $doc->doc_status ?? ''; ?></td>

            <td>
                <a href="editPage.php?id=<?php echo $doc->_id; ?>">
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