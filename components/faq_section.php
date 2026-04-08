<?php
    require '../utils/TextUtils.php';

    function FAQSection(string $id, array $entries, string $label = "Frequently Asked Questions"): string {
        # Sanitize the received inputs for the string parameters
        $sanitizedID = htmlspecialchars(TextUtils::sanitizeIdentifier($id));
        $sanitizedLabel = htmlspecialchars($label);
        
        # Extract the specified entries key-value pairs and convert them into a proper expandable/collapsible section for Q&A details
        $entryList = [];
        # Read each entry as a key-value pair of question-answer
        foreach ($entries as $question => $answer) {
            $sanitizedQuestion = htmlspecialchars($question);
            $sanitizedAnswer = htmlspecialchars($answer);
            $entry = <<< HTML
                <details>
                    <summary>$sanitizedQuestion</summary>
                    <p>&emsp;&emsp;$sanitizedAnswer</p>
                </details>
            HTML;
            array_push($entryList, $entry);
        }
        $finalEntryList = implode("\n", $entryList);

        $html = <<< HTML
            <div id="$sanitizedID">
                <h1>$sanitizedLabel</h1>
                $finalEntryList
            </div>
        HTML;
        return $html;
    }
?>