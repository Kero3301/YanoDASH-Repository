<?php
    session_start();

    require_once '../../../src/loader.php';
    load (
        'authentication',
        'authorization',
        'vendor_autoload',
        'mongodb_client',
        'mongodb_collections',
        'navbar',
        'accordion',
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms($permissions)) 
        die("You do not have permission to access this resource.");

    $docID = $_GET['doc_id'];
 
    $client = mongodb_client();

    $collection_documents = coll('documents', $client);

    $documentFound = false;
    $fetchedDocument = null;

    try {
        $fetchedDocument = $collection_documents->findOne([
            '_id' => new MongoDB\BSON\ObjectId($docID)
        ]);
        $documentFound = (bool) $fetchedDocument;
    } catch (Exception $e) {
        $documentFound = false;
    }

    $title = "";
    $area = "";
    $category = "Select Category";
    $currentVersion = 1;

    if ($documentFound) {
        $title = $fetchedDocument->doc_title;
        $area = $fetchedDocument->area_of_origin;
        $category = $fetchedDocument->doc_category;
        $currentVersion = $fetchedDocument->current_version;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Edit Document | YanoDASH')?>
    <link rel="stylesheet" type="text/css" href="../../css/pages/editstyle.css">
</head>
<body>
    <?php echo navbar(0) ?>
    <!-- Edit Document Content -->
    <div id="contents"> 
        <!-- Display this if the document is found -->
        <?php if ($documentFound): ?>
            <h1>Edit Document</h1>

            <form id="editDocumentForm"
                method="POST"
                action="edit_logic.php"
                enctype="multipart/form-data">

                <div class="tca">
                    <input type="hidden" name="doc_id" value="<?php echo $docID; ?>">

                    <label class="doc_title" for="doc_title">
                        Document Title:
                    </label>

                    <input class="box"
                        type="text"
                        name="doc_title"
                        required
                        value="<?php echo htmlspecialchars($title); ?>">
                </div>

                <div class="tca">
                    <label for="category">Category:</label>

                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Activity Designs">Activity Design</option>
                        <option value="Memorandum">Memorandum</option>
                        <option value="Financial Statements">Financial Statement</option>
                        <option value="Minutes of Meetings">Minutes of Meeting</option>
                        <option value="Accomplishment Report">Accomplishment Report</option>
                        <option value="Project Proposal">Project Proposal</option>
                    </select>
                </div>

                <div class="tca">
                    <label for="area">Area:</label>

                    <input class="box"
                        type="text"
                        name="area"
                        required
                        value="<?php echo htmlspecialchars($area); ?>" disabled>
                </div>

                <div id="fileupload-accordion" class="accordion-container">
                    <button type="button" class="accordion">
                        Upload a New Version
                    </button>

                    <div class="panel">
                        <input type="file"
                            name="new_file"
                            style="display: block; margin: auto;">
                    </div>
                </div>

                <button type="submit" class="btn">
                    Save Changes
                </button>
            </form>

        <!-- Display this message if the requested document is not found/doesn't exist -->
        <?php else: ?>
            <div class="not-found">
                <h1>Document Not Found</h1>

                <p>
                    Sorry, the document you are requesting was not found.
                </p>

                <a href="../" class="btn">
                    Return to DMS Home
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($documentFound): ?>
        <script>
            document.getElementById("category").value = "<?php echo $category?>";
        </script>
    <?php endif; ?>
</body>
</html>