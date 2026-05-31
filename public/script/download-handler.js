const toast = document.getElementById('download-toast');

const toastMessage = toast?.querySelector('.toast-message');
const toastClose = toast?.querySelector('.toast-close');

let toastTimeout;

function hideToast() {
    toast.classList.remove('show');
}

function showDownloadToast(message) {

    toastMessage.textContent = message;

    toast.classList.add('show');

    clearTimeout(toastTimeout);

    toastTimeout = setTimeout(() => {
        hideToast();
    }, 3000);
}

toastClose?.addEventListener('click', () => {
    clearTimeout(toastTimeout);
    hideToast();
});

document.addEventListener('keydown', (event) => {

    if (event.key !== 'Escape') {
        return;
    }

    if (!toast.classList.contains('show')) {
        return;
    }

    clearTimeout(toastTimeout);

    hideToast();
});

document.querySelectorAll('.download-btn').forEach(button => {
    button.addEventListener('click', () => {

        showDownloadToast('Your download will begin shortly.');

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