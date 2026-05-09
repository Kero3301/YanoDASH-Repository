document.addEventListener("DOMContentLoaded", () => {
    const accordionContainers = document.querySelectorAll(".accordion-container");

    accordionContainers.forEach(container => {
        const allowMultiple = container.classList.contains("multiple-open");
        const buttons = container.querySelectorAll(".accordion");

        buttons.forEach(button => {
            button.addEventListener("click", () => {
                const panel = button.nextElementSibling;
                const isActive = button.classList.contains("active");

                if (!allowMultiple) {
                    buttons.forEach(btn => {
                        btn.classList.remove("active");
                        const pnl = btn.nextElementSibling;
                        pnl.style.maxHeight = null;
                    });
                }

                if (!isActive) {
                    button.classList.add("active");
                    panel.style.maxHeight = panel.scrollHeight + "px";
                } else {
                    button.classList.remove("active");
                    panel.style.maxHeight = null;
                }
            });
        });
    });
});