<?php // Título e descrição agora são passados pelo Controller ?>

<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Dúvidas</span>
        <h1 class="page-hero-title">Perguntas <span>frequentes</span></h1>
        <div class="accent-line center"></div>
        <p class="page-hero-subtitle">Tudo o que você precisa saber sobre como transformamos seus ambientes com qualidade e confiança.</p>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="faq-container" style="max-width: 800px; margin: 0 auto;">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Qual o prazo médio de entrega?</h3>
                    <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                </div>
                <div class="faq-answer">
                    <p>Nosso prazo médio varia entre 30 a 45 dias úteis, dependendo da complexidade do projeto e dos materiais escolhidos.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Quais materiais vocês utilizam?</h3>
                    <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                </div>
                <div class="faq-answer">
                    <p>Trabalhamos exclusivamente com MDF de alta qualidade das melhores marcas do mercado, além de ferragens premium com amortecimento.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Vocês oferecem garantia?</h3>
                    <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                </div>
                <div class="faq-answer">
                    <p>Nossos móveis seguem as especificações dos fabricantes de ferragens e componentes, com garantia conforme o fornecedor.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Como funciona o processo de orçamento?</h3>
                    <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                </div>
                <div class="faq-answer">
                    <p>O orçamento é gratuito. Realizamos uma visita para medição, desenvolvemos o projeto 3D e apresentamos a proposta detalhada para você.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Vocês atendem apenas Campo Grande?</h3>
                    <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                </div>
                <div class="faq-answer">
                    <p>Atendemos prioritariamente Campo Grande/MS, mas podemos realizar projetos em cidades próximas sob consulta de deslocamento.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <span class="eyebrow">Ainda tem dúvidas?</span>
            <h2 class="cta-title">Fale com a gente</h2>
            <p class="cta-description">Nossa equipe está pronta para te atender e tirar todas as suas dúvidas pelo WhatsApp.</p>
            <a href="https://wa.me/556799712508" target="_blank" class="btn-primary"><i class="fab fa-whatsapp"></i> (67) 99971-2508</a>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.faq-question').forEach(item => {
    item.addEventListener('click', () => {
        const parent = item.parentElement;
        parent.classList.toggle('open');
    });
});
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Qual o prazo médio de entrega?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Nosso prazo médio varia entre 30 a 45 dias úteis, dependendo da complexidade do projeto e dos materiais escolhidos."
            }
        },
        {
            "@type": "Question",
            "name": "Quais materiais vocês utilizam?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Trabalhamos exclusivamente com MDF de alta qualidade das melhores marcas do mercado, além de ferragens premium com amortecimento."
            }
        },
        {
            "@type": "Question",
            "name": "Vocês oferecem garantia?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Nossos móveis seguem as especificações dos fabricantes de ferragens e componentes, com garantia conforme o fornecedor."
            }
        },
        {
            "@type": "Question",
            "name": "Como funciona o processo de orçamento?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "O orçamento é gratuito. Realizamos uma visita para medição, desenvolvemos o projeto 3D e apresentamos a proposta detalhada para você."
            }
        },
        {
            "@type": "Question",
            "name": "Vocês atendem apenas Campo Grande?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Atendemos prioritariamente Campo Grande/MS, mas podemos realizar projetos em cidades próximas sob consulta de deslocamento."
            }
        }
    ]
}
</script>
