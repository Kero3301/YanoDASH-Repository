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
?>
<!DOCTYPE html>
<html>
<head>
    <?php initialize_page("All Documents | YanoDASH")?>
    <link rel="stylesheet" href="../css/pages/docsss.css"/>
    
    <style>
        /* Modern Scrollbar for page contents */
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

        /* Tooltip Styling */
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

        /* Hover Action Buttons */
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
            border-radius: 16px;
            padding: 6px 10px;
            cursor: pointer;
            background: rgba(0,0,0,0.02);
        }
        .clickable:hover {
            background: rgba(255,0,0,0.15);
        }
        .document {
            color: #9a082d
        }
        .page-header {
            width: 100%;
        }
        .page-contents {
            border: none;
            overflow: hidden;
        }

        /* Responsive Layout Grid & Architecture */
        /* Update the layout width configuration here */
.mw {
    display: flex;
    gap: 16px;
    width: 99%; /* Change from 95% to 99% to match your system defaults */
    margin: auto;
    height: calc(100vh - 90px);
    position: relative;
}

        #sidebarWrap {
            position: relative;
            flex-basis: 260px;
            flex-shrink: 0;
            overflow: hidden;
            transition: flex-basis 0.25s ease, opacity 0.15s ease, margin 0.25s ease;
        }

        #sidebarToggle {
            position: absolute;
            top: 50%;
            left: 260px; /* Aligns with sidebar flex basis */
            transform: translate(-50%, -50%);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: black;
            color: white;
            border: none;
            cursor: pointer;
            z-index: 999;
            transition: left 0.25s ease; 
        }

        /* State Class Modification via CSS instead of hardcoded JS styles */
        .mw.sidebar-collapsed #sidebarWrap {
            flex-basis: 0px;
            opacity: 0;
            margin-right: -16px; /* Negates flexbox gap styling when hidden */
            pointer-events: none;
        }
        .mw.sidebar-collapsed #sidebarToggle {
            left: 0px;
        }

        /* Responsive Design Breakpoint for Tablets and Mobiles */
        @media (max-width: 768px) {
            .mw {
                flex-direction: column;
                height: auto;
                gap: 20px;
            }
            #sidebarWrap {
                flex-basis: auto;
                width: 100%;
            }
            #sidebar {
                height: auto;
                max-height: 300px;
            }
            #sidebarToggle {
                display: none; /* Hide toggle on mobile layouts */
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

    <div class="mw" id="mainWrapper">
        <div id="sidebarWrap">
            <aside id="sidebar" style="
                height: 92%;
                overflow-y: auto;
                border-radius: 8px;
                border-top: 5px solid #7f0000;
                padding: 12px;
                background: white;
                width: 100%;
                min-width: 240px; 
                white-space: nowrap;
            ">
                <h2 style="text-decoration: underline 2px red; text-align: center; margin-bottom: 12px;">Sidebar</h2>
                
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

        <button id="sidebarToggle" data-tooltip="Hide Sidebar">◀</button>

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

        <!-- Scroll Actions -->
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
    const toggle = document.getElementById("sidebarToggle");
    
    const mainContentScroll = document.getElementById("mainContentScroll");
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");
    const scrollToBottomBtn = document.getElementById("scrollToBottomBtn");

    let open = true;

    toggle.addEventListener("click", () => {
        open = !open;
        if (!open) {
            mainWrapper.classList.add("sidebar-collapsed");
            toggle.innerHTML = "▶";
            toggle.setAttribute("data-tooltip", "Show Sidebar"); 
        } else {
            mainWrapper.classList.remove("sidebar-collapsed");
            toggle.innerHTML = "◀";
            toggle.setAttribute("data-tooltip", "Hide Sidebar"); 
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