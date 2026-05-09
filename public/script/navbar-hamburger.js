document.addEventListener("DOMContentLoaded", () => {
    const mediaQuery = window.matchMedia("(min-width: 768px)");

    const hamburger = document.querySelector(".hamburger");
    const navLinks = document.querySelector("#nav-links");
    const dropdowns = document.querySelectorAll("#nav-links .dropdown");

    if (hamburger && navLinks) {
        // TOGGLE NAV
        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navLinks.classList.toggle("active");
            dropdowns.forEach(d => d.classList.remove("open"));
        });

        // DROPDOWN CLICK
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener("mouseenter", () => {
                if (!mediaQuery.matches) return;
                dropdown.classList.add("open");
            });

            dropdown.addEventListener("mouseleave", () => {
                if (!mediaQuery.matches) return;
                dropdown.classList.remove("open");
            });

            dropdown.addEventListener("click", function (e) {
                // ignore clicks inside dropdown menu
                if (e.target.closest(".menu")) return;

                e.preventDefault();
                dropdown.classList.toggle("open");
                // close other open dropdown menus
                dropdowns.forEach(d => {
                    if (d !== dropdown) d.classList.remove("open");
                });
            });
        });

        document.querySelectorAll(".menu a").forEach(link => {
            link.addEventListener("click", () => {
                dropdowns.forEach(d => d.classList.remove("open"));
                navLinks.classList.remove("active");
            });
        });

        document.addEventListener("click", (e) => {
            if (!e.target.closest("#nav-links")) {
                dropdowns.forEach(d => d.classList.remove("open"));
            }
        });
    }

    const highlight = document.querySelector("#nav-highlight");
    const navItems = document.querySelectorAll(".nav-item-link");

    
    let isHidden = true;


    function moveHighlight(el) {
        const rect = el.getBoundingClientRect();
        const parentRect = el.closest("#nav-links").getBoundingClientRect();

        const x = rect.left - parentRect.left;
        const w = rect.width;

        if (isHidden) {
            highlight.style.transformOrigin = "left center"; // 👈 reset

            highlight.style.transition = "none";
            highlight.style.transform = `translateX(${x}px) scaleX(0)`;
            highlight.style.width = `${w}px`;

            highlight.offsetHeight;

            highlight.style.transition = "transform 0.25s ease, opacity 0.15s ease";
            highlight.style.transform = `translateX(${x}px) scaleX(1)`;
        }
        else {
            highlight.style.transition = "transform 0.25s ease, width 0.25s ease";
            highlight.style.transform = `translateX(${x}px) scaleX(1)`;
            highlight.style.width = `${w}px`;
        }

        highlight.style.opacity = "1";
        isHidden = false;
    }

    const q = window.matchMedia("(min-width: 768px)");

    navItems.forEach(item => {
        item.addEventListener("mouseenter", () => {
            if (!q.matches) return;
            moveHighlight(item);
        });
    });

    document.querySelector("#nav-links").addEventListener("mouseleave", () => {
        highlight.style.transformOrigin = "right center";
        highlight.style.transition = "transform 0.35s ease, opacity 0.2s ease";
        highlight.style.transform = highlight.style.transform.replace(/scaleX\(1\)/, "scaleX(0)");

        highlight.style.opacity = "0";
        isHidden = true;
    });
});