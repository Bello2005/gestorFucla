import "./bootstrap";

document.addEventListener("DOMContentLoaded", function () {
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    if (sidebarToggle && sidebar) {
        console.log("Elementos:", { sidebar, mainContent, sidebarToggle });
        sidebarToggle.addEventListener("click", function () {
            sidebar.classList.toggle("collapsed");
            document.body.classList.toggle("sidebar-collapsed");
            if (mainContent) {
                mainContent.classList.toggle("sidebar-collapsed");
            }
            console.log("Toggle clicked");
            console.log("Sidebar classes:", sidebar.className);
        });
    } else {
        console.log("Sidebar o toggle no encontrados");
    }
});
