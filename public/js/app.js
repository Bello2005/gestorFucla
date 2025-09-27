// Sidebar functionality
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.querySelector(".sidebar-toggle.d-md-none");
    const overlay = document.querySelector(".sidebar-overlay");

    // Solo para móvil: mostrar/ocultar sidebar
    function toggleSidebarMobile(event) {
        event.preventDefault();
        sidebar.classList.toggle("show");
        overlay.classList.toggle("show");
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", toggleSidebarMobile);
    }

    if (overlay) {
        overlay.addEventListener("click", function () {
            sidebar.classList.remove("show");
            overlay.classList.remove("show");
        });
    }
});
