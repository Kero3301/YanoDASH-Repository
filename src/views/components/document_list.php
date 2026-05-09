<?php
require_once dirname(dirname(__DIR__)). '/loader.php';
load('document_card', 'doc_ed');

function list_document($doc) {
    echo document_card($doc);
}

function list_all_documents(array $docs) {
    foreach ($docs as $doc) 
        if ($doc instanceof Document) list_document($doc);
}
?>