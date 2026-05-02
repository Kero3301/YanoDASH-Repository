<?php
    require_once dirname(__DIR__). '/utils/loader.php';
    load_utils('text_utils');
    
    global $app_url;

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="$app_url/css/components/accordion.css">
        <script src="$app_url/script/accordion.js"></script>
    HTML;

    function accordion(string $id, array $entries, bool $allowMultipleOpen = false) {
        $sanitizedID = htmlspecialchars(normalize_identifier($id));

        $entriesList = [];
        foreach ($entries as $summary => $content) {
            $sanitizedSummary = htmlspecialchars($summary);
            $sanitizedContent = htmlspecialchars($content);
            $entry = <<< HTML
                <button class="accordion">$sanitizedSummary</button>
                <div class="panel">
                    <p>$sanitizedContent</p>
                </div>
            HTML;
            array_push($entriesList, $entry);
        }

        $entriesHTML = implode("\n", $entriesList);
        $classlist = $allowMultipleOpen? "accordion-container multiple-open" : "accordion-container";
        
        return <<< HTML
            <div id="$sanitizedID" class="$classlist">
                $entriesHTML        
            </div> 
        HTML;
    }
?>