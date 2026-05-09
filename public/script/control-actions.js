function togglePasswordVisibility(toggleButton) {
    if (!(toggleButton instanceof HTMLButtonElement)) return;

    const parent = toggleButton.parentElement;
    if (!parent || !parent.classList.contains('password-input')) return;

    const inputField = parent.querySelector('input');
    if (!inputField || !inputField.classList.contains('password-input-field')) return;
    
    if (inputField.type !== "password" && inputField.type !== "text") return;
    const isPassword = inputField.type === "password";

    inputField.type = isPassword? "text" : "password";
    toggleButton.textContent = isPassword? "⊚" : "⊘";
    toggleButton.classList.toggle('active', isPassword);
}

function hidePassword(toggleButtonID) {
    if (typeof toggleButtonID !== "string") return;

    const toggleButton = document.querySelector('#' + toggleButtonID);
    if (!(toggleButton instanceof HTMLButtonElement)) return;

    const parent = toggleButton.parentElement;
    if (!parent || !parent.classList.contains('password-input')) return;

    const inputField = parent.querySelector('input');
    if (!inputField || !inputField.classList.contains('password-input-field')) return;

    if (inputField.type !== "password" && inputField.type !== "text") return;
    
    inputField.type = "password";
    toggleButton.textContent = "⊘";
    toggleButton.classList.remove('active');
}

// function setElementsLockedByIDs(ids, locked = true, allowedTags = ["input", "textarea", "select"]) {
//     if (!Array.isArray(ids)) return;

//     const normalizedTags = allowedTags
//         .filter(t => typeof t === "string")
//         .map(t => t.toLowerCase());

//     for (const id of ids) {
//         if (typeof id !== "string") continue;

//         const element = document.getElementById(id);
//         if (!element) continue;

//         const tag = element.tagName.toLowerCase();
//         if (!normalizedTags.includes(tag)) continue;

//         if (tag === "input" || tag === "textarea") {
//             const type = element.type?.toLowerCase?.();
//             if (type !== "checkbox" && type !== "radio")
//                 element.readOnly = locked;
//         }

//         element.classList.toggle("locked", locked);
//     }
// }

// function setElementsLockedByIDs(ids, locked = true) {
//     if (!Array.isArray(ids)) return;

//     for (const id of ids) {
//         const el = document.getElementById(id);
//         if (!el) continue;

//         // lock ALL form controls properly
//         if (el.tagName === "INPUT" || el.tagName === "TEXTAREA" || el.tagName === "SELECT") {
//             // el.disabled = locked;          // 🔥 THIS is the real lock
//             el.readOnly = locked;
//         }

//         el.classList.toggle("locked", locked);

//         // ALSO lock wrapper visually if exists
//         const wrapper =
//             el.closest(".input-wrapper") ||
//             el.closest(".password-input-wrapper");

//         if (wrapper) {
//             wrapper.classList.toggle("locked", locked);
//         }
//     }
// }


function setElementsLockedByIDs(ids, locked = true, allowedTags = ["input", "textarea", "select"]) {
    if (!Array.isArray(ids)) return;

    const normalizedTags = allowedTags
        .filter(t => typeof t === "string")
        .map(t => t.toLowerCase());

    for (const id of ids) {
        if (typeof id !== "string") continue;

        const element = document.getElementById(id);
        if (!element) continue;

        const tag = element.tagName.toLowerCase();
        if (!normalizedTags.includes(tag)) continue;

        if (tag === "input" || tag === "textarea") {
            const type = element.type?.toLowerCase?.();
            if (type !== "checkbox" && type !== "radio")
                element.readOnly = locked;
        }

        element.classList.toggle("locked", locked);
    }
}