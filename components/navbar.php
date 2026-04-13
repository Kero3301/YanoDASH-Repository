<?php
    # We include the dropdown menu component because our links need dropdown menus (they have sublinks)
    require_once 'dropdown_menu.php';

    # The default value of $activeIndex is 0, it means the homepage is active or another unknown page is active.
    # Other values:
    #   - $activeIndex is 1 = Documents is active
    #   - $activeIndex is 2 = Statistics is active
    #   - $activeIndex is 6 = Contact is active
    #   - $activeIndex is 7 = About is active
    # If you don't set a value for $activeIndex, it will always default to 0, otherwise it will use the value you put
    function navbar(int $activeIndex = 0): string {
        # We define each link whether they are active or not, based on the criteria above
        $documents_classList = $activeIndex == 1? "navbar-link active-link" : "navbar-link";
        $statistics_classList = $activeIndex == 2? "navbar-link active-link" : "navbar-link";
        $request_classList = $activeIndex == 3? "navbar-link active-link" : "navbar-link";
        $contact_classList = $activeIndex == 6? "navbar-link active-link" : "navbar-link";
        $about_classList = $activeIndex == 7? "navbar-link active-link" : "navbar-link";

        # We build the dropdown menus for each of the links in advance
        $documents_dropdownMenu = dropdownMenu("document-dropdown-menu", [
            "Latest Releases" => "#",
            "Browse Archive" => "#",
            "&emsp;Categories" => "#",
            "&emsp;All Documents" => "#"
        ]);
        $statistics_dropdownMenu = dropdownMenu("statistics-dropdown-menu", [
            "General" => "#",
            "For Editors" => "#",
            "For Admins" => "#"
        ]);
        $request_dropdownMenu = dropdownMenu("request-dropdown-menu", [
            "Request to Archive" => "/yanodash-repository/request/archive.php",
            "Track your Request" => "/yanodash-repository/request/track.php",
            "Request Overview" => "/yanodash-repository/request/overview.php"
        ]);
        $about_dropdownMenu = dropdownMenu("about-dropdown-menu", [
            "What is the OSC?" => "#",
            "Meet the Executives" => "#",
            "YanoDASH's Story" => "#"
        ]);
        $myaccount_dropdownMenu = dropdownMenu("myaccount-dropdown-menu", [
            "Login" => "/yanodash-repository/login",
            "Request an Account" => "/yanodash-repository/request-account"
        ]);

        $html = <<< HTML
            <!-- Temporary style, this can be moved to initial CSS file and replaced. -->
            <style>
                #navbar {
                    padding: 8px;
                }

                #navbar a, #navbar a:visited {
                    color: black;
                    text-decoration: none;
                }

                .active-link:link, .active-link:visited {
                    color: red;
                    font-style: italic;
                }

                .dropdown {
                    position: relative;
                    border-radius: 32px;
                    transition: background-color 0.3s ease;
                    padding: 8px 12px;
                    cursor: pointer;
                }

                .dropdown:hover {
                    background: rgba(255, 52, 52, 0.15);
                }

                .dropdown h3 {
                    margin: 0;
                }

                .dropdown-contents {
                    position: absolute;
                    top: 100%;
                    left: 0;

                    background: #f0f0f0;
                    min-width: 164px;
                    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
                    border-radius: 8px;
                    padding: 8px 0;
                    z-index: 1000;

                    clip-path: inset(0 0 100% 0);
                    opacity: 0;

                    transition: 
                        clip-path 0.35s ease,
                        opacity 0.25s ease;

                    pointer-events: none;
                }

                .dropdown-contents a {
                    display: block;
                    padding: 8px 12px;
                    text-decoration: none;
                    color: black;
                }

                .dropdown-contents a:hover {
                    background: rgba(255, 52, 52, 0.15);
                }

                .dropdown:hover .dropdown-contents {
                    clip-path: inset(0 0 0 0);
                    opacity: 1;
                    pointer-events: auto;
                }

                #myaccount-dropdown-menu {
                    position: absolute !important;
                    right: 0 !important;
                    transform: translateX(-75%);
                }

                #yanodash-home
                {
                    cursor: pointer;
                }

                #yanodash-home
                {
                    color: #71100F;
                    transition: color 0.4s ease;
                    margin-left: 8px;
                    margin-right: 8px;
                }

                #yanodash-home:hover
                {
                    text-decoration: none;
                    color: red;
                    transition: color 0.3s ease;
                }

                #yanodash-home:hover .ulp
                {
                }

                #yanodash-home:hover .ulp::after
                {
                    width: 100%;
                }

                .ulp { position: relative; }

                .ulp::after
                {
                    content: "";
                    position: absolute;
                    left: 0;
                    bottom: -2px;
                    width: 0;
                    height: 3px;
                    background: red;
                    transition: width 0.3s ease;
                }
            </style>
            <div id="navbar" style="display: flex; align-items: center;">
                <a href="/yanodash-repository/">
                    <img src="/yanodash-repository/images/navbar-logo.png" draggable="false" style="width: 90px;">
                </a>
                <a href="/yanodash-repository" id="yanodash-home"><h1>Yano<span class="ulp" href="#">DASH</h1></span></a>
                <span id="vertical-bar" style="width: 2px; height: 32px; background-color: #71100F; margin-left: 8px; margin-right: 8px;"></span>
                <div class="dropdown" id="documents-dropdown">
                    <a class="$documents_classList" href="/yanodash-repository/documents/">
                        <h3>Documents</h3>
                    </a>
                    $documents_dropdownMenu
                </div>
                <div class="dropdown" id="stats-dropdown">
                    <a class="$statistics_classList" href="#">
                        <h3>Statistics</h3>
                    </a>
                    $statistics_dropdownMenu
                </div>
                <div class="dropdown" id="request-dropdown">
                    <a class="$request_classList" href="/yanodash-repository/request/request.php">
                        <h3>Request</h3>
                    </a>
                    $request_dropdownMenu
                </div>
                <div class="dropdown">
                    <a class="$contact_classList" href="#">
                        <h3>Contact</h3>
                    </a>
                </div>
                <div class="dropdown" id="about-dropdown">
                    <a class="$about_classList" href="#">
                        <h3>About</h3>
                    </a>
                    $about_dropdownMenu
                </div>

                <div class="dropdown" style="margin-left: auto; margin-right: 24px;">
                    <a style="cursor: pointer;">
                        <img src="/yanodash-repository/images/ui-indicators/account.png" draggable="false" style="width: 40px;">
                    </a>
                    $myaccount_dropdownMenu
                </div>
                
            </div>
        HTML;
        return $html;
    }
?>