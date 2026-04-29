<?php
    require_once __DIR__ . '/../utils/TextUtils.php';

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="/yanodash-repository/css/components/sliding-switch.css">
    HTML;

    function slidingSwitch(string $id, string $offIconHref = "", string $onIconHref = "") {
        $sanitizedID = htmlspecialchars(TextUtils::sanitizeIdentifier($id));
        $sanitizedInputID = $sanitizedID. "-input";

        return <<< HTML
            <label id="$sanitizedID" class="switch">
                <input id="$sanitizedInputID" type="checkbox">
                <span class="slider round"></span>
            </label>
        HTML;
    }
?>