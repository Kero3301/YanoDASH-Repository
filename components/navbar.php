<?php
    session_start();
    $_SESSION['role'] = "editor";
    $_SESSION['userID'] = "12345678";

    $shouldShowSpace = 
        $_SESSION['role'] === "admin" || $_SESSION['role'] === "editor";

    $space_text = match ($_SESSION['role']) {
        default => '#',
        'admin' => 'Admin Space',
        'editor' => 'Editor Space'
    };
    $space_link = match ($_SESSION['role']) {
        default => '#',
        'admin' => '/yanodash-repository/admin-space?user='. $_SESSION['userID'],
        'editor' => '/yanodash-repository/editor-space?user='. $_SESSION['userID']
    };

    require_once 'menu.php';

    echo <<< HTML
        <link rel="stylesheet" href="/yanodash-repository/css/components/navbar.css">
        <script src="/yanodash-repository/script/navbar-hamburger.js"></script>
    HTML;

    function navbar(int $activeIndex = 0): string {
        global $shouldShowSpace;
        global $space_text;
        global $space_link;

        $documents_classList = $activeIndex == 1? "navbar-link active-link" : "navbar-link";
        $statistics_classList = $activeIndex == 2? "navbar-link active-link" : "navbar-link";
        $request_classList = $activeIndex == 3? "navbar-link active-link" : "navbar-link";
        $contact_classList = $activeIndex == 5? "navbar-link active-link" : "navbar-link";
        $about_classList = $activeIndex == 6? "navbar-link active-link" : "navbar-link";

        $test_dropdownMenu = menu("test-dropdown-menu", [
            "A" => "#",
            "B" => "#",
            "C" => "#"
        ]);
        $documents_dropdownMenu = menu("document-dropdown-menu", [
            "Document Directory" => "/yanodash-repository/documents/",
            "Latest Releases" => "/yanodash-repository/documents/latest_rel.php",
            "Browse Archive" => "/yanodash-repository/documents/br_arch.php"
        ]);
        $statistics_dropdownMenu = menu("statistics-dropdown-menu", [
            "General" => "#",
            "For Editors" => "#",
            "For Admins" => "#"
        ]);
        $request_dropdownMenu = menu("request-dropdown-menu", [
            "Request Menu" => "/yanodash-repository/request/request.php",
            "Request to Archive" => "/yanodash-repository/request/archive.php",
            "Track your Request" => "/yanodash-repository/request/track.php",
            "Requests Overview" => "/yanodash-repository/request/overview.php"
        ]);

        $space_menu = !$shouldShowSpace
            ? "" # Don't generate space-related menu if user doesn't have permissions (deny-by-default)
            : ( # Otherwise, determine whether user is admin or editor and display the right space-related menu accordingly
                $_SESSION['role'] === 'admin'
                    ? menu("space-dropdown-menu", [
                        "Pending Archive Requests" => "#",
                        "&emsp;General Documents" => "#",
                        "&emsp;Important Documents" => "#",
                        "Create Document" => "#",
                        "Manage Documents" => "#",
                        "Security" => "#",
                        "&emsp;Manage Document View Passwords" => "#",
                        "&emsp;Manage Access Control" => "#"
                    ])
                    : menu("space-dropdown-menu", [
                        "Create Document" => "/yanodash-repository/dms/project.php",
                        "Manage Documents" => "#",
                        "Security" => "#",
                        "&emsp;Manage Document View Passwords" => "#"
                    ])
            );

        $space_content = !$shouldShowSpace
            ? ""
            : <<< HTML
                <div class="nav-item dropdown" id="space-dropdown">
                    <a class="nav-item-link" href="$space_link">
                        <h3>$space_text</h3>
                    </a>
                    $space_menu
                </div>
            HTML;

        $about_dropdownMenu = menu("about-dropdown-menu", [
            "What is the OSC?" => "#",
            "Meet the Executives" => "#",
            "YanoDASH's Story" => "#"
        ]);
        $myaccount_dropdownMenu = menu("myaccount-dropdown-menu", [
            "Login" => "/yanodash-repository/login",
            "Request an Account" => "/yanodash-repository/request-account"
        ]);

        return <<< HTML
            <div id="navbar">
                <a href="/yanodash-repository/">
                    <img src="/yanodash-repository/images/navbar-logo.png" draggable="false">
                </a>
                <a href="/yanodash-repository"><h1 style="user-select: none;">YanoDASH</h1></a>
                <span id="vertical-bar" style="width: 2px; height: 32px; background-color: #71100F"></span>

                <button class="hamburger">
                    <div style="display: flex; flex-direction: row">
                        <div>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <h3 style="margin-top: auto; margin-bottom: auto; margin-left: 8px; color: #71100F">Menu</h3>
                    </div>
                </button>

                <div id="nav-links">
                    <div class="nav-item dropdown">
                        <a class="nav-item-link" href="/yanodash-repository/documents/">
                            <h3>Documents</h3>
                        </a>
                        $documents_dropdownMenu
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-item-link" href="/yanodash-repository/stats/">
                            <h3>Statistics</h3>
                        </a>
                        $statistics_dropdownMenu
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-item-link" href="/yanodash-repository/request/request.php">
                            <h3>Request</h3>
                        </a>
                        $request_dropdownMenu
                    </div>

                    $space_content

                    <div class="nav-item">
                        <a class="nav-item-link" href="#">
                            <h3>Contact</h3>
                        </a>
                    </div>

                    <div class="nav-item dropdown" id="about-dropdown">
                        <a class="nav-item-link" href="/yanodash-repository/about/">
                            <h3>About</h3>
                        </a>
                        $about_dropdownMenu
                    </div>

                    <div id="myaccount" class="dropdown" style="margin-left: auto; margin-right: 24px;">
                        <a style="cursor: pointer;">
                            <img src="/yanodash-repository/images/ui-indicators/account.png" draggable="false" style="width: 40px;">
                        </a>
                        $myaccount_dropdownMenu
                    </div>
                </div>
            </div>
        HTML;
    }
?>