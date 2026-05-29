<?php
    function normalize_identifier(string $identifier): string {
        $identifier = trim($identifier);
        $identifier = mb_strtolower($identifier, 'UTF-8');
        $identifier = preg_replace('/\s+/', '-', $identifier);
        $identifier = preg_replace('/[^a-z0-9\-_:\.]/', '', $identifier);
        $identifier = preg_replace('/-+/', '-', $identifier);
        $identifier = trim($identifier, '-');

        return $identifier;
    }

    function normalize_title_for_download(string $title, bool $lowercase = true): string {
        $title = preg_replace('/[^a-zA-Z0-9]+/', '-', $title);
        $title = preg_replace('/-+/', '-', $title);
        $title = trim($title, '-');
        if ($lowercase) $title = strtolower($title);
        return $title;
    }
?>