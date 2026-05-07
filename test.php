<?php
    ini_set('display_errors', 'Off');
    session_start();
    
    require_once 'vendor/autoload.php';
    require_once 'utils/loader.php';

    load_components(
        'document_list'
    );
    load_utils(
        'document_factory'
    );
    
    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));

    $collection_documents = $client->yano_dash->documents_schema;
    $results = $collection_documents->find(
        [
            'is_publicized' => true
        ]
    );
    $documents = get_all($results);
?>

<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page("Document Loading Test")?>
        <style>
            .docs-grid {
                padding: 2rem;
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 1.6rem;
            }

            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.75);
                backdrop-filter: blur(4px);
                align-items: center;
                justify-content: center;
                z-index: 1000;
                font-family: monospace;
            }

            .modal-content {
                background: white;
                max-width: 550px;
                width: 90%;
                border-radius: 1.8rem;
                padding: 1.8rem 2rem 2rem;
                text-align: center;
                box-shadow: 0 25px 40px #63071e;
                animation: fadeSlide 0.2s ease;
            }

            .modal-content h3 {
                font-size: 1.6rem;
                margin-bottom: 0.75rem;
            }

            .modal-content p {
                margin: 1rem 0;
                color: #2d3e50;
                word-break: break-word;
            }

            .modal-buttons {
                display: flex;
                gap: 1rem;
                justify-content: center;
                margin-top: 1.5rem;
            }
        </style>
    </head>
    <body>
        <!-- Document modal window -->
        <div id="doc-modal" class="modal">
            <div class="modal-content">
                <h3 id="modalTitle"></h3>
                <p id="modalCategory"></p>
                <div class="modal-buttons">
                    <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <a id="downloadBtn" href="#" class="btn btn-primary" download>Download</a>
                </div>
            </div>
        </div>

        <!-- Document grid -->
        <div class="docs-grid" id="docsGrid">
            <!-- Document list -->
            <?php list_all_documents($documents);?>
        </div>
    </body>
</html>