<?php
require_once '../bootstrap/app.php';
load('footer');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page('hello world 2');?>
        <style>
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
                height: 100px;
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
            }

            #main {
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
                padding: 12px;
            }

        </style>
    </head>
    <body>
        <div id="root">
           <div id="top-root">
                <div id="top-root-p1" style="overflow: visible">
                    <nav id="navigation">
                        <h1>YanoDASH</h1>
                    </nav>
                </div>
            </div>
            <div id="bottom-root">
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
    </body>
</html>
