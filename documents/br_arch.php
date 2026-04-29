<?php
    session_start();
    require_once '../components/head.php';
    require_once '../components/navbar.php';
    require_once 'docEd.php';
    $all_docs = Documents::getAll();
?>
<!DOCTYPE html>
<html>
<head>
    <?php initializePage("All Documents | YanoDASH")?>
    <link rel="stylesheet" href="../css/docsss.css"/>
</head>
<body>
    <?php echo navbar(1)?>
<div class="archive-container">
    <a href="index.php" id="b-back">Back to Index</a>
    <div class="search-panel">
        <div class="search-wrapper">
            <input type="text" id="searchInput" placeholder="Search by title..." autocomplete="off">
        </div>
    </div>
    <header id="title"> 
        <h1>Documents</h1>
        <!-- <form class="category">
            <label>Filter:</label>
            <select id="categorySelect">
                <option value="all">All Documents</option>
                <option value="Activity Designs">Activity Designs</option>
                <option value="Memorandum">Memorandum</option>
                <option value="Financial Statements">Financial Statements</option>
                <option value="Minutes of Meetings">Munites of Meetings</option>
                <option value="Financial Statements">Financial Statements</option>
                <option value="Accomplishment Report">Accomplishment Report</option>
                <option value="Project Proosal">Project Proposal</option>
            </select>
        </form> -->
        <div class="filter-section">
            <!-- <label>Filter by Category:</label> -->
            <div class="filter-chips" id="categoryChips">
                <button class="chip active" data-value="all">All Documents</button>
                <button class="chip" data-value="Activity Designs">Activity Designs</button>
                <button class="chip" data-value="Memorandum">Memorandum</button>
                <button class="chip" data-value="Financial Statements">Financial Statements</button>
                <button class="chip" data-value="Minutes of Meetings">Minutes of Meetings</button>
                <button class="chip" data-value="Accomplishment Report">Accomplishment Report</button>
                <button class="chip" data-value="Project Proposal">Project Proposal</button>
            </div>
    </div>

    </header>
</div> 

<div class="docs-grid" id="docsGrid"></div>

<div id="docModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle"></h3>
        <p id="modalCategory"></p>
        <div class="modal-buttons">
            <button class="btn btn-secondary" onclick="closeModal()">Close</button>
            <a id="downloadBtn" href="#" class="btn btn-primary" download>Download</a>
        </div>
    </div>
</div>

<script> 
    // 1. Pass PHP data to JS
    const Documents = <?php echo json_encode($all_docs); ?>;
    let currentCategory = 'all'; 

    const chips = document.querySelectorAll('.chip');
    const searchInput = document.getElementById('searchInput');
    const docsGrid = document.getElementById('docsGrid');

    // 2. Chip Click Handler
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            document.querySelector('.chip.active').classList.remove('active');
            chip.classList.add('active');
            
            currentCategory = chip.getAttribute('data-value');
            renderDocs();
        });
    });

    // 3. Rendering Engine
    function renderDocs() {
        const searchTerm = searchInput.value.toLowerCase();
        docsGrid.innerHTML = '';

        const filteredDocs = Documents.filter(doc => {
            const matchesSearch = doc.doc_title.toLowerCase().includes(searchTerm);
            
            // Handle both String and Array categories
            const docCats = Array.isArray(doc.category) ? doc.category : [doc.category];
            
            // Match if 'all' or if the chip value exists in doc's categories
            const matchesCategory = (currentCategory === 'all' || docCats.includes(currentCategory));
            
            return matchesSearch && matchesCategory;
        });

        filteredDocs.forEach(doc => {
            const card = document.createElement('div');
            card.className = 'doc-card';
            card.onclick = () => openModal(doc);
            
            const categoryLabel = Array.isArray(doc.category) ? doc.category.join(', ') : doc.category;

            card.innerHTML = `
                <div class="card-content">
                    <span class="doc-badge">${categoryLabel}</span>
                    <h2 class="doc-title">${doc.doc_title}</h2>
                    <div class="doc-meta">
                        <span>📅 ${doc.dates}</span>
                        <span>🏢 ${doc.area}</span>
                    </div>
                </div>`;
            docsGrid.appendChild(card);
        });
    }

    function openModal(doc) {
        document.getElementById('modalTitle').innerText = doc.doc_title;
        // Show all categories in the modal[cite: 14]
        const catLabel = Array.isArray(doc.category) ? doc.category.join(', ') : doc.category;
        document.getElementById('modalCategory').innerText = catLabel;
        document.getElementById('downloadBtn').href = "../uploads/" + doc.file_path;
        document.getElementById('docModal').style.display = 'flex';
    }

    function closeModal() { 
        document.getElementById('docModal').style.display = 'none'; 
    }

    searchInput.addEventListener('input', renderDocs);
    renderDocs();
</script>
</body>
</html>