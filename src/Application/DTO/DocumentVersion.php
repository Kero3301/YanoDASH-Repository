<?php
class DocumentVersion
{
    public function __construct(
        public string $_id,
        public string $document,
        public int $version_number,
        public string $file_url
    ) {}
}
?>