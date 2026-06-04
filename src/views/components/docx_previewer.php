<?php
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;

function docx_previewer(string $filePath, string $width = "75vw", string $height = "600px"): string 
{
    if (!file_exists($filePath)) {
        return "<div style='color:red; font-weight:bold;'>Error: File not found at " . htmlspecialchars($filePath) . "</div>";
    }

    try {
        $phpWord = IOFactory::load($filePath);
        $writer = IOFactory::createWriter($phpWord, 'HTML');

        $twipToMm = 0.0176389;

        $sections = $phpWord->getSections();
        $primarySection = !empty($sections) ? current($sections) : null;

        $pageWidthMm  = 210;
        $pageHeightMm = 297;
        $marginTopMm    = 20;
        $marginBottomMm = 20;
        $marginLeftMm   = 20;
        $marginRightMm  = 20;

        if ($primarySection && method_exists($primarySection, 'getStyle')) {
            $sectionStyle = $primarySection->getStyle();
            
            if ($sectionStyle) {
                $pageWidthMm    = $sectionStyle->getPageSizeW() * $twipToMm;
                $pageHeightMm   = $sectionStyle->getPageSizeH() * $twipToMm;
                $marginTopMm    = $sectionStyle->getMarginTop() * $twipToMm;
                $marginBottomMm = $sectionStyle->getMarginBottom() * $twipToMm;
                $marginLeftMm   = $sectionStyle->getMarginLeft() * $twipToMm;
                $marginRightMm  = $sectionStyle->getMarginRight() * $twipToMm;
            }
        }

        $totalVerticalPaddingMm = $marginTopMm + $marginBottomMm;
    } catch (\Exception $e) {
        return "<div style='color:red; font-weight:bold;'>Error processing document: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #e5e5e5;
            height: 100vh;
            display: grid;
            grid-template-rows: 56px 1fr;
            overflow: hidden;
        }
        .toolbar {
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            z-index: 100;
        }
        .zoom-controls {
            display: inline-flex;
            align-items: center;
            background: #f1f3f4;
            border-radius: 32px;
            padding: 4px;
            border: 1px solid #dadce0;
        }
        .zoom-btn {
            background: transparent; border: none; color: #3c4043;
            font-size: 16px; font-weight: bold; width: 32px; height: 32px;
            cursor: pointer; border-radius: 32px; display: flex;
            align-items: center; justify-content: center;
            transition: background 0.15s ease;
        }
        .zoom-btn:hover { background: #e8eaed; }
        .zoom-btn:active { background: #dadce0; }
        #zoomLevel { font-family: 'RobotoFlex', sans-serif; font-size: 14px; font-weight: 500; color: #3c4043; min-width: 60px; text-align: center; user-select: none; }
        .viewer { width: 100%; height: 100%; overflow: auto; padding: 30px 0; display: flex; justify-content: center; align-items: flex-start; scrollbar-width: thin; }
        .scale-wrapper { transform-origin: top center; display: block; width: max-content; height: max-content; }
        
        .page { 
            width: <?php echo $pageWidthMm; ?>mm; 
            min-height: <?php echo $pageHeightMm; ?>mm; 
            padding: <?php echo "{$marginTopMm}mm {$marginRightMm}mm {$marginBottomMm}mm {$marginLeftMm}mm"; ?>;
            background: white; 
            margin: 0 auto 30px auto; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.15); 
            overflow: hidden; 
        }
        
        .page:last-child { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; }
    </style>
    </head>
    <body>

    <header class="toolbar">
        <div class="zoom-controls">
            <button class="zoom-btn" onclick="zoomOut()" title="Zoom Out">−</button>
            <span id="zoomLevel">100%</span>
            <button class="zoom-btn" onclick="zoomIn()" title="Zoom In">+</button>
        </div>
    </header>

    <main class="viewer">
        <div class="scale-wrapper" id="scaleWrapper">
            <div id="contentHost">
                <?php $writer->save("php://output"); ?>
            </div>
        </div>
    </main>

    <script>
    let zoom = 1;
    const wrapper = document.getElementById('scaleWrapper');
    const viewer = document.querySelector('.viewer');

    const PAGE_HEIGHT_MM = <?php echo $pageHeightMm; ?>;
    const PADDING_VERTICAL_MM = <?php echo $totalVerticalPaddingMm; ?>;

    function applyZoom() {
        if (!wrapper || !viewer) return;
        wrapper.style.transform = `scale(${zoom})`;
        wrapper.dataset.zoom = zoom;
        document.getElementById('zoomLevel').innerText = Math.round(zoom * 100) + '%';
    }

    function zoomIn() { zoom = Math.min(zoom + 0.1, 2); applyZoom(); }
    function zoomOut() { zoom = Math.max(zoom - 0.1, 0.5); applyZoom(); }

    viewer.addEventListener('wheel', function(e) {
        if (e.ctrlKey) {
            e.preventDefault(); 
            if (e.deltaY > 0) { zoomOut(); } else { zoomIn(); }
        }
    }, { passive: false });

    function pageHeightPx() {
        const mmToPx = 3.78;
        return (PAGE_HEIGHT_MM + PADDING_VERTICAL_MM) * mmToPx;
    }

    function getHeight(el) {
        const style = window.getComputedStyle(el);
        return el.getBoundingClientRect().height + parseFloat(style.marginTop) + parseFloat(style.marginBottom);
    }

    function createPage() {
        const page = document.createElement("div");
        page.className = "page";
        return page;
    }

    function splitIntoPages() {
        const content = document.getElementById("contentHost");
        if (!content) return;

        const nodes = Array.from(content.children || []);
        let pages = [];
        let currentPage = createPage();
        let currentHeight = 0;

        nodes.forEach(node => {
            const nodeHeight = getHeight(node);
            if (currentHeight + nodeHeight > pageHeightPx()) {
                pages.push(currentPage);
                currentPage = createPage();
                currentHeight = 0;
            }
            currentPage.appendChild(node);
            currentHeight += nodeHeight;
        });

        pages.push(currentPage);
        const contentHost = document.getElementById("contentHost");
        contentHost.innerHTML = "";
        pages.forEach(p => contentHost.appendChild(p));
        applyZoom();
    }

    window.onload = async () => {
        await document.fonts.ready;
        requestAnimationFrame(() => { requestAnimationFrame(splitIntoPages); });
    };
    </script>
    </body>
    </html>
    <?php
    $htmlContent = ob_get_clean();

    $encodedHtml = base64_encode($htmlContent);
    $iframeSrc = "data:text/html;base64," . $encodedHtml;

    return sprintf(
        '<iframe src="%s" style="width: %s; height: %s; border: 2px solid #ccc; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);" allowfullscreen></iframe>',
        $iframeSrc,
        htmlspecialchars($width),
        htmlspecialchars($height)
    );
}