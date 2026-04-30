<?php
    require_once '../utils/TextUtils.php';

    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="/yanodash-repository/css/components/password-input.css">
    HTML;

    const MIN_WIDTH = 200;
    const MAX_WIDTH = 400;
    const MIN_HEIGHT = 32;
    const MAX_HEIGHT = 64;

    function passwordInput(
        string $id, 
        string $inputName, 
        string $watermark = "Password", 
        int $width = MIN_WIDTH, 
        int $height = MIN_HEIGHT
    ): string {
        $sanitizedID = htmlspecialchars(TextUtils::sanitizeIdentifier($id));
        $sanitizedInputID = $sanitizedID. "-inputfield";
        $sanitizedInputName = htmlspecialchars(TextUtils::sanitizeIdentifier($inputName));
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