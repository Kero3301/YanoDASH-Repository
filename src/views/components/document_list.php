<?php
require_once dirname(dirname(__DIR__)). '/loader.php';
load('document_card', 'doc_ed');

global $_CURRENTUSER;

function list_document($doc) {
    global $_CURRENTUSER;
    echo document_card($doc, $_CURRENTUSER);
}

function list_all_documents(array $docs) {
    global $_CURRENTUSER;
    foreach ($docs as $doc) 
        if ($doc instanceof Document) list_document($doc);
}
?>