<?php // Dados do serviço passados pelo ServicesController (variável $servico) ?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/services.css">

<section class="services-tech-page">
    <!-- Hero do Serviço -->
    <section class="tech-service-hero">
        <div class="container">
            <div class="tech-service-hero-grid">
                <div>
                    <div class="tech-hero-label"><?php echo htmlspecialchars($servico['eyebrow']); ?></div>
                    <h1><?php echo $servico['h1']; ?></h1>
                    <p class="tech-service-desc"><?php echo htmlspecialchars($servico['subtitle']); ?></p>
                    <div class="tech-service-meta">
                        <span><i class="fas fa-ruler-combined"></i> Medição inclusa</span>
                        <span><i class="fas fa-cube"></i> Projeto 3D</span>
                        <span><i class="fas fa-shield-alt"></i> Garantia 5 anos</span>
                    </div>
                    <div class="tech-hero-actions">
                        <a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20or%C3%A7amento%20de%20<?php echo $servico['wa_message']; ?>" target="_blank" class="btn-tech btn-tech-primary">
                            <i class="fab fa-whatsapp"></i> Pedir orçamento gratuito
                        </a>
                        <a href="<?php echo BASE_URL; ?>/servicos" class="btn-tech btn-tech-secondary">
                            <i class="fas fa-th-large"></i> Todos os serviços
                        </a>
                    </div>
                </div>
                <div class="tech-service-image">
                    <img src="<?php echo htmlspecialchars($servico['imagem']); ?>" alt="<?php echo htmlspecialchars($servico['titulo']); ?>" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Especificações Técnicas -->
    <section class="tech-specs">
        <div class="container">
            <div class="tech-section-header">
                <div class="tech-section-label">Especificações Técnicas</div>
                <h2 class="tech-section-title">Como este serviço é executado</h2>
                <p class="tech-section-subtitle">
                    Cada projeto segue uma especificação técnica definida durante o briefing.
                    Aqui está o padrão para este tipo de serviço — ajustável conforme o seu espaço.
                </p>
            </div>
            <div class="tech-specs-grid">
                <div class="tech-spec-card">
                    <div class="tech-spec-header">
                        <div class="tech-spec-icon"><i class="fas fa-drafting-compass"></i></div>
                        <h3 class="tech-spec-title">Projeto e medição</h3>
                    </div>
                    <ul class="tech-spec-list">
                        <li><strong>Medição:</strong> trena laser + ficha técnica padrão</li>
                        <li><strong>Prazo do projeto:</strong> 3 a 5 dias úteis</li>
                        <li><strong>Alterações:</strong> ilimitadas até aprovação</li>
                        <li><strong>Entregas:</strong> projeto 3D + executivo + orçamento</li>
                        <li><strong>Custo:</strong> gratuito e sem compromisso</li>
                    </ul>
                </div>
                <div class="tech-spec-card">
                    <div class="tech-spec-header">
                        <div class="tech-spec-icon"><i class="fas fa-layer-group"></i></div>
                        <h3 class="tech-spec-title">Materiais utilizados</h3>
                    </div>
                    <ul class="tech-spec-list">
                        <?php foreach ($servico['materiais'] as $mat): ?>
                            <li><strong><?php echo htmlspecialchars($mat['nome']); ?></strong> <?php echo htmlspecialchars($mat['detalhe']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="tech-spec-card">
                    <div class="tech-spec-header">
                        <div class="tech-spec-icon"><i class="fas fa-cog"></i></div>
                        <h3 class="tech-spec-title">Processo de fabricação</h3>
                    </div>
                    <ul class="tech-spec-list">
                        <?php foreach ($servico['processo'] as $etapa): ?>
                            <li><?php echo htmlspecialchars($etapa); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="tech-spec-card">
                    <div class="tech-spec-header">
                        <div class="tech-spec-icon"><i class="fas fa-clipboard-check"></i></div>
                        <h3 class="tech-spec-title">Garantia e prazos</h3>
                    </div>
                    <ul class="tech-spec-list">
                        <li><strong>Garantia estrutural:</strong> 5 anos</li>
                        <li><strong>Ferragens:</strong> garantia do fabricante</li>
                        <li><strong>Acabamento:</strong> 2 anos</li>
                        <li><strong>Prazo médio:</strong> <?php echo htmlspecialchars($servico['prazo'] ?? '30 a 45 dias'); ?></li>
                        <li><strong>Instalação:</strong> incluída no serviço</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Aplicações -->
    <section class="tech-applications">
        <div class="container">
            <div class="tech-section-header">
                <div class="tech-section-label">Onde Aplicamos</div>
                <h2 class="tech-section-title">Ambientes e utilizações</h2>
            </div>
            <div class="tech-app-grid">
                <?php foreach ($servico['aplicacoes'] as $app): ?>
                    <div class="tech-app-card">
                        <div class="tech-app-icon"><i class="fas <?php echo htmlspecialchars($app['icone']); ?>"></i></div>
                        <h3 class="tech-app-title"><?php echo htmlspecialchars($app['titulo']); ?></h3>
                        <p class="tech-app-desc"><?php echo htmlspecialchars($app['descricao']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Benefícios -->
    <section class="tech-details" style="background: #FAFAFA;">
        <div class="container">
            <div class="tech-section-header">
                <div class="tech-section-label">Vantagens Técnicas</div>
                <h2 class="tech-section-title">O que este serviço oferece</h2>
            </div>
            <div class="tech-details-grid">
                <div>
                    <div class="tech-detail-block">
                        <div class="tech-detail-header">
                            <div class="tech-detail-icon"><i class="fas fa-check"></i></div>
                            <h3 class="tech-detail-title"><?php echo htmlspecialchars($servico['beneficios'][0] ?? 'Otimização de espaço'); ?></h3>
                        </div>
                        <p class="tech-detail-text">
                            Projetado para aproveitar cada centímetro disponível, incluindo cantos, alturas e áreas
                            que geralmente ficam subutilizadas. A otimização é feita com base no uso real do ambiente,
                            não apenas na metragem.
                        </p>
                    </div>
                </div>
                <div>
                    <div class="tech-detail-block">
                        <div class="tech-detail-header">
                            <div class="tech-detail-icon"><i class="fas fa-check"></i></div>
                            <h3 class="tech-detail-title"><?php echo htmlspecialchars($servico['beneficios'][1] ?? 'Projeto antes da fabricação'); ?></h3>
                        </div>
                        <p class="tech-detail-text">
                            Você aprova o projeto 3D e o executivo antes de qualquer peça ser cortada.
                            Isso evita surpresas e garante que o resultado final corresponda ao que foi combinado.
                            Alterações são feitas na fase de projeto, não na fase de produção.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="tech-faq">
        <div class="container">
            <div class="tech-section-header" style="text-align: center; max-width: 600px; margin: 0 auto 48px;">
                <div class="tech-section-label" style="justify-content: center;">Dúvidas Frequentes</div>
                <h2 class="tech-section-title">Perguntas sobre este serviço</h2>
            </div>
            <div class="tech-faq-list">
                <?php foreach ($servico['faq'] as $item): ?>
                    <div class="tech-faq-item">
                        <button class="tech-faq-question">
                            <?php echo htmlspecialchars($item['p']); ?>
                            <span class="tech-faq-toggle"><i class="fas fa-chevron-down"></i></span>
                        </button>
                        <div class="tech-faq-answer">
                            <p><?php echo htmlspecialchars($item['r']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="tech-faq-item">
                    <button class="tech-faq-question">
                        Ainda tenho dúvidas técnicas específicas
                        <span class="tech-faq-toggle"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="tech-faq-answer">
                        <p>
                            Cada projeto tem suas particularidades: dimensões do ambiente, tipo de uso,
                            preferência de material, restrições de obra. Se a sua dúvida não está aqui,
                            converse diretamente com o nosso especialista pelo WhatsApp.
                            Ele vai orientar sobre a viabilidade técnica e as opções mais adequadas para o seu caso.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="tech-cta">
        <div class="container">
            <div class="tech-cta-box">
                <div class="tech-cta-label">Próximo passo</div>
                <h2 class="tech-cta-title">Vamos fazer o seu projeto</h2>
                <p class="tech-cta-desc">
                    Chame a gente no WhatsApp. A visita, o projeto 3D e o orçamento são gratuitos.
                </p>
                <a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20or%C3%A7amento%20de%20<?php echo $servico['wa_message']; ?>" target="_blank" class="btn-tech btn-tech-primary">
                    <i class="fab fa-whatsapp"></i> Falar com especialista
                </a>
            </div>
        </div>
    </section>
</section>

<script>
document.querySelectorAll('.tech-faq-question').forEach(btn => {
    btn.addEventListener('click', () => {
        const item = btn.parentElement;
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.tech-faq-item').forEach(i => i.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
    });
});
</script>
