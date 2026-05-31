document.addEventListener("click", (e) => {
    const btn = e.target.closest(".ms-button");
    const dropdown = e.target.closest(".ms-dropdown");

    // toggle open
    if (btn) {
        const panel = btn.nextElementSibling;
        panel.style.display = panel.style.display === "block" ? "none" : "block";
        return;
    }

    // close outside
    if (!dropdown) {
        document.querySelectorAll(".ms-panel").forEach(p => p.style.display = "none");
    }
});

document.addEventListener("change", (e) => {
    const all = e.target.closest("[data-all]");
    const item = e.target.closest(".ms-item");

    if (all) {
        const group = all.dataset.all;
        const items = document.querySelectorAll(`[data-group="${group}"]`);

        if (all.checked) {
            items.forEach(i => i.checked = false);
        }
        updateButtonLabel(group);

        return;
    }

    if (item) {
        const group = item.dataset.group;
        const allBox = document.querySelector(`[data-all="${group}"]`);

        if (item.checked && allBox) {
            allBox.checked = false;
        }

        const items = document.querySelectorAll(`[data-group="${group}"]`);
        const allSelected = [...items].every(i => i.checked);

        if (allSelected && allBox) {
            items.forEach(i => i.checked = false);
            allBox.checked = true;
        }

        updateButtonLabel(group);
    }
});

function updateButtonLabel(groupId) {
    const dropdown = document.getElementById(groupId);
    const button = dropdown.querySelector(".ms-label");

    const allBox = dropdown.querySelector("[data-all]");
    const items = dropdown.querySelectorAll(".ms-item");

    const selected = [...items].filter(i => i.checked);
    const total = items.length;

    // CASE 1: ALL selected
    if (allBox && allBox.checked) {
        button.textContent = `All ${total} selected`;
        return;
    }

    // CASE 2: none selected
    if (selected.length === 0) {
        button.textContent = `0 selected`;
        return;
    }

    // CASE 3: partial selection
    button.textContent = `${selected.length} selected`;
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".ms-dropdown").forEach(dd => {
        updateButtonLabel(dd.id);
    });
});