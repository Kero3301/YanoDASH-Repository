<?php
require_once dirname(dirname(__DIR__)). '/loader.php';
load (
    'authenticator',
    'authorizer',
    'user_profile_service',
    'iam_context_validator',
    'menu',
    'sliding_switch'
);

global $app_url;
echo <<< HTML
    <link rel="stylesheet" href="$app_url/css/components/navbar.css">
    <script src="$app_url/script/navbar-hamburger.js"></script>
HTML;

function navbar($user): string {
    global $app_url;
    # Default common public links
    $archiveMenu = menu("archive-menu", [
        "<p class=\"menu-category-label\">PUBLIC ARCHIVE</p>" 
            => "#",
        "Latest Releases"
            => "$app_url/archive/public/latest_rel.php",
        "All Documents"
            => "$app_url/archive/public/br_arch.php"
    ]);

    $statisticsLink = <<< HTML
        <div class="nav-item">
            <a class="nav-item-link" href="$app_url/statistics/general-stat.php">
                <h3>Statistics</h3>
            </a>
        </div>
    HTML;

    $contactLink = <<< HTML
        <div class="nav-item">
            <a class="nav-item-link" href="$app_url/contact/">
                <h3>Contact</h3>
            </a>
        </div>
    HTML;

    $aboutMenu = menu("about-menu", [
        "What is the OSC?" 
            => "$app_url/about/osc",
        "Meet the Executives" 
            => "$app_url/about/executives/executives.php",
    ]);
    $aboutContent = <<< HTML
        <div class="nav-item dropdown">
            <a class="nav-item-link" href="#">
                <h3>About</h3>
            </a>
            $aboutMenu
        </div>
    HTML;

    $accountMenu = menu("account-menu", [
        "Login" 
            => "$app_url/auth/login.php",
        "Request an Account" 
            => "$app_url/auth/request-account.php"
    ], isDark: true);

    # Common UI elements
    $navTitle = <<< HTML
        <a href="$app_url">
            <img src="$app_url/images/navbar-logo.png" draggable="false">
        </a>
        <a id="yanodash-home" href="$app_url">
            <h1 style="user-select: none;">Yano<span id="dash-underline">DASH<span></h1>
        </a>

        <span id="vertical-bar"></span>
    HTML;

    $hamburger = <<< HTML
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
    HTML;
    
    # Validate user status
    $userValidity = IAMContextValidator::validate($user);
    switch ($userValidity) {
        # User is logged in
        case true:
            # Full name for display
            $fullName = Profile::fullName($user['PROFILE']);

            # Access level check
            $isAdmin = Authorizer::isAdmin($user);
            $isEditor = Authorizer::isEditor($user);
            $showGenProtected = $isAdmin === true || $isEditor === true;

            # Archive Menu
            $archiveMenu = $isAdmin
                ? menu("archive-menu", [
                    "<p class=\"menu-category-label\">PUBLIC ARCHIVE</p>" 
                        => "#",

                    "Latest Releases"
                        => "$app_url/archive/public/latest_rel.php",
                    
                    "All Documents"
                        => "$app_url/archive/public/br_arch.php",
                    
                    "<p class=\"menu-category-label\">PRIVATE ARCHIVE</p>"
                        => "#",

                    "Private Documents" 
                        => "$app_url/archive/private/",

                    "Request..."
                        => "$app_url/archive/private/request/",

                    "Pending Archive Requests" 
                        => "$app_url/private-archive/archive-rq.php",
                ]): ($isEditor
                        ? menu("archive-menu", [
                            "<p class=\"menu-category-label\">PUBLIC ARCHIVE</p>"
                                => "#",
                            
                            "Latest Releases"
                                => "$app_url/archive/public/latest_rel.php",
                            
                            "All Documents"
                                => "$app_url/archive/public/br_arch.php",

                            "<p class=\"menu-category-label\">PRIVATE ARCHIVE</p>"
                                => "#",

                            "Private Documents" 
                                => "$app_url/private-archive/",

                            "Request..."
                                => "$app_url/archive/private/request",
                        ])
                        : $archiveMenu
                );

            # DMS Menu
            $dmsMenu = !$showGenProtected
                ? ""
                : menu("dms-menu", [
                    "Home" 
                        => "$app_url/dms/",

                    "Add New Document" 
                        => "$app_url/dms/add-document",

                    "Manage Documents" 
                        => "$app_url/dms/manage-documents"
                ]); 
            $dmsContent = !$showGenProtected
                ? ""
                : <<< HTML
                    <div class="nav-item dropdown">
                        <a class="nav-item-link">
                            <h3>DMS</h3>
                        </a>
                        $dmsMenu
                    </div>
                HTML;

            # Account Menu
            $accountMenu = $isAdmin
                ? menu("account-menu", [
                    "Logged in as:<br> <b><i>$fullName</i></b><br><p style='color: rgba(252, 151, 151, 0.9);'>Visit My Account</p>" 
                        => "$app_url/account/my-account.php",

                    "Admin Space"
                        => "$app_url/admin/",

                    "Logout" 
                        => "$app_url/auth/logout.php"
                ], isDark: true)
                : menu("account-menu", [
                    "Logged in as:<br> <b><i>$fullName</i></b><br><p style='color: rgba(252, 151, 151, 0.9);'>Visit My Account</p>" 
                        => "$app_url/account/my-account.php",
                    
                    "Logout"
                        => "$app_url/auth/logout.php"
                ], isDark: true);
            $accountContent = <<< HTML
                <div id="myaccount" class="dropdown" style="margin-left: auto; margin-right: 24px;">
                    <a style="cursor: pointer;">
                        <img src="$app_url/images/ui-indicators/account.png" draggable="false" style="width: 40px;">
                    </a>
                    $accountMenu
                </div>
            HTML;

            return <<< HTML
                <nav id="navbar">
                    $navTitle
                    $hamburger
                    <div id="nav-links">
                        <div id="nav-highlight"></div>

                        <!-- Archive -->
                        <div class="nav-item dropdown">
                            <a class="nav-item-link" href="#">
                                <h3>Archive</h3>
                            </a>
                            $archiveMenu
                        </div>

                        <!-- DMS -->
                        $dmsContent

                        <!-- Statistics -->
                        $statisticsLink

                        <!-- Contact -->
                        $contactLink

                        <!-- About -->
                        $aboutContent

                        <!-- Account -->
                        $accountContent
                    </div>
                </nav>
            HTML;
        # User is not logged in or is invalid
        case false: default:
            $accountContent = <<< HTML
                <div id="myaccount" class="dropdown" style="margin-left: auto; margin-right: 24px;">
                    <a style="cursor: pointer;">
                        <img src="$app_url/images/ui-indicators/account.png" draggable="false" style="width: 40px;">
                    </a>
                    $accountMenu
                </div>
            HTML;

            return <<< HTML
                <nav id="navbar">
                    $navTitle
                    $hamburger
                    <div id="nav-links">
                        <div id="nav-highlight"></div>

                        <!-- Archive -->
                        <div class="nav-item dropdown">
                            <a class="nav-item-link" href="#">
                                <h3>Archive</h3>
                            </a>
                            $archiveMenu
                        </div>

                        <!-- Statistics -->
                        $statisticsLink

                        <!-- Contact -->
                        $contactLink

                        <!-- About -->
                        $aboutContent

                        <!-- Account -->
                        $accountContent
                    </div>
                </nav>
            HTML;
    }
}
?>