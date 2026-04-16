<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initializePage("Test | YanoDASH")?>
        <style>
            .test2 {
                padding: 8px 12px;
                cursor: pointer;
                background: lightgray;
                border-radius: 16px;
                display: inline-block;
            }

            .test1 {
                position: relative;
                display: inline-block;
            }

            .dd {
                position: absolute;
                top: 100%;
                left: 0;

                display: flex;
                flex-direction: column;

                background: #eee;
                min-width: 120px;

                opacity: 0;
                visibility: hidden;
                transition: 0.15s ease;

                z-index: 1000;
                border-radius: 8px;
                padding: 8px 0;
            }

            .dd a {
                padding: 8px 12px;
            }

            .dd a:hover {
                background-color: skyblue;
            }

            .test1:hover .dd {
                opacity: 1;
                visibility: visible;
            }
        </style>
    </head>
    <body>
        <?php echo navbar()?>
        <h1>abc</h1>
        <div>
            <div class="test1">
                <a class="test2">ABC</a>
                <div class="dd">
                    <a href="#">A</a>
                    <a href="#">B</a>
                    <a href="#">C</a>
                </div>
            </div>
        </div>
    </body>
</html>