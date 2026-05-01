<?php
    require_once __DIR__. '/../utils/loader.php';   
    load_components(
        'menu',
        'sliding_switch'
    );

    global $app_url;

    echo <<< HTML
        <link rel="stylesheet" href="$app_url/css/components/navbar.css">
        <script src="$app_url/script/navbar-hamburger.js"></script>
    HTML;

    function navbar(int $activeIndex = 0): string {
        global $app_url;
        
        $isLoggedIn = isset($_SESSION['username']);
        $shouldShowPrivate = 
            $isLoggedIn && ($_SESSION['role'] === "admin" || $_SESSION['role'] === "editor");

        $documents_activeness = $activeIndex === 1? "active" : "";
        $request_activeness = $activeIndex === 2? "active" : "";
        $privateArchive_activeness = $shouldShowPrivate && $activeIndex === 3? "active" : "";
        $dms_activeness = $shouldShowPrivate && $activeIndex === 4? "active" : "";
        $contact_activeness = $activeIndex === 5? "active": "";
        $about_activeness = $activeIndex === 6? "active": "";

        $documents_menu = menu("document-menu", [
            "Document Directory" 
                => "$app_url/documents",

            "Latest Releases" 
                => "$app_url/documents/latest_rel.php",

            "Browse Public Archive" 
                => "$app_url/documents/br_arch.php"
        ]);

        $request_menu = !$shouldShowPrivate
            ? ""
            : menu("request-menu", [
                "Request Menu" 
                    => "$app_url/request",

                "Request Document Archiving" 
                    => "$app_url/request/archive.php",

                "Requests Overview" 
                    => "$app_url/request/overview.php"
            ]);

        $privateArchive_menu = !$shouldShowPrivate
            ? ""
            : ($_SESSION['role'] === 'admin'
                ? menu("private-archive-menu", [
                    "Home" 
                        => "$app_url/private-archive/index_priv-ar.php",

                    "Pending Archive Requests" 
                        => "$app_url/private-archive/archive-rq.php",
                    
                    "Important Documents" 
                        => "$app_url/private-archive/key-docs.php"
                ])
                : menu("private-archive-menu", [
                    "Home" 
                        => "$app_url/private-archive/index_priv-ar.php"                
                ]));

        $dms_menu = !$shouldShowPrivate
            ? ""
            : menu("dms-menu", [
                "Home" 
                    => "$app_url/dms/",

                "Add New Document" 
                    => "$app_url/dms/add-document",

                "Manage Documents" 
                    => "$app_url/dms/manage-documents"
            ]);

        $about_menu = menu("about-menu", [
            "What is the OSC?" 
                => "$app_url/about/osc",

            "Meet the Executives" 
                => "#",
        ]);

        $account_menu = menu("account-menu", [
            "Login" 
                => "$app_url/auth/login.php",

            "Request an Account" 
                => "$app_url/auth/request-account.php"
        ], isDark: true);

        if ($isLoggedIn) {
            $fullname = $_SESSION['fullname'];
            $account_menu = $_SESSION['role'] !== "admin"
                ? menu("account-menu", [
                    "Logged in as:<br> <b><i>$fullname</i></b><br><p style='color: rgba(252, 151, 151, 0.9);'>Visit My Account</p>" 
                        => "$app_url/account/my-account.php",
                    
                    "Logout" 
                        => "$app_url/auth/logout.php"
                ], isDark: true)
                : menu("account-menu", [
                    "Logged in as:<br> <b><i>$fullname</i></b><br><p style='color: rgba(252, 151, 151, 0.9);'>Visit My Account</p>" 
                        => "$app_url/account/my-account.php",
                    
                    "Manage YanoDASH Accounts" 
                        => "$app_url/admin/manage-accounts.php",
                    
                    "Logout" 
                        => "$app_url/auth/logout.php"
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

        $colorModeSwitch = sliding_switch("toggle-color-mode");

        return <<< HTML
            <div id="navbar">
                <a href="$app_url">
                    <img src="$app_url/images/navbar-logo.png" draggable="false">
                </a>
                <a id="yanodash-home" href="$app_url">
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
                        <a class="nav-item-link" href="$app_url/documents/">
                            <h3>Documents</h3>
                        </a>
                        $documents_menu
                    </div>

                    $request_content

                    $privateArchive_content
                    
                    $dms_content

                    <div class="nav-item">
                        <a class="nav-item-link" href="$app_url/contact/">
                            <h3>Contact</h3>
                        </a>
                    </div>

                    <div class="nav-item dropdown $about_activeness">
                        <a class="nav-item-link" href="$app_url/about/">
                            <h3>About</h3>
                        </a>
                        $about_menu
                    </div>

                    $colorModeSwitch

                    <div id="myaccount" class="dropdown" style="margin-left: auto; margin-right: 24px;">
                        <a style="cursor: pointer;">
                            <img src="$app_url/images/ui-indicators/account.png" draggable="false" style="width: 40px;">
                        </a>
                        $account_menu
                    </div>
                </div>
            </div>
        HTML;
    }
?>