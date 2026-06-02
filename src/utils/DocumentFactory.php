<?php
require_once dirname(__DIR__). '/database/mongodb.php';
require_once dirname(__DIR__). '/models/DocEd.php';

final class DocumentFactory {
    public static function getDoc(mixed $docData): ?Document {
        if (!is_array($docData)) return null;

        # Attribute storage
        $_ID = $docData['_id'];
        $TITLE = $docData['doc_title'];
        $DESCRIPTION = $docData['doc_description'] ?? $docData['description'] ?? "(no description)";
        $CATEGORY = $docData['doc_category'] ?? $docData['category'] ?? "(unknown)";
        $TAGS = $docData['doc_tags'] ?? [];
        $AUTHOR = "(unknown)";
        $ORIGIN_AREA = $docData['area_of_origin'];
        $STATUS = $docData['doc_status'];
        $TC = $docData['tracking_code'];
        $DATES = $docData['dates'];
        $CURRENT_VERSION = $docData['current_version'];
        $METADATA = $docData['category_data'] ?? [];

        # Data transformation and resolution
        

        return new Document(
            _id: $docData['_id'],
            doc_title: $docData['doc_title'],
            doc_description: $docData['doc_description'] ?? "(no description)",
            doc_category: $docData['doc_category'],
            doc_tags: $docData['doc_tags'],
            author: $docData['author'],
            area_of_origin: $docData['area_of_origin'],
            doc_status: $docData['doc_status'],
            tracking_code: $docData['tracking_code'],
            dates: $docData['dates'],
            current_version: $docData['current_version'] ?? 1,
            category_data: $docData['category_data'] ?? []
        );
    }

    public static function getAll($results): array {
        $documentList = [];
        foreach ($results as $result)
            array_push($documentList, DocumentFactory::getDoc($result));
        return $documentList;
    }
}
?>