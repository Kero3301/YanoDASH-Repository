<?php
    require __DIR__. '/../utils/TextUtils.php';

    # A function to create a main section with a specified element ID, text, content, background image, background tint,
    # and inner content alignment (0 = left, 1 = center, 2 = right)
    function mainSection(
        string $id, 
        string $title, 
        string $content, 
        string $bgImage, 
        string $bgImageTint, 
        int $alignment = 0,
        array $linksList = [])
        : string {

        # Escaped outputs
        $sanitizedID = htmlspecialchars(TextUtils::sanitizeIdentifier($id));
        $sanitizedTitle = htmlspecialchars($title);
        $sanitizedContent = htmlspecialchars($content);
        $sanitizedBGImage = htmlspecialchars($bgImage);
        $sanitizedBGImageTint = htmlspecialchars($bgImageTint);
        
        $buttonsList = [];

        if (count($linksList) > 0) {
            foreach ($linksList as $link) {
                $sanitizedLabel = htmlspecialchars($link[0]);
                $sanitizedLink = htmlspecialchars($link[1]);
                $sanitizedButtonID = htmlspecialchars(TextUtils::sanitizeIdentifier($link[2]));

                $html = <<< HTML
                    <a href="$sanitizedLink" id="$sanitizedButtonID" style="text-decoration: none;">
                        <button style="padding: 8px; border: none; border-radius: 16px; cursor: pointer;">
                            $sanitizedLabel
                        </button>
                    </a>
                HTML;
                array_push($buttonsList, $html);
            }
        }

        $outputButtons = "";
        if (count($buttonsList) > 0) $outputButtons = implode("\n", $buttonsList); 
        

        # Dynamic outputs
        $alignmentClass = match ($alignment) {
            default => "left-contents",
            0 => "left-contents",
            1 => "center-contents",
            2 => "right-contents"
        };
        $classList = "main-section $alignmentClass";

        return <<< HTML
            <div class="$classList" style="
                background: linear-gradient($sanitizedBGImageTint, $sanitizedBGImageTint), url('$sanitizedBGImage');
                background-size: cover;
                background-position: center;
                margin: 0;">
                <div class="content-section">
                    <h1 id="{$sanitizedID}-title" class="content-title">$sanitizedTitle</h1>
                    <p id="{$sanitizedID}-text" class="content-text">$sanitizedContent</p>
                    $outputButtons
                </div>
            </div>
        HTML;
    }
?>