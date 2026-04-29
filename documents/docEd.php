<?php
// Document Central Data
class Documents {
    public function __construct(
        public string $doc_title,
        public string $category,
        public string $area,
        public string $author,
        public string $dates,
        public string $file_path, 
        public string $tracking_code,
        public int $version = 1
    ) {}

    public static function getAll(): array {
        return [
            [ 
                "doc_title" => "Event Report", 
                "category" => "Accomplishment Report", 
                "area" => "OSC Office", 
                "author" => "Jane Doe", 
                "dates" => "2026-04-10", 
                "file_path" => "event_report_v1.pdf", 
                "tracking_code" => "TRC-0001",
                "version" => 1
            ],
            [
                "doc_title" => "Hudyaka", 
                "category" => "Accomplishment Report", 
                "area" => "OSC Office", 
                "author" => "Anu Tuwa", 
                "dates" => "2026-04-26", 
                "file_path" => "hudyaka_v2.pdf", 
                "tracking_code" => "TRC-0002",
                "version" => 2
            ],
             [
                "doc_title" => "Fest", 
                "category" => ["Activity Designs", "Financial Statements"], 
                "area" => "CIC LC", 
                "author" => "Anu Tuwa", 
                "dates" => "2026-03-26", 
                "file_path" => "cicfest_v1.pdf", 
                "tracking_code" => "TRC-0003",
                "version" => 1
            ],
             [
                "doc_title" => "Fest Meeting", 
                "category" => "Minutes of Meetings", 
                "area" => "CT LC", 
                "author" => "Anu Tuwa", 
                "dates" => "2026-04-26", 
                "file_path" => "ctfest_v1.pdf", 
                "tracking_code" => "TRC-0004",
                "version" => 1
            ],
             [
                "doc_title" => "Ha Ha Ha", 
                "category" => "Project Proposal", 
                "area" => "OSC Office", 
                "author" => "Anu Tuwa", 
                "dates" => "2025-01-26", 
                "file_path" => "hihi.pdf", 
                "tracking_code" => "TRC-0005",
                "version" => 1
            ]
        ];
    }
}
?>