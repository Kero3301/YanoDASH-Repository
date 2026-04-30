<?php
    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="/yanodash-repository/css/components/document-card.css">
    HTML;

    function documentCard(string $tagclass = "", string $date = "unknown", string $author = "unknown", bool $readOnly = true, string $title = "Untitled", string $thumbnailPath = "/yanodash-repository/images/ui-indicators/doc-placeholder-thumbnail.png", string $dept = "OSC", string $tag = "Document", string $description = "Lorem ipsum dolor sit amet consectetur adipiscing elit.", string $tc = "ABC-1234-56789") {
        $sanitizedAuthor = htmlspecialchars($author);
        $sanitizedTitle = htmlspecialchars($title);
        $sanitizedTag = htmlspecialchars($tag);
        $sanitizedTrackingCode = htmlspecialchars($tc);
        $sanitizedDescription = htmlspecialchars($description);
        $sanitizedDepartment = htmlspecialchars($dept);
        $sanitizedDate = htmlspecialchars($date);
        $sanitizedTagclass = htmlspecialchars($tagclass);

        return <<< HTML
            <div class="doc-card-wrapper">
            <div class="doc-card">
                <div class="doc-preview">
                    <div class="doc-thumbnail" style="background-image:url('$thumbnailPath')"></div>
                    <span class="tag $sanitizedTagclass">$sanitizedTag</span>
                </div>
                <div class="doc-info">
                    <h2 class="doc-title">$sanitizedTitle</h2>
                    <p>📆 $sanitizedDate</p>
                    <p style="display: inline;">👤 $sanitizedAuthor</p> 
                    <p style="display: inline;">🏢 $sanitizedDepartment</p>
                    <br>
                    <p>🔎 <span class="doc-tc">$sanitizedTrackingCode</span></p>
                    <p>$sanitizedDescription</p>

                    <div class="doc-actions">
                    <button class="wow" title="View Document"><img src="/yanodash-repository/images/doc-actions/preview-doc.png"></button>
                    <button class="wow" title="Edit Document"><img src="/yanodash-repository/images/doc-actions/edit-doc.png"></button>
                    <button class="wow" title="Protect Document"><img src="/yanodash-repository/images/doc-actions/set-view-password.png"></button>
                    <button class="delete-btn">Delete</button>
                    </div>
                </div>
                
            </div>
            </div>
        HTML;
    }
?>