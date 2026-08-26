<?php // Título e descrição agora são passados pelo Controller ?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Contato</span>
        <h1 class="page-hero-title">Fale <span>conosco</span></h1>
        <div class="accent-line center"></div>
        <p class="page-hero-subtitle">Estamos prontos para transformar seus sonhos em projetos reais e sofisticados.</p>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-form-wrapper">
                <form action="<?php echo BASE_URL; ?>/contato/enviar" method="POST" class="contact-form" id="contactForm">
                    <div class="form-group">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="nome" class="form-input" required placeholder="Seu nome">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telefone / WhatsApp</label>
                        <input type="tel" name="whatsapp" class="form-input" required placeholder="(67) 99999-9999">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Categoria</label>
                        <select name="assunto" class="form-input" required>
                            <option value="">Selecione</option>
                            <option>Orçamento</option>
                            <option>Dúvida</option>
                            <option>Agendar Visita</option>
                            <option>Projeto Sob Medida</option>
                            <option>Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mensagem</label>
                        <textarea name="mensagem" class="form-input form-textarea" required placeholder="Descreva sua ideia..."></textarea>
                    </div>

                    <button type="submit" class="btn-primary" style="width:100%; justify-content:center;"><i class="fas fa-paper-plane"></i> Enviar Mensagem</button>
                    <div id="formResponse" style="margin-top:20px; display:none;"></div>
                </form>
            </div>
            <div class="contact-info-wrapper">
                <div class="contact-info-card">
                    <h3 style="margin-bottom: var(--space-lg);">Informações de <span class="text-accent">contato</span></h3>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <h4>Telefone</h4>
                            <a href="tel:6732537898">(67) 3253-7898</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fab fa-whatsapp"></i></div>
                        <div>
                            <h4>WhatsApp Empresa</h4>
                            <a href="https://wa.me/556799712508">(67) 99712-508</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <h4>WhatsApp Consultoria</h4>
                            <a href="https://wa.me/5567998284808">(67) 99828-4808</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h4>Localização</h4>
                            <p>Rua Francisco José Abrão, 525<br>Campo Grande/MS</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Maps Section -->
        <div class="contact-map" style="margin-top: var(--space-2xl); border-radius: var(--radius-xl); overflow: hidden; border: var(--border); box-shadow: var(--shadow-md);">
            <iframe 
                src="<?php echo GOOGLE_MAPS_LINK; ?>" 
                width="100%" 
                height="400" 
                style="border:0; filter: grayscale(0.8);" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const response = document.getElementById('formResponse');

    response.style.display = 'block';
    response.innerHTML = '<p style="color:var(--accent)">Enviando...</p>';
    fetch(form.action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(new FormData(form)).toString()
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            response.innerHTML = '<p style="color:var(--success)">' + data.message + '</p>';
            form.reset();
        } else {
            response.innerHTML = '<p style="color:var(--error)">' + data.message + '</p>';
        }
    })
    .catch(() => response.innerHTML = '<p style="color:var(--error)">Erro no envio. Tente novamente.</p>');
});
</script>
