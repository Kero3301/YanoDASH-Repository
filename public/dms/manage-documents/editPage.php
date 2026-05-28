<?php
    session_start();

    require_once '../../../bootstrap/app.php';
    load (
        'authentication',
        'authorization',
        'vendor_autoload',
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
 
    $documentFound = false;
    $fetchedDocument = null;

    try {
        $fetchedDocument = coll('documents')
            ->findOne(['_id' => new MongoDB\BSON\ObjectId($docID)])
            ->execute();
        $documentFound = (bool) $fetchedDocument;
    } catch (Exception $e) {
        $documentFound = false;
    }

    $title = "";
    $area = "";
    $category = "Select Category";
    $currentVersion = 1;

    $v_ids = [];

    if ($documentFound) {
        $title = $fetchedDocument['doc_title'];
        $area = $fetchedDocument['area_of_origin'];
        $category = $fetchedDocument['doc_category'];
        $currentVersion = $fetchedDocument['current_version'];

        $versions = coll('document_versions')
            ->find(['doc_id' => new MongoDB\BSON\ObjectId($fetchedDocument['_id'])])
            ->execute();

        foreach ($versions as $v) {
            array_push($v_ids, $v['_id']);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .dw {
            display: grid;
            /* grid-template-columns: repeat(3, auto); two autosized columns */
            gap: 8px;
            justify-items: center;
            align-items: center;
            border: 3px solid black;
            border-radius: 10px;
            flex-shrink: 0;
            }

            /* Top block spans full width */
            .id-box {
            grid-column: 1 / -1;
            padding: 8px;
            background: black;
            color: white;
            text-align: left;
            border-radius: 8px;
            }

            /* Optional styling for bottom cells */
            .cell {
                padding: 8px;
                background: #eee;
                border-radius: 6px;
                cursor: pointer;
            }

            .current-version-badge {
                display: inline;
                border: 2px solid #86ff86;
                background: #2dd32d;
                color: #004d00;
                text-align: center;
                padding: 2px 6px;
                border-radius: 12px;
                font-size: 0.85rem;
                font-family: 'RobotoFlex', sans-serif
            }
    </style>
    <?php initialize_page('Edit Document | YanoDASH')?>
    <link rel="stylesheet" type="text/css" href="../../css/pages/editstyle.css">
    <link rel="stylesheet" type="text/css" href="../../css/components/document-card.css">
</head>
<body>
    <?php echo navbar(0) ?>
    <!-- Edit Document Content -->
    <div id="contents"> 
        <!-- Display this if the document is found -->
        <?php if ($documentFound): ?>
            <div class="page-contents no-padding">
            <div class="pch">
                <h1>Edit Document</h1>
            </div>
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
                        <option value="Activity Design">Activity Design</option>
                        <option value="Memorandum">Memorandum</option>
                        <option value="Minutes of Meeting">Minutes of Meeting</option>
                        <option value="Notice of Meeting">Notice of Meeting</option>
                        <option value="Project Proposal">Project Proposal</option>
                        <option value="Financial Statement">Financial Statement</option>
                        <option value="Accomplishment Report">Accomplishment Report</option>
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

                <div class="tca">
                    <label>Version History</label>
                    <div style="border-radius: 8px; padding: 12px; width: 100%; background: #eee; border: 2px solid #ddd">
                        <div  id="version-container" class="version-container" style="border-radius: 16px; background: #FAFAFA; text-align: center; padding: 24px 8px; display: flex; flex-direction: row; gap: 8px; overflow-x: auto; ">
                            <?php if(!empty($versions)):?>
                                <?php 
                                    global $app_url;

                                    foreach($versions as $v) {
                                        $vn = $v['version_number'];
                                        $vd = !empty($v['date_added'])
                                            ? (new DateTime($v['date_added']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('M d, Y - g:i A')
                                            : '(unknown)';
                                        $currentVersionBadge = $vn === $currentVersion 
                                            ? <<< HTML
                                                <div class="current-version-badge">
                                                    IN USE
                                                </div>
                                            HTML
                                            : "";
                                        $useVersionButton = $vn !== $currentVersion
                                            ? <<< HTML
                                                <button type="button" class="document-action" style="display: inline-block;">
                                                    <img src="$app_url/images/doc-actions/use-version.png" draggable="false">
                                                </button>
                                            HTML
                                            : "";

                                        echo <<< HTML
                                            <div class="dw">
                                                <div class="id-box">
                                                    <p style="display: inline;"><b>Version $vn</b> $currentVersionBadge</p>
                                                    <p>$vd</p>
                                                </div>

                                                <div>
                                                <button type="button" class="document-action" style="display: inline-block;">
                                                    <img src="$app_url/images/doc-actions/preview-doc.png" draggable="false">
                                                </button>
                                                <button type="button" class="document-action" style="display: inline-block;">
                                                    <img src="$app_url/images/doc-actions/download-doc.png" draggable="false">
                                                </button>
                                                $useVersionButton
                                                <button type="button" class="document-action" style="display: inline-block;">
                                                    <img src="$app_url/images/doc-actions/delete-doc.png" draggable="false">
                                                </button>
                                                </div>
                                            </div>
                                        HTML;
                                    }
                                ?>
                            <?php else: ?>
                                <p>No versions</p>
                            <?php endif; ?>
                        </div>
                        <p style="text-align: center">The version you use <img style="vertical-align: middle;" width="20" height="20" alt="Green checkmark: Use this version" src="<?= $app_url. '/images/doc-actions/use-version.png' ?>"> will be the one seen by reviewers during the archiving process.</p>
                        <div id="fileupload-accordion" class="accordion-container">
                            <button type="button" class="accordion">
                                Upload a New Version
                            </button>

                            <div class="panel">
                                <input type="file"
                                    name="new_file"
                                    style="display: block; margin: auto;"
                                    accept=".pdf,.doc,.docx,.txt">
                            </div>
                        </div>
                    </div>
                </div>

                

                <button type="submit" class="btn">
                    Save Changes
                </button>
            </form>
        </div>
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