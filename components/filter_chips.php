<?php
    require_once dirname(__DIR__). '/utils/loader.php';

    echo <<< HTML
        <link rel="stylesheet" href="/yanodash-repository/css/components/filter-chips.css">
    HTML;

    function filter_chips(array $filters = [], string $default = "All Documents") {
        $sanitizedDefault = htmlspecialchars($default);

        $buttons = [];
        foreach ($filters as $filter) {
            $sanitizedFilter = htmlspecialchars($filter);
            $buttonClasslist = $sanitizedFilter === $sanitizedDefault? "chip active" : "chip";


            $button = <<< HTML
                <button class="$buttonClasslist" data-value="$sanitizedFilter">$sanitizedFilter</button>
            HTML;
            array_push($buttons, $button);
        }
        $buttonsHTML = implode("\n", $buttons);

        return <<< HTML
            <div class="filter-chips" id="categoryChips">
                $buttonsHTML
            </div>
        HTML;
    }
?>