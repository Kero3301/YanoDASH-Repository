<?php
    require_once '../bootstrap/app.php';
    load('svg_templates', 'footer', 'accordion', 'sliding_switch');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page('layout') ?>
        <style>
            :root {
                --nav-height: 124px;
                --nav-border-radius: 48px;
                --main-margin-il: 8px;
                --circle-btn-wh: 40px;
                --osc-logo-nav-wh: 100px;
                --yd-logo-nav-wh: 80px;
                --yd-title-nav-inpad: 16px;
                --yd-title-nav-lmarg: 2px;
                --yd-title-nav-fontsize: 2.6rem;
                --yd-title-nav-border-radius: 16px;
                --maroon: #63071e;
                --richred: #9f1f3f;
                --gold: #F8BB38;
                --gold-translucent: #ffc74db9;
                --roundborder-top: 6px solid var(--maroon);
                --vertical-bar-nav-lmarg: 14px;
            }

            body::before {
                background: linear-gradient(to bottom, #E5E7E9, #E5E7E9, #A0A0A0);
            }

            #yd-title-nav {
                margin-left: var(--yd-title-nav-lmarg);
                position: relative;
                overflow: hidden;
                z-index: 0;
                font-size: var(--yd-title-nav-fontsize);
                color: #63071e;
                padding-inline: var(--yd-title-nav-inpad);
                cursor: pointer;
                border-radius: var(--yd-title-nav-border-radius);
                transition: color 0.3s ease;
            }

            #yd-logo-nav-wrap:hover:has(~#yd-title-nav),
            #yd-title-nav:hover {
                color: #BB0000;
            }

            #yd-title-nav::before {
                z-index: -1;
                content: "";
                position: absolute;
                inset: 0;
                background: linear-gradient(to right, #ff9d9d83, #fff4f46f);
                opacity: 0;
                transition: opacity 0.4s ease, transform 0.4s ease;
                z-index: -1;

                transform: translateX(-100%);
            }

            #yd-logo-nav-wrap:hover + #yd-title-nav::before,
            #yd-title-nav:hover::before {
                opacity: 1;
                transform: translateX(0%);
            }

            #nav-dock {
                position: sticky;
                top: 0;
                z-index: 999;

                margin-bottom: calc(var(--nav-height) * 0.66);

                background: #E5E7E9;
                overflow: visible;
                height: calc(var(--nav-height) / 2);
            }

            #navigation-links {
                display: flex;
                gap: 4px;
                align-items: center;
                /* margin-left: auto; */
                margin-right: 16px;
            }

            #navigation-links .inline-link {
                padding-inline: 12px;
                border-radius: 8px;
                font-size: 1.05rem;
            }

            #nav {
                margin-inline: var(--main-margin-il);
                display: flex;
                border-radius: var(--nav-border-radius);
                height: var(--nav-height);
                background: linear-gradient(to right, white, #f3f3f3);
                border: 1px solid var(--richred);
                position: relative;
                top: 8px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                padding-inline: 8px;
            }

            #nav-home {
                display: flex;
                align-items: center;
            }

            #main {
                margin-inline: var(--main-margin-il);
                display: grid;
                grid-template-columns: 0 1fr 0;
                gap: 0;
                transition: grid-template-columns 0.3s ease;
            }

            /* Left sidebar open */
            #main.lsb-open {
                grid-template-columns: 250px 1fr 0;
            }

            /* Right sidebar open */
            #main.rsb-open {
                grid-template-columns: 0 1fr 200px;
                gap: 8px;
            }

            /* Both sidebars open */
            #main.lsb-open.rsb-open {
                grid-template-columns: 250px 1fr 200px;
                gap: 8px;
            }

            #lsb-btn {
                z-index: 99999;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);

                width: var(--circle-btn-wh);
                height: var(--circle-btn-wh);

                position: fixed;
                top: 50%;
                left: calc(var(--main-margin-il) / 2);
                border-radius: var(--circle-btn-wh);
                border: 1px solid lightgray;
                background: rgba(255,255,255,0.33);

                backdrop-filter: blur(10px);
                transition: transform 0.3s ease, background 0.3s ease;

                display: flex;
                justify-content: center; 
                align-items: center;
            }

            #lsb-btn:hover {
                transform: translateX(4px);
                background: var(--gold-translucent);
                border-color: #c68d12;
            }

            #lsb-btn #lsb-toggle-icon {
                transform: rotate(180deg);
                transition: transform 0.3s ease;
            }

            #lsb-btn:has(~#main.lsb-open) #lsb-toggle-icon {
                transform: rotate(0deg);
            }

            #lsb-btn:has(~#main.lsb-open):hover {
                transform: translateX(-4px);
            }

            #left-sidebar-container {
                position: relative;
                top: 0;
            }

            #main.lsb-open #left-sidebar-container {
                padding-right: 8px;
            }

            #left-sidebar {
                position: sticky;
                top: calc(var(--nav-height) + 16px);
                height: 500px;
                background: white;
                position: sticky;
                border-radius: 10px;
                border: none;
                border-top: var(--roundborder-top);
            }

            .sidebar-contents {
                /* background: yellow; */
                display: grid;
                grid-template-rows: 
                    auto /* Section title */ 
                    1fr; /* Section */
                height: 100%;
                overflow: hidden;
                padding-inline: 0px;
                opacity: 0;
                transition: opacity 0.3s ease, padding 0.3s ease;
            }

            .sidebar-contents .section-title {
                margin-bottom: 8px;
            }

            .sidebar-item-section {
                margin-bottom: 24px;
                border-radius: 8px;
                overflow: auto;
                background: rgba(0,0,0,0.03);
                display: flex;
                flex-direction: column;
                flex-shrink: 0;
                border: 2px solid #eee;
            }

            .sidebar-item {
                cursor: pointer;
                width: 100%;
                margin-block: 0;
                padding-block: 4px;
                padding-inline: 10px;
                text-overflow: ellipsis;
                font-family: 'RobotoFlex', sans-serif;
                font-size:0.89rem;
                transition: background 0.1s ease, color 0.1s ease, border-color 0.1s ease, transform 0.15s ease, border-bottom-width 0.1s ease;
                border-radius: 10px;
                border: 2px solid transparent;
                user-select: none;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }

            .sidebar-item:hover {
                border: 2px solid var(--richred);
                /*border-bottom-width: 4px;*/
                background: rgba(255,0,0,0.15);
                transform: translateY(1px);
            }

            .sidebar-item:active {
                background: rgba(255,0,0,0.3);
            }

            #main.lsb-open .sidebar-contents {
                opacity: 1;
                padding-inline: 24px;
            }

            ul, ol { list-style-position: inside; }

            #sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                backdrop-filter: blur(2px);

                opacity: 0;
                visibility: hidden;
                transition: opacity 0.25s ease, visibility 0s linear 0.25s;

                z-index: 1100;

                pointer-events: none;
            }

            #vertical-bar-nav {
                height: 28px;
                width: 3px;
                background: rgba(0,0,0,0.11);
                margin-left: var(--vertical-bar-nav-lmarg);
                border-radius: 2px;
            }

            #osc-logo-nav {
                width: var(--osc-logo-nav-wh);
                height: var(--osc-logo-nav-wh);
            }

            #yd-logo-nav {
                width: var(--yd-logo-nav-wh);
                height: var(--yd-logo-nav-wh);
            }

            #footer {
                margin-top: auto;
            }

            #nav-right {
                margin-left: auto;
                margin-right: 16px;
                padding-right: 12px;
                display: flex;
                align-items: center;
                overflow: hidden;
            }

            .section-title {
                font-weight: 700;
                color:var(--maroon);
                margin-bottom: 12px;
                border-bottom: 2px solid rgba(128,0,0,.2);
                padding-bottom: 6px;
                text-transform: uppercase;
                font-size: .95rem;
                font-family: 'RobotoFlex', serif !important;
            }

            .section-gap {
                width: 100%;
                height: 16px;
                background: transparent;
                border: none;
            }

            .panel-title {
                font-family: 'Gupter', serif;
                font-weight: bold;
                color: var(--maroon);
                font-size: 20px;
                text-align: center;
            }

            .table-container {
                width: 90%;
                margin: 0 auto 30px;
                overflow-x: auto;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                font-family: 'RobotoFlex', sans-serif;
            }

            .table-container table {
                width: 100%;
                border-collapse: collapse;
            }

            .table-container table thead {
                background: linear-gradient(to right, var(--maroon), var(--richred));
            }

            th {
                /*background-color: var(--maroon);*/
                color: white;
                padding: 15px;
                text-align: center;
                font-weight: 600;
            }

            td {
                padding: 15px;
                border-bottom: 1px solid #eee;
                text-align: center;
            }

            td button {
                display: inline-block;
                margin-right: 5px;
                margin-bottom: 5px;
                padding: 5px 12px;
                font-size: 0.8rem;
                border-radius: 20px;
                cursor: pointer;
                border: none;
                transition: 0.2s ease;
            }

            /* Mobile */
            @media(max-width: 767px) {
                :root {
                    --nav-height: 100px;
                    --nav-border-radius: 40px;
                    --osc-logo-nav-wh: 80px;
                    --yd-logo-nav-wh: 60px;
                    --yd-title-nav-inpad: 10px;
                    --yd-title-nav-fontsize: 2rem;
                    --yd-title-nav-lmarg: 0;
                    --vertical-bar-nav-lmarg: 5px;
                    --yd-title-nav-border-radius: 12px;
                }

                #main {
                    display: block;
                }

                #left-sidebar-container {
                    position: fixed;

                    top: 0;
                    left: 0;

                    width: min(300px, 85vw);
                    height: 100dvh;

                    z-index: 1200;

                    transform: translateX(-100%);
                    transition: transform 0.3s ease;

                }

                #left-sidebar {
                    position: static;

                    width: 100%;
                    height: 100%;

                    border-radius: 0 8px 8px 0;
                    background: white;

                    overflow-y: auto;

                    box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
                }

                #main.lsb-open #left-sidebar-container {
                    transform: translateX(0);
                    padding-right: 0;
                }

                #main.lsb-open ~ #sidebar-backdrop,
                body:has(#main.lsb-open) #sidebar-backdrop {
                    opacity: 1;
                    visibility: visible;
                    transition: opacity 0.25s ease, visibility 0s linear 0s;
                    pointer-events: auto;
                }
            }
        </style>
    </head>
    <body>
        <!-- Left Sidebar button -->
        <button id="lsb-btn" onclick="toggleLsb()" title="Show or hide sidebar">
            <?= svg('chevron', id: 'lsb-toggle-icon') ?>
        </button>
        <div id="nav-dock">
            <nav id="nav">
                <div id="nav-home">
                    <img draggable="false" src="images/osc-logo-temporary.png" id="osc-logo-nav">
                    <a id="yd-logo-nav-wrap" href='#'>
                        <img draggable="false" src="images/navbar-logo.png" id="yd-logo-nav">
                    </a>
                    <span id="vertical-bar-nav"></span>
                    <h1 id="yd-title-nav">
                        YanoDASH
                    </h1>
                </div>
                <div id="nav-right">
                    <div id="navigation-links">
                        <a class="inline-link" href="#"><p>Archive</p></a>
                        <a class="inline-link" href="#"><p>DMS</p></a>
                        <a class="inline-link" href="#"><p>Statistics</p></a>
                    </div>

                    <div id="account-actions-nav">
                        <button class="btn moveright latent" style="padding-inline: 16px; font-weight: normal;">Login</button>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Modal-like sidebar backdrop -->
        <div id="sidebar-backdrop" onclick="setLsbOpen(false)"></div>

        <!-- Main div -->
        <main id="main">
            <div id="left-sidebar-container">
                <div id="left-sidebar">
                    <div class="sidebar-contents">
                        <p class="section-title">Sidebar</p>
                        <!-- <p>a</p> -->
                        <div class="sidebar-item-section">
                            <?php
                                $letters = "abcdefghijklmnopqrstuvwxyz";
                                for ($i=0; $i<strlen($letters); $i++) {
                                    $letter = $letters[$i];

                                    echo <<< HTML
                                        <a class="sidebar-item">$letter</a>
                                    HTML;
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-contents no-padding" style="border-radius: 10px; grid-column: 2; width: 100%; border: none;">
                <div class="pch" style="border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(to bottom, var(--maroon), var(--richred)">
                    <h1>New Layout</h1>
                </div>
                <div style="padding-inline: 48px; padding-block: 20px;">
                    <p class="section-title">
                        Section 1
                    </p>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.<br> Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.<br><br> Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </p>

                    <hr class="section-gap">

                    <p class="section-title">
                        Section 2
                    </p>
                    <p>The quick brown fox jumps over the lazy dog. 0123456789</p>

                    <div class="table-container">
                        <table>
                            <thead>
                                <th>A</th>
                                <th>B</th>
                                <th>C</th>
                            </thead>
                            <tbody>
                                <td>ab</td>
                                <td>cd</td>
                                <td>ef</td>
                            </tbody>
                        </table>
                    </div>

                    <p class="section-title">
                        Section 3
                    </p>
                    List
                    <ul>
                        <li>a</li>
                        <li>b</li>
                        <li>c</li>
                    </ul>
                    Enumeration
                    <ol>
                        <li>d</li>
                        <li>e</li>
                        <li>f</li>
                    </ol>
                    <hr class="section-gap">

                    <p class="section-title">
                        Section 4
                    </p>
                    Buttons<br>
                    <button class="btn moveleft">Button 1</button>
                    <button class="btn action">Button 2</button>
                    <button class="btn latent movedown">Button 3</button>
                    <button class="btn action latent moveright">Button 4</button> <br>
                    Inputs
                    <input type="text">
                    <input type="text" placeholder="abc">
                    <input type="number">
                    Selection
                    <select class="sct">
                        <option>a</option>
                    </select>
                    Switches<br>
                    <?php echo sliding_switch("abc")?><br>
                    Accordion
                    <?php echo accordion("accd", ["abc" => "def", "ghi" => "jkl"]);?>
                </div>
                <?php echo footer()?>
            </div>
        </main>
        <script src="script/sidebar-controls.js">
        </script>
    </body>
</html>