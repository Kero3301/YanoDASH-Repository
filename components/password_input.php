<?php
    require_once '../utils/TextUtils.php';
    require_once 'password_input_event_handlers.php';

    # Define the minimum dimensions (width and height), in px unit, allowed for the password input
    const MIN_WIDTH = 200;
    const MAX_WIDTH = 400;
    const MIN_HEIGHT = 32;
    const MAX_HEIGHT = 64;

    # Use this function to create a password input component with a specified unique ID, a name for the underlying input field, and an optional watermark
    function passwordInput(string $id, string $inputName, string $watermark = "Password", int $width = MIN_WIDTH, int $height = MIN_HEIGHT): string {
        # Sanitize and escape the string parameters to prevent reflected XSS attack
        $sanitizedID = htmlspecialchars(TextUtils::sanitizeIdentifier($id));
        $sanitizedInputName = $sanitizedID. "-inputfield";
        $sanitizedWatermark = htmlspecialchars($watermark);

        # Ensure that the final dimensions of the password input never goes beyond the min or max allowed width and height
        $finalWidth = max(MIN_WIDTH, min($width, MAX_WIDTH));
        $finalHeight = max(MIN_HEIGHT, min($height, MAX_HEIGHT));

        # Create the event handler script for when the toggle password visibility button of this password field is clicked
        $v = $finalWidth - $finalHeight;
        $html = <<< HTML
            <div id="$sanitizedID" class="password-input" style="width: {$finalWidth}px; height: {$finalHeight}px; border: 2px solid #ddd; display: grid; grid-template-columns: auto {$finalHeight}px; box-sizing: border-box;">
                <input id="$sanitizedInputName" class="password-input-field" name="$sanitizedInputName" type="password" placeholder="$watermark" style="grid-column: 1; border: none; width: 100%; height: 100%; box-sizing: border-box; padding: 0 4px;">
                <button type="button" style="grid-column: 2; border: none;" onclick="togglePasswordVisibility(this)">⊘</button>
            </div>
        HTML;
        return $html;
    }
?>