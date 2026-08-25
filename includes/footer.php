<footer role="contentinfo">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>LC <span>Soluções</span></h3>
                <p style="margin-bottom: 20px; font-size: 0.9rem; line-height: 1.7;">Móveis planejados de alta performance e design exclusivo em Campo Grande/MS.</p>
                <div class="social-links">
                    <a href="https://www.instagram.com/lcsolucoesemmoveis" target="_blank" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/556799712508" target="_blank" class="social-link" aria-label="WhatsApp Empresa"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Navegação</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>/">Início</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/portfolio">Portfólio</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/clientes">Clientes</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/sobre">Sobre</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/blog">Blog</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Serviços</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>/portfolio">Cozinhas Planejadas</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/portfolio">Dormitórios</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/portfolio">Salas e Painéis</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/portfolio">Home Office</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contato</h3>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt" style="color:var(--accent-light); width:20px;"></i> Rua Francisco José Abrão, 525 - Campo Grande/MS</li>
                    <li><i class="fas fa-phone-alt" style="color:var(--accent-light); width:20px;"></i> <a href="tel:6732537898">(67) 3253-7898</a></li>
                    <li><i class="fab fa-whatsapp" style="color:var(--accent-light); width:20px;"></i> <a href="https://wa.me/556799712508">WhatsApp Empresa</a></li>
                    <li><i class="fas fa-user-tie" style="color:var(--accent-light); width:20px;"></i> <a href="https://wa.me/5567998284808">WhatsApp Consultoria</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> LC Soluções em Móveis. Todos os direitos reservados.</p>
            <div>
                <a href="<?php echo BASE_URL; ?>/termodeservico" style="margin-right: 20px;">Termos de Serviço</a>
                <a href="<?php echo BASE_URL; ?>/politicadeprivacidade" style="margin-right: 20px;">Política de Privacidade</a>
                <a href="<?php echo BASE_URL; ?>/login" title="Painel Administrativo" style="opacity:0.3; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.3'"><i class="fas fa-lock" style="font-size:0.7rem;"></i></a>
            </div>
        </div>
    </div>
</footer>

<a href="https://wa.me/556799712508" class="whatsapp-float" target="_blank" aria-label="WhatsApp Empresa">
    <i class="fab fa-whatsapp"></i>
</a>

<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
