<?php
require_once dirname(__DIR__). '/utils/loader.php';
require_once 'document_card.php';
load_utils('data/DocEd');

function list_document($doc) {
    echo document_card($doc);
}

function list_all_documents(array $docs) {
    foreach ($docs as $doc) 
        if ($doc instanceof Document) list_document($doc);
}
?>