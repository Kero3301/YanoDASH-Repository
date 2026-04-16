<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
     <?php initializePage("Latest Releases | YanoDASH")?>
    <link rel="stylesheet" href="../css/docsss.css"/>
</head>
<body>
    <?php echo navbar(1) ?>
<div class="archive-container">
    <a href="index.php" id="b-back">Back to Index</a>
    <div class="search-panel">
        <div class="search-wrapper">
            <input type="text" id="searchInput" placeholder="     Search..." autocomplete="off">
        </div>
    </div>
    <header id="title"> <h1> Latest Releases </h1> </header>
    </div> <div class="docs-grid" id="docsGrid">
    </div>

<div id="docModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Document Title</h3>
        <p id="modalCategory"></p>
        <p id="modalDate"></p>
        <div class="modal-buttons">
            <button class="btn btn-secondary" onclick="closeModal()">Close</button>
            <a id="downloadBtn" href="#" class="btn btn-primary" target="_blank">Open File</a>
        </div>
    </div>
</div>


<script> 
    // Your Data Array (Note: Fixed a syntax error in your original fileRef string)
    const Documents = [
        {id: "LR-2026-01", title: "Hudyaka", category: "Departmental", fileRef: "anu.pdf", date: "2026-04-08", dept: "USeP", desc: "Official publication for the first quarter." }

    ];

    const docsGrid = document.getElementById('docsGrid');
    const searchInput = document.getElementById('searchInput');
    const filterBadges = document.querySelectorAll('.filter-badge');

    // Function to render cards
    function renderDocs(filter = 'all', search = '') {
        docsGrid.innerHTML = '';
        
        const filtered = Documents.filter(doc => {
            const matchesFilter = filter === 'all' || doc.category === filter;
            const matchesSearch = doc.title.toLowerCase().includes(search.toLowerCase());
            return matchesFilter && matchesSearch;
        });

        filtered.forEach(doc => {
            const card = document.createElement('div');
            card.className = 'doc-card';
            card.onclick = () => openModal(doc);
            card.innerHTML = `
                <div class="card-content">
                    <span class="doc-badge">${doc.category}</span>
                    <h2 class="doc-title">${doc.title}</h2>
                    <div class="doc-meta">
                        <span>📅 ${doc.date}</span>
                        <span>🏢 ${doc.dept.toUpperCase()}</span>
                    </div>
                    <p class="doc-desc">${doc.desc || 'No description available.'}</p>
                </div>
            `;
            docsGrid.appendChild(card);
        });
    }

    // Search Event
    searchInput.addEventListener('input', (e) => {
        const activeFilter = document.querySelector('.filter-badge.active').dataset.filter;
        renderDocs(activeFilter, e.target.value);
    });

    // Filter Event
    filterBadges.forEach(badge => {
        badge.addEventListener('click', () => {
            filterBadges.forEach(b => b.classList.remove('active'));
            badge.classList.add('active');
            renderDocs(badge.dataset.filter, searchInput.value);
        });
    });

    // Modal Logic
    function openModal(doc) {
        document.getElementById('modalTitle').innerText = doc.title;
        document.getElementById('modalCategory').innerText = `Category: ${doc.category}`;
        document.getElementById('modalDate').innerText = `Date: ${doc.date}`;
        document.getElementById('downloadBtn').href = `../uploads/${doc.fileRef}`;
        document.getElementById('docModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('docModal').style.display = 'none';
    }

    // Initial Load
    renderDocs();
</script>
</body>
</html>