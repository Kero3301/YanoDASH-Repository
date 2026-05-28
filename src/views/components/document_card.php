<?php
    require_once dirname(dirname(__DIR__)). '/loader.php';
    load (
        'doc_ed',
        'authorization'
    );

    global $app_url;

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="$app_url/css/components/document-card.css">
        <script src="$app_url/script/document-actions.js"></script>
    HTML;

    define ('DEFAULT_THUMBNAIL_PATH', "$app_url/images/ui-indicators/doc-placeholder-thumbnail.png");

    function document_card(Document $document, string $tagclass = "") {
        global $app_url;
        global $identity;
        global $permissions;

        # Auth-related data
        $isAdmin = $permissions['access_level'] === "admin";

        # Primary data
        $_id = $document->_id;
        $title = $document->doc_title ?? "(Unknown)";
        $category = $document->doc_category ?? "";
        $tags = $document->doc_tags ?? [];
        $author = $document->author ?? "(Unknown)";
        $area_of_origin = $document->area_of_origin ?? "(Unknown)";
        $status = $document->doc_status ?? "(Unknown)";
        $tc = $document->tracking_code ?? "(Unknown)";
        $dates = $document->dates ?? [];
        $version = $document->version ?? 0;
        $category_data = $document->category_data ?? [];

        # Secondary data
        $isInternal = in_array($status, ["EDITING", "ARCHIVED"]);
        $isPublic = $status === "PUBLICIZED";
        $readOnly = in_array($status, ["ARCHIVED", "PUBLICIZED"]);
        $tags = implode(',', $tags);
        $add_date = !empty($dates['date_added'])
            ? (new DateTime($dates['date_added']))->format('Y-m-d g:i A')
            : '(unknown)';
        $finalize_date = !empty($dates['date_finalized'])
            ? (new DateTime($dates['date_finalized']))->format('Y-m-d g:i A')
            : '(unknown)';
        $archive_date = !empty($dates['date_archived'])
            ? (new DateTime($dates['date_archived']))->format('Y-m-d g:i A')
            : '(unknown)';
        // $archive_date = (string) $dates['date_archived']?->toDateTime()->format('Y-m-d g:i A');

        # Decorative data
        $tagClass = match (strtoupper($category)) {
            default => "gsp",
            "MEETING MINUTES", "ACTIVITY DESIGN" => "technical",
            "ACCOMPLISHMENT_REPORT" => "essay",
            "PROJECT PROPOSAL" => "research"
        };

        # Sanitized data
        $sanitizedID = htmlspecialchars($_id);
        $sanitizedTitle = htmlspecialchars($title);
        $sanitizedCategory = htmlspecialchars($category);
        $sanitizedTags = htmlspecialchars($tags);
        $sanitizedAuthor = htmlspecialchars($author);
        $sanitizedAreaOfOrigin = htmlspecialchars($area_of_origin);
        $sanitizedStatus = htmlspecialchars($status);
        $sanitizedTC = htmlspecialchars($tc);
        $sanitizedAddDate = htmlspecialchars($add_date);
        $sanitizedFinalizeDate = htmlspecialchars($finalize_date);
        $sanitizedArchiveDate = htmlspecialchars($archive_date);

        $view_button = <<< HTML
            <button class="document-action" title="View Document" data-action="view" data-linked-document="$sanitizedID">
                <img src="$app_url/images/doc-actions/preview-doc.png" draggable="false">
            </button>
        HTML;

        $edit_button = $readOnly
            ? ""
            : <<< HTML
                <button class="document-action" title="Edit Document" data-action="edit" data-linked-document="$sanitizedID">
                    <img src="$app_url/images/doc-actions/edit-doc.png" draggable="false">
                </button>
            HTML;

        $protect_button = !$isAdmin
            ? ""
            : <<< HTML
                <button class="document-action" title="Protect Document" data-action="protect" data-linked-document="$sanitizedID">
                    <img src="$app_url/images/doc-actions/set-view-password.png" draggable="false">
                </button>
            HTML;
        
        $delete_button = !$isAdmin
            ? ""
            : <<< HTML
                <button class="delete-btn" title="Delete Document" data-action="delete" data-linked-document="$sanitizedID">Delete</button>
            HTML;

        $thumbnailPath = "";

        return <<< HTML
            <div class="doc-card-wrapper">
                <div class="doc-card-b2">
                    <div class="doc-card-b1">
                        <div class="doc-card" data-category="$sanitizedCategory" data-document-id="$sanitizedID" data-status="$sanitizedStatus" data-publicity="$isPublic">
                            <div class="doc-preview">
                                <div class="doc-thumbnail" style="background-image:url('$thumbnailPath')"></div>
                                <span class="tag $tagClass">$sanitizedCategory</span>
                            </div>
                            <div class="doc-info">
                                <h3 class="doc-title" title="$sanitizedTitle">$sanitizedTitle</h3>
                                <p>📆 $sanitizedAddDate</p>
                                <p>👤 $sanitizedAuthor</p> 
                                <p>🏢 $sanitizedAreaOfOrigin</p>
                                <p>🔎 <span class="doc-tc">$sanitizedTC</span></p>
                                <p class="doc-desc">Lorem ipsum</p>
                            </div>
                            <!-- <div class="doc-actions">
                                $view_button
                                $edit_button
                                $protect_button
                                $delete_button
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }
?>