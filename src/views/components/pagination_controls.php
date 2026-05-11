<?php

echo <<< HTML
    <link rel="stylesheet" type="text/css" href="$app_url/css/components/pagination-controls.css">
HTML;

function pagination_controls(int $currentPage, int $totalPage) {
    $previousPage = max(1, $currentPage - 1);
    $nextPage = min($totalPage, $currentPage + 1);

    $previousDisabled = $currentPage <= 1;
    $nextDisabled = $currentPage >= $totalPage;

    $leftArrowClass = $previousDisabled
    ? 'left-arrow disabled-arrow'
    : 'left-arrow';

    $rightArrowClass = $nextDisabled
        ? 'right-arrow disabled-arrow'
        : 'right-arrow';

    return <<<HTML
    <div class="pagination-controls">
        <div class="arrow-navigation">
            <a class="$leftArrowClass" href="?page=$previousPage" aria-label="Previous Page">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
            </a>

            <h2 class="arrow-navigation-label">
                Page <span class="current-page">$currentPage</span> of $totalPage
            </h2>

            <a class="$rightArrowClass" href="?page=$nextPage" aria-label="Next Page">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9 6l6 6-6 6" />
                </svg>
            </a>
        </div>

        <div class="input-navigation">
            Go to page:
            <input type="number" class="page-number" min="1" max="$totalPage" value="$currentPage">

            <button class="go-page-button">Go</button>
        </div>
    </div>
    HTML;
}
?>