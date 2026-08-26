<header id="site-header">
    <div class="container">
        <div class="header-content">
            <a href="<?php echo BASE_URL; ?>/" class="logo">
                <img src="<?php echo BASE_URL; ?>/assets/img/logo.jpg" alt="LC Soluções em Móveis" width="40" height="40">
                <div class="logo-text"> <span>LC Soluções em Móveis</span></div>
            </a>

            <nav class="nav-desktop" id="main-nav">
                <a href="<?php echo BASE_URL; ?>/" class="nav-link">Início</a>
                <a href="<?php echo BASE_URL; ?>/servicos" class="nav-link">Serviços</a>
                <a href="<?php echo BASE_URL; ?>/portfolio" class="nav-link">Portfólio</a>
                <a href="<?php echo BASE_URL; ?>/clientes" class="nav-link">Clientes</a>
                <a href="<?php echo BASE_URL; ?>/contato" class="nav-link">Contato</a>
                <a href="<?php echo BASE_URL; ?>/sobre" class="nav-link">Sobre</a>
                <a href="<?php echo BASE_URL; ?>/faq" class="nav-link">FAQ</a>
                <a href="<?php echo BASE_URL; ?>/blog" class="nav-link">Blog</a>
            </nav>

            <button class="mobile-toggle" id="mobile-menu-button" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<script>
    window.addEventListener('scroll', () => {
        const header = document.getElementById('site-header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    const toggleBtn = document.getElementById('mobile-menu-button');
    const nav = document.getElementById('main-nav');
    if (toggleBtn && nav) {
        toggleBtn.addEventListener('click', () => {
            nav.classList.toggle('active');
            const icon = toggleBtn.querySelector('i');
            if (nav.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
</script>
