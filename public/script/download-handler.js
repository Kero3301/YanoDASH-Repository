document.querySelectorAll('.download-btn').forEach(button => {
    button.addEventListener('click', () => {
        const versionID = button.dataset.versionId;
        const link = document.createElement('a');

        link.href =
            `/yanodash-repository/public/download?file_id=${versionID}`;

        link.style.display = 'none';

        document.body.appendChild(link);

        link.click();

        document.body.removeChild(link);
    });
});