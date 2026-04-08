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
        $contact_classList = $activeIndex == 6? "navbar-link active-link" : "navbar-link";
        $about_classList = $activeIndex == 7? "navbar-link active-link" : "navbar-link";

        # We build the dropdown menus for each of the links in advance
        $documents_dropdownMenu = dropdownMenu("document-dropdown-menu", [
            "Latest Releases" => "/yanodash-repository/documents/public-archive/latest",
            "Browse Archive" => "/yanodash-repository/documents/public-archive",
            "&emsp;Categories" => "/yanodash-repository/documents/public-archive/categories",
            "&emsp;All Documents" => "/yanodash-repository/documents/public-archive/all"
        ]);
        $statistics_dropdownMenu = dropdownMenu("statistics-dropdown-menu", [
            "General" => "/yanodash-repository/stats/general",
            "For Editors" => "/yanodash-repository/stats/editing",
            "For Admins" => "/yanodash-repository/stats/admin"
        ]);
        $about_dropdownMenu = dropdownMenu("about-dropdown-menu", [
            "What is the OSC?" => "/yanodash-repository/about/osc",
            "Meet the Executives" => "/yanodash-repository/about/execs",
            "YanoDASH's Story" => "/yanodash-repository/about/yanodash-story"
        ]);
        $myaccount_dropdownMenu = dropdownMenu("myaccount-dropdown-menu", [
            "Login" => "/yanodash-repository/login",
            "Request an Account" => "/yanodash-repository/request-account"
        ]);

        $html = <<< HTML
            <!-- Temporary style, this can be moved to initial CSS file and replaced. -->
            <style>
                #navbar a {
                    text-decoration: none;
                }

                .active-link:link, .active-link:visited {
                    color: red;
                    font-style: italic;
                }

                .dropdown {
                    position: relative;
                }

                .dropdown-contents {
                    display: none;
                    position: absolute;
                    top: 100%;
                    left: 0;
                    transform: translateX(-25%);
                    border-radius: 8px;

                    background: #eee;
                    min-width: 164px;
                    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
                    padding: 8px 0;
                    z-index: 1000;
                }

                .dropdown-contents a {
                    display: block;
                    padding: 8px 12px;
                    text-decoration: none;
                    color: black;
                }

                .dropdown-contents a:hover {
                    background: #6480DB;
                }

                .dropdown:hover .dropdown-contents {
                    display: block;
                }

                #myaccount-dropdown-menu {
                    position: absolute !important;
                    right: 0 !important;
                    transform: translateX(-75%);
                }
            </style>
            <div id="navbar" style="display: flex; gap: 16px; align-items: center;">
                <a href="/yanodash-repository/">
                    <img src="/yanodash-repository/images/navbar-logo.png" draggable="false" style="width: 90px;">
                </a>
                <a href="/yanodash-repository"><h1 style="user-select: none;">YanoDASH</h1></a>
                <span id="vertical-bar" style="width: 2px; height: 32px; background-color: #71100F"></span>
                <div class="dropdown" id="documents-dropdown">
                    <a class="$documents_classList" href="/yanodash-repository/documents/">
                        <h3>Documents</h3>
                    </a>
                    $documents_dropdownMenu
                </div>
                <div class="dropdown" id="stats-dropdown">
                    <a class="$statistics_classList" href="/yanodash-repository/stats/">
                        <h3>Statistics</h3>
                    </a>
                    $statistics_dropdownMenu
                </div>
                <h3><a class="$contact_classList" href="/yanodash-repository/contact/">Contact</a></h3>
                <div class="dropdown" id="about-dropdown">
                    <a class="$about_classList" href="/yanodash-repository/about/">
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