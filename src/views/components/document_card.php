<?php
    require_once dirname(dirname(__DIR__)). '/loader.php';
    load (
        'doc_ed',
        'authorizer',
        'iam_context_validator'
    );

    global $app_url;

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="$app_url/css/components/document-card.css">
        <script src="$app_url/script/document-actions.js"></script>
    HTML;

    define ('DEFAULT_THUMBNAIL_PATH', "$app_url/images/ui-indicators/doc-placeholder-thumbnail.png");

    function document_card(Document $document, mixed $user, string $tagclass = "") {
        # Load first document data
        $_id = $document->_id;
        $title = $document->doc_title ?? "(unknown)";
        $description = $document->description ?? "(no description)";
        $category = $document->doc_category ?? "";
        $tags = $document->doc_tags ?? [];
        $tc = $document->tracking_code;
        $status = $document->doc_status;
        $author = $document->author ?? "(unknown)";
        $areaOfOrigin = $document->area_of_origin ?? "(unknown)";
        $dates = $document->dates ?? [];
        $version = $document->current_version ?? 0;
        $categoryData = $document->categoryData ?? null;

        # Sanitize data to be displayed
        $sanitizedID = htmlspecialchars($_id);
        $sanitizedTitle = htmlspecialchars($title);
        $sanitizedDescription = htmlspecialchars($description);
        $sanitizedCategory = htmlspecialchars($category);
        $sanitizedTC = htmlspecialchars($tc);
        $sanitizedAuthor = htmlspecialchars($author);
        $sanitizedAreaOfOrigin = htmlspecialchars($areaOfOrigin);

        global $app_url;
        
        $userValidity = IAMContextValidator::validate($user);

        # Default doc actions
        $previewAction = <<< HTML
            <button class="document-action" title="View Document" data-action="view" data-linked-document="$sanitizedID">
                <img src="$app_url/images/doc-actions/preview-doc.png" draggable="false">
            </button>
        HTML;
        $downloadAction = <<< HTML
            <button class="document-action" title="Download Document" data-action="download" data-linked-doc="$sanitizedID">
                <img src="$app_url/images/doc-actions/download-doc.png" draggable="false">
            </button>
        HTML;

        $docActions = <<< HTML
            <div class="button-list doc-actions">

            </div>
        HTML;

        # Map doc category to tags
        $tagClass = match (strtoupper($category)) {
            "MEMORANDUM" => 'general',
            "MINUTES OF MEETING", "NOTICE OF MEETING", "ACCOMPLISHMENT_REPORT", "ATTENDANCE" => 'secretarial',
            "FINANCIAL STATEMENT", "MERCH-RELATED" => 'financial',
            "ACTIVITY DESIGN", "PROJECT PROPOSAL" => 'external',
            default => 'general'
        };

        # Decide based on doc status
        switch (strtoupper(trim($status))) {
            # Public and safe for general consumption
            case "PUBLICIZED":
                return <<< HTML
                    <div class="doc-card-wrapper">
                        <div class="doc-card-b2">
                            <div class="doc-card-b1">
                                <div class="doc-card" data-category="$sanitizedCategory" data-doc-id="$sanitizedID">
                                    <div class="doc-preview">
                                        <div class="doc-thumbnail"></div>
                                        <span class="tag $tagClass">$sanitizedCategory</span>
                                    </div>
                                    <div class="doc-info">
                                        <h3 class="doc-title" title="$sanitizedTitle">$sanitizedTitle</h3>
                                        <p>👤 $sanitizedAuthor</p> 
                                        <p>🏢 $sanitizedAreaOfOrigin</p>
                                        <p>🔎 <span class="doc-tc">$sanitizedTC</span></p>
                                        <p class="doc-desc">$sanitizedDescription</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                HTML;
            # Archived and can be seen only by verified admins or editors
            case "ARCHIVED":
                return "<h1>Archived</h1>";
            # Pending archival and can be tracked
            case "PENDING ARCHIVAL":
                return "<h1>Pending Archival</h1>";
            # Editing in DMS
            case "EDITING":
                return "<h1>Editing in DMS</h1>";
            default:
                return "<h1>Unknown</h1>";
        }
    }


        # Primary data
        // $_id = $document->_id;
        // $title = $document->doc_title ?? "(Unknown)";
        // $category = $document->doc_category ?? "";
        // $tags = $document->doc_tags ?? [];
        // $author = $document->author ?? "(Unknown)";
        // $area_of_origin = $document->area_of_origin ?? "(Unknown)";
        // $status = $document->doc_status ?? "(Unknown)";
        // $tc = $document->tracking_code ?? "(Unknown)";
        // $dates = $document->dates ?? [];
        // $version = $document->version ?? 0;
        // $category_data = $document->category_data ?? [];

        # Secondary data
        // $isInternal = in_array($status, ["EDITING", "ARCHIVED"]);
        // $isPublic = $status === "PUBLICIZED";
        // $readOnly = in_array($status, ["ARCHIVED", "PUBLICIZED"]);
        // $tags = implode(',', $tags);
        // $add_date = !empty($dates['date_added'])
        //     ? (new DateTime($dates['date_added']))->format('Y-m-d g:i A')
        //     : '(unknown)';
        // $finalize_date = !empty($dates['date_finalized'])
        //     ? (new DateTime($dates['date_finalized']))->format('Y-m-d g:i A')
        //     : '(unknown)';
        // $archive_date = !empty($dates['date_archived'])
        //     ? (new DateTime($dates['date_archived']))->format('Y-m-d g:i A')
        //     : '(unknown)';
        // $archive_date = (string) $dates['date_archived']?->toDateTime()->format('Y-m-d g:i A');

        // # Decorative data
        // $tagClass = match (strtoupper($category)) {
        //     default => "gsp",
        //     "MEETING MINUTES", "ACTIVITY DESIGN" => "technical",
        //     "ACCOMPLISHMENT_REPORT" => "essay",
        //     "PROJECT PROPOSAL" => "research"
        // };

        // # Sanitized data
        // $sanitizedID = htmlspecialchars($_id);
        // $sanitizedTitle = htmlspecialchars($title);
        // $sanitizedCategory = htmlspecialchars($category);
        // $sanitizedTags = htmlspecialchars($tags);
        // $sanitizedAuthor = htmlspecialchars($author);
        // $sanitizedAreaOfOrigin = htmlspecialchars($area_of_origin);
        // $sanitizedStatus = htmlspecialchars($status);
        // $sanitizedTC = htmlspecialchars($tc);
        // $sanitizedAddDate = htmlspecialchars($add_date);
        // $sanitizedFinalizeDate = htmlspecialchars($finalize_date);
        // $sanitizedArchiveDate = htmlspecialchars($archive_date);

        // $view_button = <<< HTML
        //     <button class="document-action" title="View Document" data-action="view" data-linked-document="$sanitizedID">
        //         <img src="$app_url/images/doc-actions/preview-doc.png" draggable="false">
        //     </button>
        // HTML;

        // $edit_button = $readOnly
        //     ? ""
        //     : <<< HTML
        //         <button class="document-action" title="Edit Document" data-action="edit" data-linked-document="$sanitizedID">
        //             <img src="$app_url/images/doc-actions/edit-doc.png" draggable="false">
        //         </button>
        //     HTML;

        // $protect_button = !$isAdmin
        //     ? ""
        //     : <<< HTML
        //         <button class="document-action" title="Protect Document" data-action="protect" data-linked-document="$sanitizedID">
        //             <img src="$app_url/images/doc-actions/set-view-password.png" draggable="false">
        //         </button>
        //     HTML;
        
        // $delete_button = !$isAdmin
        //     ? ""
        //     : <<< HTML
        //         <button class="delete-btn" title="Delete Document" data-action="delete" data-linked-document="$sanitizedID">Delete</button>
        //     HTML;

    //     $thumbnailPath = "";

    //     return <<< HTML
    //         <div class="doc-card-wrapper">
    //             <div class="doc-card-b2">
    //                 <div class="doc-card-b1">
    //                     <div class="doc-card" data-category="$sanitizedCategory" data-document-id="$sanitizedID" data-status="$sanitizedStatus" data-publicity="$isPublic">
    //                         <div class="doc-preview">
    //                             <div class="doc-thumbnail" style="background-image:url('$thumbnailPath')"></div>
    //                             <span class="tag $tagClass">$sanitizedCategory</span>
    //                         </div>
    //                         <div class="doc-info">
    //                             <h3 class="doc-title" title="$sanitizedTitle">$sanitizedTitle</h3>
    //                             <p>📆 $sanitizedAddDate</p>
    //                             <p>👤 $sanitizedAuthor</p> 
    //                             <p>🏢 $sanitizedAreaOfOrigin</p>
    //                             <p>🔎 <span class="doc-tc">$sanitizedTC</span></p>
    //                             <p class="doc-desc" style="display: none">(no description)</p>
    //                         </div>
    //                         <!-- <div class="doc-actions">
    //                             $view_button
    //                             $edit_button
    //                             $protect_button
    //                             $delete_button
    //                         </div> -->
    //                     </div>
    //                 </div>
    //             </div>
    //         </div>
    //     HTML;
    // }
?>