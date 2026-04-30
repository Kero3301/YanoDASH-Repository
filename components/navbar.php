<?php
    $isLoggedIn = isset($_SESSION['username']);
    $shouldShowPrivate = 
        $isLoggedIn && ($_SESSION['role'] === "admin" || $_SESSION['role'] === "editor");

    require_once 'menu.php';
    require_once 'sliding_switch.php';

    echo <<< HTML
        <link rel="stylesheet" href="/yanodash-repository/css/components/navbar.css">
        <script src="/yanodash-repository/script/navbar-hamburger.js"></script>
    HTML;

    function navbar(int $activeIndex = 0): string {
        global $isLoggedIn;
        global $shouldShowPrivate;

        $documents_activeness = $activeIndex === 1? "active" : "";
        $request_activeness = $activeIndex === 2? "active" : "";
        $privateArchive_activeness = $shouldShowPrivate && $activeIndex === 3? "active" : "";
        $dms_activeness = $shouldShowPrivate && $activeIndex === 4? "active" : "";
        $contact_activeness = $activeIndex === 5? "active": "";
        $about_activeness = $activeIndex === 6? "active": "";

        $documents_menu = menu("document-menu", [
            "Document Directory" => "/yanodash-repository/documents/",
            "Latest Releases" => "/yanodash-repository/documents/latest_rel.php",
            "Browse Public Archive" => "/yanodash-repository/documents/br_arch.php"
        ]);

        $request_menu = !$shouldShowPrivate
            ? ""
            : menu("request-menu", [
                "Request Menu" => "/yanodash-repository/request/request.php",
                "Request Document Archiving" => "/yanodash-repository/request/archive.php",
                "Requests Overview" => "/yanodash-repository/request/overview.php"
            ]);

        $privateArchive_menu = !$shouldShowPrivate
            ? ""
            : ($_SESSION['role'] === 'admin'
                ? menu("private-archive-menu", [
                    "Home" => "/yanodash-repository/admin-pages/index_priv-ar.php",
                    "Pending Archive Requests" => "/yanodash-repository/admin-pages/archive-rq.php",
                    "Important Documents" => "/yanodash-repository/admin-pages/key-docs.php"
                ])
                : menu("private-archive-menu", [
                    "Home" => "/yanodash-repository/admin-pages/index_priv-ar.php"                
                ]));

        $dms_menu = !$shouldShowPrivate
            ? ""
            : menu("dms-menu", [
                "Home" => "/yanodash-repository/dms/home/documents.php",
                "Add New Document" => "/yanodash-repository/dms/create.php",
                "Manage Documents" => "/yanodash-repository/dms/manage.php"
            ]);

        $about_menu = menu("about-menu", [
            "What is the OSC?" => "/yanodash-repository/about/obrero-student-council/about.php",
            "Meet the Executives" => "#",
        ]);

        $account_menu = menu("account-menu", [
            "Login" => "/yanodash-repository/auth/login.php",
            "Request an Account" => "/yanodash-repository/request-account"
        ], isDark: true);
        if ($isLoggedIn) {
            $fullname = $_SESSION['fullname'];
            $account_menu = $_SESSION['role'] !== "admin"
                ? menu("account-menu", [
                    "Logged in as:<br> <b><i>$fullname</i></b><br><p style='color: rgba(252, 151, 151, 0.9);'>Visit My Account</p>" => "/yanodash-repository/user/account.php",
                    "Logout" => "/yanodash-repository/auth/logout.php"
                ], isDark: true)
                : menu("account-menu", [
                    "Logged in as:<br> <b><i>$fullname</i></b><br><p style='color: rgba(252, 151, 151, 0.9);'>Visit My Account</p>" => "/yanodash-repository/user/account.php",
                    "Manage YanoDASH Accounts" => "/yanodash-repository/user/manage.php",
                    "Logout" => "/yanodash-repository/auth/logout.php"
                ], isDark: true);
            }
        
        $request_content = !$shouldShowPrivate
            ? ""
            : <<< HTML
                <div class="nav-item dropdown $request_activeness">
                    <a class="nav-item-link">
                        <h3>Request</h3>
                    </a>
                    $request_menu
                </div>
            HTML;

        $privateArchive_content = !$shouldShowPrivate
            ? ""
            : <<< HTML
                <div class="nav-item dropdown $privateArchive_activeness">
                    <a class="nav-item-link">
                        <h3>Private Archive</h3>
                    </a>
                    $privateArchive_menu
                </div>
            HTML;

        $dms_content = !$shouldShowPrivate
            ? ""
            : <<< HTML
                <div class="nav-item dropdown $dms_activeness">
                    <a class="nav-item-link">
                        <h3>DMS</h3>
                    </a>
                    $dms_menu
                </div>
            HTML;

        $colorModeSwitch = slidingSwitch("toggle-color-mode");

        return <<< HTML
            <div id="navbar">
                <a href="/yanodash-repository/">
                    <img src="/yanodash-repository/images/navbar-logo.png" draggable="false">
                </a>
                <a id="yanodash-home" href="/yanodash-repository">
                    <h1 style="user-select: none;">Yano<span id="dash-underline">DASH<span></h1>
                </a>

                <span id="vertical-bar"></span>

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
                    <div class="nav-item dropdown $documents_activeness">
                        <a class="nav-item-link" href="/yanodash-repository/documents/">
                            <h3>Documents</h3>
                        </a>
                        $documents_menu
                    </div>

                    $request_content

                    $privateArchive_content
                    
                    $dms_content

                    <div class="nav-item">
                        <a class="nav-item-link" href="#">
                            <h3>Contact</h3>
                        </a>
                    </div>

                    <div class="nav-item dropdown $about_activeness">
                        <a class="nav-item-link" href="/yanodash-repository/about/">
                            <h3>About</h3>
                        </a>
                        $about_menu
                    </div>

                    $colorModeSwitch

                    <div id="myaccount" class="dropdown" style="margin-left: auto; margin-right: 24px;">
                        <a style="cursor: pointer;">
                            <img src="/yanodash-repository/images/ui-indicators/account.png" draggable="false" style="width: 40px;">
                        </a>
                        $account_menu
                    </div>
                </div>
            </div>
        HTML;
    }
?>