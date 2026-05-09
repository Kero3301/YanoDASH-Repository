<?php
    require_once dirname(dirname(__DIR__)). '/loader.php';
    load('text_utils');

    echo <<< HTML
        <link rel="stylesheet" href="$app_url/css/components/document-modal.css">
    HTML;

    function document_modal() {
        return <<< HTML
            <div id="docModal" class="modal">
                <div class="modal-content">
                    <h3 id="modalTitle"></h3>
                    <p id="modalCategory"></p>
                    <div id="modalDescriptionContainer">
                        <p id="modalDescription"></p>
                    </div>
                    <div class="modal-buttons">
                        <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                        <a id="downloadBtn" href="#" class="btn btn-primary" download>Download</a>
                    </div>
                </div>
            </div>
        HTML;
    }
?>