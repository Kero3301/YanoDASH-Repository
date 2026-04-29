<?php
    session_start();
    require_once '../components/head.php';
    require_once '../components/navbar.php';
    require_once 'docEd.php'; // Connects to the same data

    $all_docs = Documents::getAll();
    
    // Sort by date: Newest first
    usort($all_docs, function($a, $b) {
        return strtotime($b['dates']) - strtotime($a['dates']);
    });
    
    // Only show the 5 most recent
    $latest_docs = array_slice($all_docs, 0, 5);
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
    <header id="title"> <h1>Latest Releases</h1> </header>
</div> 

<div class="docs-grid" id="docsGrid"></div>

<div id="docModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle"></h3>
        <p id="modalCategory"></p>
        <p id="modalDate"></p>
        <div class="modal-buttons">
            <button class="btn btn-secondary" onclick="closeModal()">Close</button>
            <a id="downloadBtn" href="#" class="btn btn-primary" download>Download</a>
        </div>
    </div>
</div>

<script> 
    const Documents = <?php echo json_encode($latest_docs); ?>;
    const docsGrid = document.getElementById('docsGrid');

    function renderDocs() {
        docsGrid.innerHTML = '';
        Documents.forEach(doc => {
            const card = document.createElement('div');
            card.className = 'doc-card';
            card.onclick = () => openModal(doc);
            card.innerHTML = `
                <div class="card-content">
                    <span class="doc-badge">${doc.category}</span>
                    <h2 class="doc-title">${doc.doc_title}</h2>
                    <div class="doc-meta"><span>📅 ${doc.dates}</span>
                            <span>🏢 ${doc.area}</span></div>
                </div>`;
            docsGrid.appendChild(card);
        });
    }

    function openModal(doc) {
        document.getElementById('modalTitle').innerText = doc.doc_title;
        document.getElementById('modalCategory').innerText = doc.category;
        document.getElementById('modalDate').innerText = "Released on: " + doc.dates;
        document.getElementById('downloadBtn').href = "../uploads/" + doc.file_path;
        document.getElementById('docModal').style.display = 'flex';
    }

    function closeModal() { document.getElementById('docModal').style.display = 'none'; }
    renderDocs();
</script>
</body>
</html>