<?php
    require_once dirname(__DIR__). '/utils/loader.php';
    load_utils("data/DocEd");

    global $app_url;

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="$app_url/css/components/document-card.css">
        <script src="$app_url/script/document-actions.js"></script>
    HTML;

    define ('DEFAULT_THUMBNAIL_PATH', "$app_url/images/ui-indicators/doc-placeholder-thumbnail.png");

    function document_card(Document $document, string $tagclass = "") {
        global $app_url;
        $isAdmin = $_SESSION['auth']['access_level'] === 'admin';

        $_id = $document->_id;
        $title = $document->doc_title;
        $tag = $document->categories[0];
        $date = (string) $document->dates['date_added']->toDateTime()->format('Y-m-d g:i A');
        $author = $document->author;
        $dept = $document->area_of_origin;
        $tc = $document->tracking_code;
        $description = $document->description;
        $maincateg = $document->main_category;

        $docStatus = strtoupper($document->status);
        $isDocPublic = $document->is_publicized;

        $sanitizedMaincateg = htmlspecialchars($maincateg);
        $sanitizedDocumentID = htmlspecialchars($_id); 
        $sanitizedAuthor = htmlspecialchars($author);
        $sanitizedTitle = htmlspecialchars($title);
        $sanitizedTag = htmlspecialchars($tag);
        $sanitizedDate = htmlspecialchars($date);
        $sanitizedTrackingCode = htmlspecialchars($tc);
        $sanitizedDescription = htmlspecialchars($description);
        $sanitizedDepartment = htmlspecialchars($dept);
        $sanitizedTagclass = htmlspecialchars($tagclass);

        $sanitizedDocStatus = htmlspecialchars($docStatus);
        $sanitizedIsDocPublic = htmlspecialchars($isDocPublic);

        $normalizedDocStatus = strtoupper($sanitizedDocStatus);

        $readOnly = $normalizedDocStatus === "ARCHIVED";

        $view_button = <<< HTML
            <button class="document-action" title="View Document" data-action="view" data-linked-document="$sanitizedDocumentID">
                <img src="$app_url/images/doc-actions/preview-doc.png" draggable="false">
            </button>
        HTML;

        $edit_button = $readOnly
            ? ""
            : <<< HTML
                <button class="document-action" title="Edit Document" data-action="edit" data-linked-document="$sanitizedDocumentID">
                    <img src="$app_url/images/doc-actions/edit-doc.png" draggable="false">
                </button>
            HTML;

        $protect_button = !$isAdmin
            ? ""
            : <<< HTML
                <button class="document-action" title="Protect Document" data-action="protect" data-linked-document="$sanitizedDocumentID">
                    <img src="$app_url/images/doc-actions/set-view-password.png" draggable="false">
                </button>
            HTML;
        
        $delete_button = !$isAdmin
            ? ""
            : <<< HTML
                <button class="delete-btn" title="Delete Document" data-action="delete" data-linked-document="$sanitizedDocumentID">Delete</button>
            HTML;

        $thumbnailPath = "";

        return <<< HTML
            <div class="doc-card-wrapper">
                <div class="doc-card-b2">
                    <div class="doc-card-b1">
                        <div class="doc-card" data-category="$sanitizedMaincateg" data-document-id="$sanitizedDocumentID" data-status="$docStatus" data-publicity="$isDocPublic">
                            <div class="doc-preview">
                                <div class="doc-thumbnail" style="background-image:url('$thumbnailPath')"></div>
                                <span class="tag $sanitizedTagclass">$sanitizedMaincateg</span>
                            </div>
                            <div class="doc-info">
                                <h2 class="doc-title">$sanitizedTitle</h2>
                                <p>📆 $sanitizedDate</p>
                                <p style="display: inline;">👤 $sanitizedAuthor</p> 
                                <p style="display: inline;">🏢 $sanitizedDepartment</p>
                                <br>
                                <p>🔎 <span class="doc-tc">$sanitizedTrackingCode</span></p>
                                <p class="doc-desc">$sanitizedDescription</p>

                            </div>
                            <div class="doc-actions">
                                $view_button
                                $edit_button
                                $protect_button
                                $delete_button
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }
?>