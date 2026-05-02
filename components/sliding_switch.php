<?php
    require_once dirname(__DIR__). '/utils/loader.php';
    load_utils('text_utils');
    
    global $app_url;

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="$app_url/css/components/sliding-switch.css">
    HTML;

    function sliding_switch(string $id, string $offIconHref = "", string $onIconHref = "") {
        $sanitizedID = htmlspecialchars(normalize_identifier($id));
        $sanitizedInputID = $sanitizedID. "-input";

        return <<< HTML
            <label id="$sanitizedID" class="switch">
                <input id="$sanitizedInputID" type="checkbox">
                <span class="slider round"></span>
            </label>
        HTML;
    }
?>