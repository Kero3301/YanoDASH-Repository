<?php
    require_once dirname(dirname(__DIR__)). '/loader.php';
    load('text_utils');

    global $app_url;

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="$app_url/css/components/password-input.css">
    HTML;

    const MIN_WIDTH = 200;
    const MAX_WIDTH = 400;
    const MIN_HEIGHT = 32;
    const MAX_HEIGHT = 64;

    function password_input(
        string $id, 
        string $inputName, 
        string $watermark = "Password", 
        int $width = MIN_WIDTH, 
        int $height = MIN_HEIGHT
    ): string {
        $sanitizedID = htmlspecialchars(normalize_identifier($id));
        $sanitizedInputID = $sanitizedID. "-inputfield";
        $sanitizedInputName = htmlspecialchars(normalize_identifier($inputName));
        $sanitizedWatermark = htmlspecialchars($watermark);
        $sanitizedButtonID = $sanitizedID. "-visibilitytoggle";

        $finalWidth = max(MIN_WIDTH, min($width, MAX_WIDTH));
        $finalHeight = max(MIN_HEIGHT, min($height, MAX_HEIGHT));

        return <<< HTML
            <div id="$sanitizedID" class="password-input-wrapper" style="--w: {$finalWidth}px; --h: {$finalHeight}px;">
                <div class="password-input">
                    <input id="$sanitizedInputID" class="password-input-field" name="$sanitizedInputName" type="password" placeholder="$sanitizedWatermark" required>
                    <button id="$sanitizedButtonID" class="toggle-visibility" type="button" onclick="togglePasswordVisibility(this)">⊘</button>
                </div>
            </div>
        HTML;
    }
?>