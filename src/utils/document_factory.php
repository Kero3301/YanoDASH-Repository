<?php
require_once dirname(__DIR__). '/models/DocEd.php';

function get_doc($docData): ?Document {
    return new Document(
        _id: $docData -> _id,
        doc_title: $docData -> doc_title,
        doc_type: $docData -> doc_type ?? "(unknown)",
        categories: $docData -> doc_categories->getArrayCopy(),
        status: $docData -> doc_status,
        description: $docData -> description ?? "(no description)",
        keywords: isset($docData->keywords)? $docData->keywords->getArrayCopy() : [],
        area_of_origin: $docData -> area_of_origin,
        author: $docData -> author,
        dates: $docData -> dates->getArrayCopy(),
        version: $docData -> current_version_id ?? 0,
        tracking_code: $docData -> tracking_code,
        main_category: $docData -> main_category,
        is_publicized: $docData -> is_publicized
    );
}

function get_all($results): array {
    $documentList = [];
    foreach ($results as $result)
        array_push($documentList, get_doc($result));
    return $documentList;
}
?>