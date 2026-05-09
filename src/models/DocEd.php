<?php
# Document Central Data
class Document {
    public function __construct(
        public string $_id,
        public string $doc_title,
        public ?string $doc_type,
        public array $categories,
        public string $status,
        public ?string $description,
        public ?array $keywords,
        public string $area_of_origin,
        public string $author,
        public array $dates,
        public int $version,
        public string $tracking_code,
        public string $main_category,
        public bool $is_publicized
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