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
        .version-card {
            display: grid;
            gap: 8px;
            justify-items: center;
            align-items: center;
            border: 3px dotted #DDD;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            flex-shrink: 0;
            background: #F1f1f1;
            padding: 6px;
        }

        .version-card:has(.document-action.use-version:hover) {
            border-color: #86ff86;
        }

        .version-card.active {
            border-color: #2dd32d;
        }

            /* Top block spans full width */
            .id-box {
            grid-column: 1 / -1;
            padding: 8px;
            background: transparent;
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

            .in-use-badge {
                display: inline;
                border: 2px solid #86ff86;
                background: #2dd32d;
                color: black;
                text-align: center;
                padding: 2px 6px;
                border-radius: 12px;
                font-size: 0.85rem;
                font-family: 'RobotoFlex', sans-serif
            }

            .file-upload {
                display: none;
                margin: auto;
                text-align: center;
            }

            .file-upload-container {
                justify-items: center;
                justify-content: center;
                align-items: center;
                text-align: center;
                padding: 8px;
                border: 3px solid black;
                border-radius: 16px;
                background: black;
                color: white;
                width: max-content;
                margin: auto;
            }

            .file-upload-btn {
                padding: 4px 8px;
                border-radius: 8px;
                background: white;
                border: 3px dotted lightgray;
                font-family: 'RobotoFlex', sans-serif;
                cursor: pointer;
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
                        Document Title
                    </label>

                    <input class="box"
                        id="doc_title"
                        type="text"
                        name="doc_title"
                        required
                        value="<?php echo htmlspecialchars($title); ?>">
                </div>

                <div class="tca">
                    <label for="category">Document Category</label>

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
                    <label>Version History</label>
                    <div style="border-radius: 16px; padding: 12px; width: 100%; background: #eee; border: 3px solid #ddd">
                        <div  id="version-container" class="version-container" style="border-radius: 14px; background: #FAFAFA; text-align: center; padding: 24px 8px; display: flex; flex-direction: row; gap: 10px; overflow-x: auto; ">
                            <?php if(!empty($versions)):?>
                                <?php 
                                    global $app_url;

                                    foreach($versions as $v) {
                                        $vid = $v['_id'];
                                        $vn = $v['version_number'];
                                        $vd = !empty($v['date_added'])
                                            ? (new DateTime($v['date_added']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('M d Y, g:i A')
                                            : '(unknown)';
                                        $vfp = $v['file_path'];
                                        $filePath = ROOT. $vfp;

                                        $inUseBadge = $vn === $currentVersion 
                                            ? <<< HTML
                                                <div class="in-use-badge">
                                                    IN USE
                                                </div>
                                            HTML
                                            : "";
                                        $useVersionButton = $vn !== $currentVersion
                                            ? <<< HTML
                                                <button type="button" class="document-action use-version" style="display: inline-block;">
                                                    <img src="$app_url/images/doc-actions/use-version.png" draggable="false">
                                                </button>
                                            HTML
                                            : "";
                                        $deleteVersionButton = $vn !== $currentVersion
                                            ? <<< HTML
                                                <button type="button" class="document-action" style="display: inline-block;">
                                                    <img src="$app_url/images/doc-actions/delete-doc.png" draggable="false">
                                                </button>
                                            HTML
                                            : "";
                                        $versionActiveness = $vn === $currentVersion ? "active": "";

                                        echo <<< HTML
                                            <div class="version-card $versionActiveness">
                                                <div class="id-box">
                                                    <p style="display: inline;"><b>Version $vn</b> $inUseBadge</p>
                                                    <p>$vd</p>
                                                </div>

                                                <div class="button-list">
                                                    <button type="button" class="document-action" style="display: inline-block;">
                                                        <img src="$app_url/images/doc-actions/preview-doc.png" draggable="false">
                                                    </button>
                                                    <button type="button" class="document-action download-btn" data-version-id="$vid" style="display: inline-block;">
                                                        <img src="$app_url/images/doc-actions/download-doc.png" draggable="false">
                                                    </button>
                                                    $useVersionButton
                                                    $deleteVersionButton
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
                        
                        <hr class="short-divider">

                        <div class="file-upload-container">
                            <label for="new_file"><b>Upload a New Version</b></label>
                            <input type="file" id="new_file" class="file-upload"
                                        name="new_file"
                                        style="display: block; margin: auto;"
                                        accept=".pdf,.doc,.docx,.txt"
                                        hidden>
                            <p class="instructional-label-technical">Maximum file size: 5 MB</p>
                            <p id="new-version-label" style="text-align: center; display: none; font-size: 0.8rem; padding: 2px 10px; border-radius: 16px; background: #8e86ff; color: black">Version: <b><?php $newVersion = ++$currentVersion; echo $newVersion; ?></b></p>
                        <button type="button"
                                id="remove-version-btn"
                                class="btn danger small-padding"
                                style="display: none; margin-top: 10px; font-size: 0.8rem; font-weight: normal">
                            Remove Version
                        </button>
                        </div>
                        <p id="version-add-notice" style="text-align: center; display: none;">After saving changes, this version will be added<br>to the version history and used automatically.</p>
                    </div>
                </div>

                

                <div class="button-list">
                <button type="submit" class="btn positive">
                    Save Changes
                </button>
                <a class="btn danger" href="./">Cancel</a>
                </div>
            </form>

          
            <div id="download-toast" class="toast">
                <span class="toast-message"></span>

                <button
                    type="button"
                    class="toast-close"
                    aria-label="Close">
                    ×
                </button>
            </div>
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

    <script>
        const fileInput = document.getElementById("new_file");
        const uploadBtn = document.getElementById("uploadBtn");
        const versionAddNotice = document.getElementById("version-add-notice");
        const newVersionLabel = document.getElementById("new-version-label");
        const removeVersionBtn = document.getElementById("remove-version-btn");

        function updateVersionUI() {
            const hasFiles = fileInput.files.length > 0;

            versionAddNotice.style.display = hasFiles ? "block" : "none";
            newVersionLabel.style.display = hasFiles ? "inline" : "none";
            removeVersionBtn.style.display = hasFiles ? "inline-block" : "none";
        }

        fileInput.addEventListener("change", updateVersionUI);

        removeVersionBtn.addEventListener("click", function () {
            fileInput.value = "";
            updateVersionUI();
        });
    </script>
</body>
</html>