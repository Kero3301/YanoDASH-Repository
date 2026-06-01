<?php
# Document Central Data
class Document {
    public function __construct(
        public string $_id,
        public string $doc_title,
        public ?string $doc_description,
        public string $doc_category,
        public ?array $doc_tags,
        public string $author,
        public string $area_of_origin,
        public string $doc_status,
        public string $tracking_code,
        public array $dates,
        public int $current_version,
        public ?array $category_data
    ) {}
}

class DocumentVersion {
    public function __construct(
        public string $_id,
        public string $doc_id,
        public int $version_number,
        public string $description = "",
        public string $file_path,
        public string $date_added
    ) {}
}
?>