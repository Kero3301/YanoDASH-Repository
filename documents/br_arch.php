<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initializePage("All Documents | YanoDASH")?>
        <link rel="stylesheet" href="docsss.css"/>
    </head>
    <body>
        <?php echo navbar(1)?>
<div class="archive-container">
    <div class="search-panel">
        <div class="search-wrapper">
            <input type="text" id="searchInput" placeholder="     Search..." autocomplete="off">
        </div>
    </div>
    <header id="title"> <h1>Documents </h1>

 <form class="category">
            <label for="category">Category:</label>
            <select name="category" id="category">
                <option value="all">All Documents</option>
                <option value="Departmental Documents">Departmental</option>
                <option value="Council Documents">Council</option>
            </select>
            <button type="button" id="filterBtn">Show</button>
    </form>
    </header>
</div> 

<div class="docs-grid" id="docsGrid">
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
    // Your Data Array
    const Documents = [
        {id: "LR-2026-01", title: "Hudyaka", category: "Council Documents", fileRef: "anu.pdf", date: "2026-04-08", dept: "USeP", desc: "Official publication for the first quarter." },
        {id: "BA-123", title: "Ambut", category: "Departmental Documents", fileRef: "wala.pdf", date: "2026-04-04", dept: "CIC", desc: "Archived records for departmental review." },
        {id: "DD-111", title: "Hayst", category: "Departmental Documents", fileRef: "sample.pdf", date: "2026-01-05", dept: "CIC", desc: "Guidelines for system development." },
        {id: "CD-001", title: "Hawak ko ang beat", category: "Council Documents", fileRef: "council.pdf", date: "2026-01-03", dept: "CT", desc: "Meeting minutes from the student council." },
    ];

    const docsGrid = document.getElementById('docsGrid');
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('category');
    const filterBtn = document.getElementById('filterBtn');

    // Function to render cards based on category and search
    function renderDocs() {
        const selectedCategory = categorySelect.value;
        const searchTerm = searchInput.value;
        
        docsGrid.innerHTML = '';
        
        const filtered = Documents.filter(doc => {
            // Filter by category
            const matchesCategory = selectedCategory === 'all' || doc.category === selectedCategory;
            
            // Filter by search
            const matchesSearch = doc.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                                 doc.desc.toLowerCase().includes(searchTerm.toLowerCase());
            
            return matchesCategory && matchesSearch;
        });

        if (filtered.length === 0) {
            docsGrid.innerHTML = '<div class="no-results">No documents found in this category.</div>';
            return;
        }

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

    // Filter button click event
    filterBtn.addEventListener('click', () => {
        renderDocs();
    });

    // Search input event (real-time search)
    searchInput.addEventListener('input', () => {
        renderDocs();
    });

    // Also filter when dropdown changes (optional - removes need for button)
    categorySelect.addEventListener('change', () => {
        renderDocs();
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

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('docModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    // Initial Load
    renderDocs();
</script>



    </body>
</html>