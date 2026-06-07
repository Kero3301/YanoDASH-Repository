<?php
require_once dirname(__DIR__, 2). '/utils/text_utils.php';

function svg($template, $dimensions = [20, 20], $id = null): string {
    # Define path templates registry
    static $pathTemplates = [
        'chevron' => 
        <<< HTML
            <path d="M14 6 L8 12 L14 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        HTML
    ];

    # Validate class type and value
    if (!is_string($template) || trim($template) === '') return '';

    # Search path templates associative array for corresponding value
    if (!isset($pathTemplates[$template])) return '';
    $path = $pathTemplates[$template];

    # Validate dimensions
    if (count($dimensions) !== 2) return '';
    foreach ($dimensions as $d) if (!is_int($d) || $d <= 0) return '';
    [$w, $h] = $dimensions;

    # Escape ID for output
    $sanitizedID = is_string($id) && trim($id) !== ''
        ? htmlspecialchars(normalize_identifier($id))
        : null;

    # Determine ID attribute to be given
    $idAttr = !is_null($sanitizedID)
        ? "id=\"$sanitizedID\""
        : '';

    # Return HTML string
    return <<< HTML
        <svg $idAttr viewBox="0 0 24 24" width="$w" height="$h">
            $path
        </svg>
    HTML;
}
?>