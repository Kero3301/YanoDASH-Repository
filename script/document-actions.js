document.addEventListener('click', function (event) {
    const button = event.target.closest('.document-action');
    if (!button) return;

    const documentID = button.dataset.linkedDocument;
    
    const action = button.dataset.action;

    console.log('Clicked Document ID: ', documentID);

    switch (action) {
        case 'view': alert("View"); break;
        case 'edit': alert("Edit"); break;
        case 'protect': alert("Protect"); break;
        case 'delete': alert("Delete"); break;
    }
});

document.addEventListener('click', (e) => {
    const card = e.target.closest('.doc-card');
    if (!card) return;

    // const title = card.querySelector('.doc-title').textContent;
    if (e.target.closest('button')) return;

    const id = card.dataset.documentId;
    const status = card.dataset.status;
    const publicity = card.dataset.publicity;

    switch (status) {
        case 'nonfinal': 
        case 'draft': 
            window.location.href = `http://localhost/yanodash-repository/dms/manage-document?id=` + id; 
            return;
        default: 
            openModal(card);
            return;
    }
});

