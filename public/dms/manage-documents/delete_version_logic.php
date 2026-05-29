<?php
session_start();
require_once '../../../bootstrap/app.php';
load (
    'authentication',
    'authorization',
    'vendor_autoload',
    'mongodb_collections'
);

if (!is_logged_in() || !can_use_dms($permissions)) {
    http_response_code(403);
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docID = $_POST['doc_id'] ?? null;
    $versionNum = isset($_POST['version_number']) ? (int)$_POST['version_number'] : null;

    if (!$docID || !$versionNum) {
        http_response_code(400);
        die("Missing parameters.");
    }

    $_id = new MongoDB\BSON\ObjectId($docID);

    $document = coll('documents')->findOne(['_id' => $_id])->execute();
    if (!$document) {
        http_response_code(404);
        die("Document context not found.");
    }

    if ($versionNum === (int)$document['current_version']) {
        http_response_code(400);
        die("Cannot delete the version currently in use.");
    }

    $versionData = coll('document_versions')->findOne([
        'doc_id' => $_id,
        'version_number' => $versionNum
    ])->execute();

    if ($versionData) {
        $absoluteFilePath = ROOT . $versionData['file_path'];
        if (file_exists($absoluteFilePath) && is_file($absoluteFilePath)) {
            unlink($absoluteFilePath);
        }

        coll('document_versions')->deleteOne([
            'doc_id' => $_id,
            'version_number' => $versionNum
        ])->execute();
    }

    // Re-fetch remaining versions to compile live markup and calculate the new max version
    $versions = coll('document_versions')
        ->find(['doc_id' => $_id])
        ->execute();

    $highestVersion = 0;
    $currentVersion = (int)$document['current_version'];
    global $app_url;

    if (!empty($versions)) {
        foreach ($versions as $v) {
            $highestVersion = max($highestVersion, $v['version_number']);
            
            $vid = $v['_id'];
            $vn = $v['version_number'];
            $vd = !empty($v['date_added'])
                ? (new DateTime($v['date_added']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('M d Y, g:i A')
                : '(unknown)';

            $inUseBadge = $vn === $currentVersion ? '<div class="in-use-badge">IN USE</div>' : "";
            
            $useVersionButton = $vn !== $currentVersion
                ? '<button type="button" class="document-action use-version" data-version="' . $vn . '"><img src="' . $app_url . '/images/doc-actions/use-version.png" draggable="false"></button>'
                : "";

            $deleteVersionButton = $vn !== $currentVersion
                ? '<button type="button" class="document-action delete-version-btn" data-version="' . $vn . '"><img src="' . $app_url . '/images/doc-actions/delete-doc.png" draggable="false"></button>'
                : "";

            $versionActiveness = $vn === $currentVersion ? "active" : "";

            echo <<< HTML
                <div class="version-card $versionActiveness" data-version="$vn">
                    <div class="id-box">
                        <p style="display: inline;"><b>Version $vn</b> $inUseBadge</p>
                        <p style="font-size: 0.85rem">$vd</p>
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
    } else {
        echo "<p>No versions</p>";
    }

    // Send the fresh variable calculation back via headers to avoid polluting the HTML payload string
    header("X-Highest-Version: " . $highestVersion);
    exit;
}