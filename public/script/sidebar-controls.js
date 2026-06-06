const mainDiv = document.querySelector('#main');

function isDesktop() {
    return window.innerWidth > 767;
}

function setLsbOpen(open) {
    mainDiv?.classList.toggle('lsb-open', open);

    if (isDesktop()) {
        localStorage.setItem('lsb-open', open);
    }
}

function toggleLsb() {
    if (!mainDiv) return;

    const open = !mainDiv.classList.contains('lsb-open');
    setLsbOpen(open);
}

document.addEventListener('DOMContentLoaded', () => {
    if (isDesktop()) {
        setLsbOpen(localStorage.getItem('lsb-open') === 'true');
    }
});

window.addEventListener('resize', () => {
    if (!isDesktop()) {
        mainDiv?.classList.remove('lsb-open');
    }
});