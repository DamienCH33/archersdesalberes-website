document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.querySelector(".menu-toggle");
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.querySelector(".sidebar-overlay");

    if (!toggle || !sidebar) return;

    function openMenu() {
        sidebar.classList.add("open");
        if (overlay) overlay.classList.add("open");
    }
    function closeMenu() {
        sidebar.classList.remove("open");
        if (overlay) overlay.classList.remove("open");
    }

    toggle.addEventListener("click", () => {
        sidebar.classList.contains("open") ? closeMenu() : openMenu();
    });
    if (overlay) overlay.addEventListener("click", closeMenu);

    sidebar.querySelectorAll(".sidebar-link").forEach((link) => {
        link.addEventListener("click", closeMenu);
    });
});
