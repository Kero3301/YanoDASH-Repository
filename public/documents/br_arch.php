<?php
    session_start();

    require_once '../../src/loader.php';
    load (
        'vendor_autoload',
        'mongodb_client', 
        'mongodb_collections',
        'doc_ed',
        'doc_query',
        'document_factory',
        'navbar',
        'footer',
        'document_list',
        'document_modal',
        'page_header',
        'pagination_controls'
    );

    $client = mongodb_client();
    $collection_documents = coll('documents', $client);

    $documentsPerPage = 8;
    $totalDocuments = $collection_documents->countDocuments([
        'doc_status' => 'PUBLICIZED'
    ]);
    
    $totalPages = (int) max(1, ceil($totalDocuments / $documentsPerPage));
    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $currentPage = max(1, min($currentPage, $totalPages));
    $skip = ($currentPage - 1) * $documentsPerPage;

    $results = $collection_documents->find(
        ['doc_status' => 'PUBLICIZED'],
        [
            'skip' => $skip,
            'limit' => $documentsPerPage
        ]
    );

    $all_docs = get_all($results);

    $sidebarWidth = isset($_COOKIE['sidebar_width']) ? $_COOKIE['sidebar_width'] : '260px';
    $sidebarState = isset($_COOKIE['sidebar_state']) ? $_COOKIE['sidebar_state'] : 'open';
    $isCollapsedClass = ($sidebarState === 'collapsed') ? ' sidebar-collapsed' : '';
?>
<!DOCTYPE html>
<html>
<head>
    <?php initialize_page("All Documents | YanoDASH")?>
    <link rel="stylesheet" href="../css/pages/docsss.css"/>
    
    <style>
        :root {
            --sidebar-width: <?php echo htmlspecialchars($sidebarWidth); ?>;
        }

        #mainContentScroll::-webkit-scrollbar {
            width: 8px;
        }
        #mainContentScroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.03);
            border-radius: 4px;
        }
        #mainContentScroll::-webkit-scrollbar-thumb {
            background: #B26568;
            border-radius: 4px;
        }
        #mainContentScroll::-webkit-scrollbar-thumb:hover {
            background: #9a082d;
        }
        #mainContentScroll {
            scrollbar-width: thin;
            scrollbar-color: #B26568 rgba(0, 0, 0, 0.03);
        }

        #sidebarToggle {
            position: relative; 
        }
        #sidebarToggle::after {
            content: attr(data-tooltip); 
            position: absolute;
            top: 50%;
            left: calc(100% + 10px); 
            transform: translateY(-50%);
            background-color: #333;
            color: #fff;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-family: sans-serif;
            white-space: nowrap;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease; 
        }
        #sidebarToggle:hover::after {
            opacity: 1;
        }

        .scroll-action-btn {
            opacity: 0.7;
            transition: transform 0.2s ease, opacity 0.2s ease, background-color 0.2s ease;
        }
        .scroll-action-btn:hover {
            opacity: 1;
            transform: scale(1.1);
            background-color: #f7eded;
        }

        .clickable {
            display: block;
            width: 100%;
            box-sizing: border-box;
            transition: background 0.3s ease;
            border-radius: 10px;
            padding: 4px 10px;
            cursor: pointer;
            background: rgba(0,0,0,0.02);
            overflow: hidden;
            font-size: 0.95rem
        }
        .clickable:hover {
            background: rgba(255,0,0,0.15);
        }
        .document {
            color: #9a082d
        }
        .page-header {
            width: 94%;
            border-color: #eee
        }
        .page-contents {
            border: none;
            overflow: hidden;
        }

        .mw {
            display: flex;
            gap: 0px; 
            width: 99%; 
            margin: auto;
            height: calc(100vh - 90px);
            position: relative;
        }

        #sidebarWrap {
            position: relative;
            flex-basis: var(--sidebar-width);
            flex-shrink: 0;
        }

        #sidebarWrap.transition-active {
            transition: flex-basis 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        }

        #sidebar {
            position: absolute;
            inset: 0 0 8% 0; 
            width: 100%;
            overflow-y: auto;
            border-radius: 8px;
            border-top: 5px solid #7f0000;
            padding: 12px;
            background: white;
            box-sizing: border-box;
            white-space: nowrap;
            opacity: 1;
        }
        
        #sidebarWrap.transition-active #sidebar {
            transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.2s ease;
        }

        .sidebar-resizer {
            width: 6px;
            cursor: col-resize;
            background-color: transparent;
            z-index: 10;
            flex-shrink: 0;
            align-self: stretch;
            margin-bottom: 8%; 
            transition: background-color 0.2s;
        }
        .sidebar-resizer:hover, .sidebar-resizer.is-dragging {
            background-color: rgba(178, 101, 104, 0.4);
        }

        #sidebarToggle {
            position: absolute;
            top: 50%;
            left: var(--sidebar-width); 
            transform: translate(-50%, -50%);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: black;
            color: white;
            border: none;
            cursor: pointer;
            z-index: 999;
        }
        
        #sidebarToggle.transition-active {
            transition: left 0.3s cubic-bezier(0.25, 1, 0.5, 1); 
        }

        .mw.sidebar-collapsed #sidebarWrap {
            flex-basis: 0px !important;
        }

        #mainContentScroll {
            border-top: 5px solid #7f0000;
        }

        .mw.sidebar-collapsed #sidebar {
            transform: translateX(-100%);
            opacity: 0;
            pointer-events: none;
        }

        .mw.sidebar-collapsed #sidebarToggle {
            left: 18px !important;
        }
        
        .mw.sidebar-collapsed .sidebar-resizer {
            pointer-events: none;
            opacity: 0;
        }

        .mw.sidebar-collapsed {
            margin-left: 0;
        }

        body.is-resizing {
            cursor: col-resize;
            user-select: none;
            -webkit-user-select: none;
        }

        .sidebar-header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 0 4px;
        }
        .reset-width-btn {
            background: #ddd;
            border: none;
            color: black;
            font-size: 13px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 16px;
            transition: color 0.2s, background-color 0.2s;
            text-decoration: none;
        }
        .reset-width-btn:hover {
            color: #9a082d;
            background-color: rgba(178, 101, 104, 0.1);
        }

        @media (max-width: 768px) {
            .mw {
                flex-direction: column;
                height: auto;
                gap: 20px;
            }
            #sidebarWrap {
                flex-basis: auto !important;
                width: 100%;
            }
            #sidebar {
                position: static;
                height: auto;
                max-height: 300px;
                width: 100%;
            }
            .sidebar-resizer {
                display: none;
            }
            #sidebarToggle {
                display: none; 
            }
            .reset-width-btn {
                display: none;
            }
            #mainContentScroll {
                padding-right: 0px;
                height: auto;
                overflow-y: visible;
            }
            .scroll-action-btn {
                right: 5% !important;
            }
        }
    </style>
</head>
<body>
    <?php echo navbar()?>

    <div class="mw<?php echo $isCollapsedClass; ?>" id="mainWrapper">
        <div id="sidebarWrap">
            <aside id="sidebar">
                <div class="sidebar-header-container">
                    <h2 style="text-decoration: underline 2px red; margin: 0; font-size: 1.5rem;">Sidebar</h2>
                    <button id="resetWidthBtn" class="reset-width-btn" title="Reset Sidebar Width">Reset width</button>
                </div>
                
                <p style="margin: 4px 0;"><span class="clickable">▼ Folder 1</span></p>
                <p style="margin: 4px 0;"><span class="clickable">▲ Folder 2</span></p>
                
                <p style="margin: 4px 0;"><span class="clickable">&emsp;&emsp;▼ Folder 2.1</span></p>
                <p style="margin: 4px 0;"><span class="clickable">&emsp;&emsp;▲ Folder 2.2</span></p>
                <p style="margin: 4px 0;"><span class="clickable document">&emsp;&emsp;&emsp;&emsp;Document 1</span></p>
                <p style="margin: 4px 0;"><span class="clickable document">&emsp;&emsp;&emsp;&emsp;Document 2</span></p>
                
                <p style="margin: 4px 0;"><span class="clickable">▼ Folder 3</span></p>
                <p style="margin: 4px 0;"><span class="clickable document">Document 0</span></p>
            </aside>
        </div>

        <div class="sidebar-resizer" id="sidebarResizer"></div>

        <button id="sidebarToggle" data-tooltip="<?php echo ($sidebarState === 'collapsed') ? 'Show Sidebar' : 'Hide Sidebar'; ?>"><?php echo ($sidebarState === 'collapsed') ? '▶' : '◀'; ?></button>

        <div id="mainContentScroll" class="page-contents no-padding" style="
            flex: 1;
            min-width: 0;
            height: 92%;
            overflow-y: auto;
            padding-right: 16px; 
        ">
            <main class="mc">
                <br>
                <?php echo page_header("Documents")?>
                <div class="docs-grid" id="docsGrid">
                    <?php list_all_documents($all_docs)?>
                </div>
                <?php echo pagination_controls($currentPage, $totalPages)?>
            </main>
        </div>

        <button id="scrollToTopBtn" class="scroll-action-btn" title="Scroll to top" style="
            position: absolute;
            bottom: 112px; 
            right: 2.5%; 
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 3px solid #B26568;
            background: white;
            color: #B26568;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            z-index: 998;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        ">▲</button>

        <button id="scrollToBottomBtn" class="scroll-action-btn" title="Scroll to bottom" style="
            position: absolute;
            bottom: 60px; 
            right: 2.5%; 
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 3px solid #B26568;
            background: white;
            color: #B26568;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            z-index: 998;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        ">▼</button>
    </div>

    <?php echo footer()?>
    <?php echo document_modal()?>

<script>
    const mainWrapper = document.getElementById("mainWrapper");
    const sidebarWrap = document.getElementById("sidebarWrap");
    const toggle = document.getElementById("sidebarToggle");
    const resizer = document.getElementById("sidebarResizer");
    const resetWidthBtn = document.getElementById("resetWidthBtn");
    
    const mainContentScroll = document.getElementById("mainContentScroll");
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");
    const scrollToBottomBtn = document.getElementById("scrollToBottomBtn");

    let open = !mainWrapper.classList.contains("sidebar-collapsed");

    function setPersistenceCookie(name, value, days = 30) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "; expires=" + date.toUTCString();
        document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/; SameSite=Lax";
    }

    resizer.addEventListener("mousedown", (e) => {
        e.preventDefault();
        
        sidebarWrap.classList.remove("transition-active");
        toggle.classList.remove("transition-active");
        
        resizer.classList.add("is-dragging");
        document.body.classList.add("is-resizing");

        document.addEventListener("mousemove", handleMouseMove);
        document.addEventListener("mouseup", handleMouseUp);
    });

    function handleMouseMove(e) {
        const wrapperRect = mainWrapper.getBoundingClientRect();
        let newWidth = e.clientX - wrapperRect.left;

        if (newWidth < 180) newWidth = 180;
        if (newWidth > 500) newWidth = 500;

        document.documentElement.style.setProperty("--sidebar-width", `${newWidth}px`);
    }

    function handleMouseUp() {
        resizer.classList.remove("is-dragging");
        document.body.classList.remove("is-resizing");
        
        document.removeEventListener("mousemove", handleMouseMove);
        document.removeEventListener("mouseup", handleMouseUp);

        const finalWidth = getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width').trim();
        setPersistenceCookie('sidebar_width', finalWidth);
    }

    resetWidthBtn.addEventListener("click", () => {
        sidebarWrap.classList.add("transition-active");
        toggle.classList.add("transition-active");

        document.documentElement.style.setProperty("--sidebar-width", "260px");
        setPersistenceCookie('sidebar_width', '260px');

        setTimeout(() => {
            if (resizer.classList.contains("is-dragging") === false) {
                sidebarWrap.classList.remove("transition-active");
                toggle.classList.remove("transition-active");
            }
        }, 300);
    });

    toggle.addEventListener("click", () => {
        open = !open;
        
        sidebarWrap.classList.add("transition-active");
        toggle.classList.add("transition-active");

        if (!open) {
            mainWrapper.classList.add("sidebar-collapsed");
            toggle.innerHTML = "▶";
            toggle.setAttribute("data-tooltip", "Show Sidebar"); 
            setPersistenceCookie('sidebar_state', 'collapsed');
        } else {
            mainWrapper.classList.remove("sidebar-collapsed");
            toggle.innerHTML = "◀";
            toggle.setAttribute("data-tooltip", "Hide Sidebar"); 
            setPersistenceCookie('sidebar_state', 'open');
        }
    });

    scrollToBottomBtn.addEventListener("click", () => {
        const target = window.innerWidth <= 768 ? document.documentElement : mainContentScroll;
        target.scrollTo({
            top: target.scrollHeight,
            behavior: "smooth"
        });
    });

    scrollToTopBtn.addEventListener("click", () => {
        const target = window.innerWidth <= 768 ? document.documentElement : mainContentScroll;
        target.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
</script>

    <script src="../script/documents-display.js"></script>
</body>
</html>