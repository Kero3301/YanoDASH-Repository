<?php

require_once dirname(dirname(__DIR__)) . '/loader.php';
load('text_utils');

global $app_url;

echo <<<HTML
<link rel="stylesheet" href="$app_url/css/components/multiselect.css">
<script src="$app_url/script/multiselect.js" defer></script>
HTML;

function multiselect(
    string $id,
    string $name,
    string $label,
    array $options,
    string $allLabel = "ALL (*)",
    string $allValue = "*",
    bool $defaultAll = false
): string {

    $safeId = htmlspecialchars(normalize_identifier($id));
    $safeName = htmlspecialchars(normalize_identifier($name));
    $safeLabel = htmlspecialchars($label);

    $buttonId = "{$safeId}-button";
    $panelId  = "{$safeId}-panel";
    $allId    = "{$safeId}-all";

    // Build options HTML
    $optionsHtml = "";

    foreach ($options as $opt) {
        $optLabel = htmlspecialchars($opt['label']);
        $optValue = htmlspecialchars($opt['value']);

        $optId = $safeId . "-" . htmlspecialchars(normalize_identifier($opt['value']));

        $optionsHtml .= <<<HTML
        <label class="ms-option">
            <input type="checkbox"
                class="ms-item"
                name="{$safeName}[]"
                value="{$optValue}"
                data-group="{$safeId}">
            <span class="ms-text">{$optLabel}</span>
        </label>
        HTML;
    }

    $checkedAll = $defaultAll ? "checked" : "";

    return <<<HTML
    <div class="ms-dropdown" id="{$safeId}">
        <button type="button" class="ms-button" id="{$buttonId}">
            <span class="ms-label">{$safeLabel}</span>
        </button>

        <div class="ms-panel" id="{$panelId}">

            <label class="ms-option ms-all">
                <input type="checkbox"
                    id="{$allId}"
                    value="{$allValue}"
                    {$checkedAll}
                    data-all="{$safeId}">
                <span class="ms-text">{$allLabel}</span>
            </label>

            <hr>

            {$optionsHtml}

        </div>
    </div>
    HTML;
}