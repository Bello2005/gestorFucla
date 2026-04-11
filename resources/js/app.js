import "./bootstrap";

document.addEventListener("DOMContentLoaded", function () {
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function () {
            sidebar.classList.toggle("collapsed");
            document.body.classList.toggle("sidebar-collapsed");
            if (mainContent) {
                mainContent.classList.toggle("sidebar-collapsed");
            }
        });
    }

    document.querySelectorAll(".js-count-up").forEach(function (el) {
        var target = parseInt(el.getAttribute("data-target") || "0", 10);
        if (!target || window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
            return;
        }
        var start = 0;
        var dur = 600;
        var t0 = performance.now();
        function frame(t) {
            var p = Math.min((t - t0) / dur, 1);
            el.textContent = Math.floor(start + (target - start) * p).toLocaleString();
            if (p < 1) {
                requestAnimationFrame(frame);
            }
        }
        requestAnimationFrame(frame);
    });
});
