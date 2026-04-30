<?php
    require_once __DIR__. '/../utils/text_utils.php';

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="/yanodash-repository/css/components/sliding-switch.css">
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