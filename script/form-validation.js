document.addEventListener("DOMContentLoaded", () => {
    
    document.querySelectorAll("input:not([type='checkbox']), textarea, select")
    .forEach(input => {
        if (!input.closest(".input-wrapper") && !input.closest(".password-input")) {
            const wrapper = document.createElement("div");
            wrapper.classList.add("input-wrapper");

            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            wrapper.style.position = "relative";
        }
    });

    const forms = document.querySelectorAll("form");

    const password = document.querySelector("#login-enter-password-inputfield");
    if (password) password.setAttribute("required", true);

    forms.forEach(form => {
        form.setAttribute("novalidate", true);

        const inputs = form.querySelectorAll(`
            input:not([type='submit']):not([type='button']):not([type='reset']):not([type='checkbox']),
            textarea,
            select
        `);

        inputs.forEach(input => {
            input.addEventListener("input", () => {
                if (input.classList.contains("error")) {
                    validateInput(input);
                }
            });
        });

        form.addEventListener("submit", (e) => {
            let isValid = true;

            inputs.forEach(input => {
                if (!validateInput(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });
});


function validateInput(input) {

    if (input.type === "checkbox") return true;

    const value = input.value.trim();
    let isValid = true;
    let message = "";

    if (input.hasAttribute("required") && value === "") {
        if (input.id === "notes") {
            message = "Please enter a document ID";

        } else if (input.tagName === "SELECT") {
            message = "Please choose an option";

        } else {
            message = "This field is required";
        }

        isValid = false;
    }

    if (input.type === "email" && value !== "") {
        const pattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
        if (!pattern.test(value)) {
            message = "Invalid email";
            isValid = false;
        }
    }

    if (input.hasAttribute("minlength")) {
        const min = input.getAttribute("minlength");
        if (value.length < min) {
            message = `Minimum ${min} characters`;
            isValid = false;
        }
    }

    if (input.type === "file" && input.files.length > 0) {
        const file = input.files[0];
        const maxSize = 5 * 1024 * 1024;

        if (file.size > maxSize) {
            message = "File too large (max 5MB)";
            isValid = false;
        }
    }

    toggleError(input, message);
    return isValid;
}


function toggleError(input, message) {
    if (input.type === "checkbox") return;

    let wrapper = input.closest(".input-wrapper") || input.closest(".password-input");

    let error = wrapper.querySelector(".error-message");

    if (message) {

        if (!error) {
            error = document.createElement("small");
            error.classList.add("error-message");
            wrapper.appendChild(error);
        }

        error.textContent = message;
        error.classList.add("show");

        if (input.closest(".password-input")) {
            input.closest(".password-input").classList.add("error");
        } else {
            input.classList.add("error");
        }

    } else {
        if (error) {
            error.remove();
        }

        if (input.closest(".password-input")) {
            input.closest(".password-input").classList.remove("error");
        } else {
            input.classList.remove("error");
        }
    }
}