<?php
    require_once dirname(__DIR__). '/utils/loader.php';
    load_utils('text_utils');
    load_components('filter_chips');

    echo <<< HTML
        <link rel="stylesheet" href="$app_url/css/components/page-header.css">
    HTML;

    function page_header(string $title, bool $showTopnav = true, bool $showChips = true) {
        $sanitizedTitle = htmlspecialchars($title);
        $topnav = $showTopnav
            ? <<< HTML
                <div class="page-header-topnav">
                    <a href="index.php" id="b-back">Back to Index</a>
                        <div class="search-panel">
                            <div class="search-wrapper">
                                <input type="text" id="searchInput" placeholder="Search by title, description, or tracking code..." autocomplete="off">
                            </div>
                    </div>
                </div>
            HTML
            : "";
        $chips = $showChips
            ? filter_chips(["All Documents", "Activity Design", "Memorandum", "Financial Statement", "Meeting Minutes", "Accomplishment Report", "Project Proposal"])
            : "";

        return <<< HTML
            <div class="page-header">
                <!-- NAV -->
                $topnav

                <!-- TITLE -->
                <header id="title"> 
                    <h1>$sanitizedTitle</h1>
                    $chips
                </header>
            </div> 
        HTML;
    }
?>