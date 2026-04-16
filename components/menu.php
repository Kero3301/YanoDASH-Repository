<?php
    echo <<< HTML
        <link rel="stylesheet" type="text/css" href="/yanodash-repository/css/components/menu.css">
    HTML;

    function menu(string $id, array $choices, bool $isDark = false) {
        $sanitizedID = htmlspecialchars($id);
        $classList = $isDark? "menu dark" : "menu";
        
        $choiceList = [];
        foreach ($choices as $text => $link) {
            $sanitizedLink = htmlspecialchars($link);
            $choice = <<< HTML
                <a href="$sanitizedLink">$text</a>
            HTML;
            array_push($choiceList, $choice);
        }
        $choiceAsHTML = implode("\n", $choiceList);

        return <<< HTML
            <div id="$sanitizedID" class="$classList">
                $choiceAsHTML
            </div>
        HTML;
    }
?>