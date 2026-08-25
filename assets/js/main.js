// main.js - Client side logic

document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu functionality
    const btn = document.getElementById('mobile-menu-button');
    const nav = document.getElementById('main-nav');

    if (btn && nav) {
        btn.addEventListener('click', () => {
            const opened = nav.classList.toggle('open');
            btn.setAttribute('aria-expanded', opened ? 'true' : 'false');
        });

        nav.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    nav.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }

    // Smooth scroll for anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
