// All-in-one script for handling document rendering, live search filtering, and category filtering by chip 

let currentCategory = 'All Documents'; 

const Documents = Array.from(document.querySelectorAll('.doc-card-wrapper'));

const searchInput = document.getElementById('searchInput');
const docsGrid = document.getElementById('docsGrid');
const chips = document.querySelectorAll('.chip');

chips.forEach(chip => {
    chip.addEventListener('click', () => {
        document.querySelector('.chip.active').classList.remove('active');
        chip.classList.add('active');

        currentCategory = chip.getAttribute('data-value');
        renderDocs();
    });
});

function renderDocs() {
    const searchTerm = searchInput.value.trim().toLowerCase();

    const filteredDocs = Documents.filter(doc => {
        const title = doc.querySelector('.doc-title').textContent.toLowerCase();
        const description = doc.querySelector('.doc-desc').textContent.toLowerCase();
        const maincateg = doc.querySelector('.doc-card').dataset.category;
        const trackingCode = doc.querySelector('.doc-tc')
            .textContent
            .toLowerCase();

        const matchesSearch = title.includes(searchTerm);
        const matchesCategory =
            currentCategory === 'All Documents' ||
            currentCategory.toLowerCase() === maincateg.toLowerCase();
        const hasValidDescription = description !== "(no description)";
        const matchesDescription =
            hasValidDescription && description.includes(searchTerm);
        const matchesTrackingCode =
            trackingCode.includes(searchTerm);

        return (matchesSearch || matchesDescription || matchesTrackingCode) && matchesCategory;
    });

    docsGrid.innerHTML = "";
    if (filteredDocs.length === 0) docsGrid.innerHTML = '<h1 style="text-align: center; margin: auto">No documents</h1>';

    filteredDocs.forEach(doc => {
        const clonedDoc = doc.cloneNode(true);

        if (searchTerm.trim() !== "") {
            const titleEl = clonedDoc.querySelector('.doc-title');
            const descEl = clonedDoc.querySelector('.doc-desc');
            const tcEl = clonedDoc.querySelector('.doc-tc');

            highlightText(titleEl, searchTerm);
            highlightText(tcEl, searchTerm);

            if (
                descEl.textContent.toLowerCase() !== "(no description)"
            ) {
                highlightText(descEl, searchTerm);
            }
        }

        docsGrid.appendChild(clonedDoc);
    });
}

function getCleanText(element) {
    return element.textContent; // always raw, no markup
}

// function highlightText(element, searchTerm) {
//     if (!searchTerm) return;

//     const originalText = element.textContent;
//     const escapedTerm = searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
//     const regex = new RegExp(`(${escapedTerm})`, 'gi');

//     element.innerHTML = originalText.replace(
//         regex,
//         '<mark class="doc-highlight">$1</mark>'
//     );
// }

function highlightText(element, searchTerm) {
    if (!searchTerm) return;

    const text = element.textContent; // always reset from clean state

    const escapedTerm = searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(${escapedTerm})`, 'gi');

    element.innerHTML = text.replace(
        regex,
        '<mark class="doc-highlight">$1</mark>'
    );
}

function openModal(documentCard) {
    if (!documentCard.classList.contains('doc-card')) return;

    const title = documentCard.querySelector('.doc-title').textContent;
    const catLabel = documentCard.dataset.category;
    const description = documentCard.querySelector('.doc-desc').textContent;

    document.getElementById('modalTitle').innerHTML = title;
    document.getElementById('modalDescription').innerText = description;
    document.getElementById('modalCategory').innerText = catLabel;
    document.getElementById('downloadBtn').href = "../uploads/";
    document.getElementById('docModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('docModal').style.display = 'none';
}

searchInput.addEventListener('input', renderDocs);
document.addEventListener("keydown", (event) => {
    const modal = document.getElementById('docModal');
    if (
        event.key === "Escape" &&
        getComputedStyle(modal).display !== "none"
    ) {
        closeModal();
    }
});
renderDocs();