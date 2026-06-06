<?php
    require_once '../bootstrap/app.php';
    load('footer');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page('hello world 2');?>
        <style>
            :root {
                --header-height: 100px;
            }

            #sidebar-icon {
                transform: rotate(0deg);
                transform-origin: center;
                transition: transform 0.25s ease;
            }

            #root.sidebar-collapsed #sidebar-icon {
                transform: rotate(180deg);
            }


            #root.sidebar-open #sidebar-icon {
                transform: rotate(180deg);
            }

            * { box-sizing: border-box; }

                    

            #root {
                width: 100%;
                min-height: 100vh;
                display: grid;
                grid-template-rows: auto 1fr;
                background: white;
            }

            #top-root {
                background: transparent;
                height: 100px;
                position: sticky;
                top: 0;
                overflow: visible;
                display: grid;
                grid-template-rows: 1fr 1fr;
            }

            #navigation {
                position: sticky;
                top: 0;
                z-index: 999;
                background: purple;
                border: 2px solid #550055;
                height: var(--header-height);
                border-radius: 40px;
                display: flex;
                align-items: center;
                padding-inline: 32px;
                color: white;
                box-shadow: 0 2px 12px rgba(0,0,0,0.3);
            }

            #bottom-root {
                padding: 16px 8px 8px 8px;
                background: white;
                height: 100%;
                width: 100%;

                display: grid;
                grid-template-columns: 260px 1fr;
                gap: 8px;
                align-items: start;

                transition: grid-template-columns 0.25s ease;
            }

            #main {
                min-width: 0;
                width: auto;
                background: white;
                border-radius: 10px;
                border: 2px solid #ddd;
                overflow: hidden;
                
                display: flex;
                flex-direction: column;
                flex-shrink: 0;
            }

            #top-root-p1 {
                background: white;
                height: 50%;
                padding: 8px;
            }

            #footer {
                margin-top: auto;
            }

            #main-contents {
                padding: 24px 48px;
            }

            #left-sidebar {
                position: sticky;
                top: 120px;
                align-self: start;

                padding: 8px;
                border-radius: 8px;
                background: white;
                border: 2px solid #ddd;
                height: 600px;
                overflow: hidden;
                transition: opacity 0.2s ease, transform 0.2s ease, width 0.25s ease;
            }

            #sidebar-toggle {
                position: fixed;
                top: var(--header-height);
                left: 2px;
                z-index: 2000;

                width: 44px;
                height: 44px;

                border-radius: 32px;
                border: 1px solid #ccc;
                background: white;

                cursor: pointer;
                box-shadow: 0 6px 18px rgba(0,0,0,0.15);
                transition: top 0.3s ease;
            }

            #sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
                z-index: 1500;
                height: 100vh;
            }

            /* Desktop sidebar states */
            @media (min-width: 769px) {
                #root.sidebar-collapsed #bottom-root {
                    grid-template-columns: 0 1fr;
                    gap: 0;
                }

                #root.sidebar-collapsed #left-sidebar {
                    opacity: 0;
                    transform: translateX(-20px);
                    pointer-events: none;
                }
            }

            /* Mobile Query */
            @media (max-width: 768px) {
                #sidebar-icon {
                    transform: rotate(180deg);
                }

                #root.sidebar-open #sidebar-icon {
                    transform: rotate(0deg);
                }

                #sidebar-toggle {
                    top: 2px;
                }

                #bottom-root {
                    grid-template-columns: 1fr !important;
                }

                #left-sidebar {
                    position: fixed;
                    top: 0;
                    left: 0;
                    bottom: 0;

                    width: 220px;
                    height: 100%;

                    transform: translateX(-100%);
                    transition: transform 0.25s ease;
                    z-index: 1600;

                    overflow-y: auto;

                    border-left: none;
                    border-top-left-radius: 0;
                    border-bottom-left-radius: 0;
                }

                #root.sidebar-open #left-sidebar {
                    transform: translateX(0);
                }

                #sidebar-backdrop {
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity 0.2s ease;
                }

                #root.sidebar-open ~ #sidebar-backdrop {
                    opacity: 1;
                    pointer-events: auto;
                }
            }
        </style>
    </head>
    <body>
        <div id="root">
            <button id="sidebar-toggle" aria-label="Toggle sidebar">
            <svg id="sidebar-icon" viewBox="0 0 24 24" width="20" height="20">
                <path d="M14 6 L8 12 L14 18"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"/>
            </svg>
            </button>

           <div id="top-root">
                <div id="top-root-p1" style="overflow: visible">
                    <nav id="navigation">
                        <h1>YanoDASH</h1>
                    </nav>
                </div>
            </div>
            <div id="bottom-root">
                <aside id="left-sidebar"></aside>
                <main id="main">
                    <!-- Page content header -->
                    <div class="pch">
                        <h1>abc</h1>
                    </div>

                    <!-- Page contents -->
                    <div id="main-contents">
                        <?php
                            for ($i = 1; $i <= 100; $i++) echo <<< HTML
                                <p>$i</p>
                            HTML;
                        ?>
                    </div>

                    <!-- Page content footer -->
                    <?php echo footer() ?>
                </main>
            </div>
        </div>

        <div id="sidebar-backdrop"></div>

        <!-- <button id="sidebar-toggle">☰</button> -->

        


        <!-- <script>
            const root = document.getElementById('root');
            const btn = document.getElementById('sidebar-toggle');

            btn.addEventListener('click', () => {
                root.classList.toggle('sidebar-collapsed');
            });
        </script> -->

        <script>
            const root = document.getElementById('root');
            const btn = document.getElementById('sidebar-toggle');
            const backdrop = document.getElementById('sidebar-backdrop');

function toggleSidebar() {
    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
        root.classList.remove('sidebar-collapsed'); // IMPORTANT
        root.classList.toggle('sidebar-open');
    } else {
        root.classList.remove('sidebar-open'); // IMPORTANT
        root.classList.toggle('sidebar-collapsed');
    }
}

            btn.addEventListener('click', toggleSidebar);
            backdrop.addEventListener('click', toggleSidebar);

            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    root.classList.remove('sidebar-open');
                }
            });
        </script>
    </body>
</html>
