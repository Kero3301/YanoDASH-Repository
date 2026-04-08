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
}