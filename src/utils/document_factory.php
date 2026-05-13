<?php
require_once dirname(__DIR__). '/models/DocEd.php';

function get_doc($docData): ?Document {
    return new Document(
        _id: $docData -> _id,
        doc_title: $docData -> doc_title,
        doc_category: $docData -> doc_category,
        doc_tags: $docData -> doc_tags->getArrayCopy(),
        author: $docData -> author,
        area_of_origin: $docData -> area_of_origin,
        doc_status: $docData -> doc_status,
        tracking_code: $docData -> tracking_code,
        dates: $docData -> dates->getArrayCopy(),
        version: $docData -> current_version,
        category_data: $docData -> category_data->getArrayCopy()
    );
}

function get_all($results): array {
    $documentList = [];
    foreach ($results as $result)
        array_push($documentList, get_doc($result));
    return $documentList;
}
?>