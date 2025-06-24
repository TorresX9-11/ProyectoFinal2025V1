// admin.js - Menú hamburguesa responsivo para el panel de administración

document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.getElementById('nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function () {
            navLinks.classList.toggle('open');
            menuToggle.classList.toggle('open');
        });
    }
});
