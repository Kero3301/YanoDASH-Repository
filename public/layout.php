<?php
    require_once '../bootstrap/app.php';
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
                --yd-title-nav-lmarg: 2px;
                --yd-title-nav-fontsize: 2.6rem;
                --maroon: #63071e;
                --richred: #9f1f3f;
                --gold: #F8BB38;
                --gold-translucent: #f8bb38b9;
                --roundborder-top: 6px solid var(--maroon);
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
                padding-inline: 16px;
                cursor: pointer;
                border-radius: 16px;
                transition: color 0.3s ease;
            }

            #yd-logo-nav-wrap:hover + #yd-title-nav,
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
                transition: opacity 0.5s ease, transform 0.5s ease;
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
                gap: 8px;
                align-items: center;
                margin-left: auto;
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
            }

            #lsb-btn:hover {
                transform: translateX(4px);
                background: var(--gold-translucent);
                border-color: #c68d12;
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
                width: 4px;
                background: rgba(0,0,0,0.11);
                margin-left: 14px;
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

            /* Mobile */
            @media(max-width: 767px) {
                :root {
                    --nav-height: 100px;
                    --nav-border-radius: 40px;
                    --osc-logo-nav-wh: 80px;
                    --yd-logo-nav-wh: 60px;
                    --yd-title-nav-fontsize: 2rem;
                    --yd-title-nav-lmarg: 0;
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
        <button id="lsb-btn" onclick="toggleLsb()">
        >
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
                <div id="navigation-links">
                    <p>1</p>
                    <p>2</p>
                </div>
            </nav>
        </div>

        <!-- Modal-like sidebar backdrop -->
        <div id="sidebar-backdrop" onclick="setLsbOpen(false)"></div>

        <!-- Main div -->
        <main id="main">
            <div id="left-sidebar-container">
                <div id="left-sidebar">
                </div>
            </div>
            <div class="page-contents no-padding" style="border-radius: 10px; grid-column: 2; width: 100%; height: 1000px; border: none; border-top: var(--roundborder-top);">
                <div class="pch" style="border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(to bottom, var(--maroon), var(--richred)">
                    <h1>New Layout</h1>
                </div>
            </div>
        </main>
        <script src="script/sidebar-controls.js">
        </script>
    </body>
</html>