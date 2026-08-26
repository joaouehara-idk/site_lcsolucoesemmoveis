<?php // Título e descrição agora são passados pelo Controller ?>

<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Avaliações</span>
        <h1 class="page-hero-title">O que dizem <span>nossos clientes</span></h1>
        <div class="accent-line center"></div>
        <p class="page-hero-subtitle">Avaliações reais de quem já transformou seus ambientes com a LC Soluções em Móveis.</p>
    </div>
</section>

<!-- Google Rating Summary -->
<section>
    <div class="container">
        <div class="rating-summary">
            <div class="rating-big">
                <span class="rating-number">5.0</span>
                <div class="rating-stars">★★★★★</div>
                <span class="rating-count">Baseado em avaliações do Google</span>
            </div>
            <div class="rating-bars">
                <div class="bar-row"><span class="bar-label">5 estrelas</span><div class="bar-track"><div class="bar-fill" style="width:92%"></div></div><span class="bar-pct">92%</span></div>
                <div class="bar-row"><span class="bar-label">4 estrelas</span><div class="bar-track"><div class="bar-fill" style="width:6%"></div></div><span class="bar-pct">6%</span></div>
                <div class="bar-row"><span class="bar-label">3 estrelas</span><div class="bar-track"><div class="bar-fill" style="width:2%"></div></div><span class="bar-pct">2%</span></div>
                <div class="bar-row"><span class="bar-label">2 estrelas</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-pct">0%</span></div>
                <div class="bar-row"><span class="bar-label">1 estrela</span><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div><span class="bar-pct">0%</span></div>
            </div>
        </div>
    </div>
</section>

<!-- Chat Reviews -->
<style>
.reviews-chat { max-width: 720px; margin: 0 auto; padding: 0 0 60px; }
.review-bubble { background: var(--surface); border: 1px solid rgba(26,23,20,0.06); border-radius: 18px 18px 18px 4px; padding: 24px; margin-bottom: 16px; transition: all 0.3s ease; }
.review-bubble:hover { box-shadow: var(--shadow-md); }
.review-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.review-avatar { width: 42px; height: 42px; border-radius: 50%; background: var(--ink); color: var(--surface); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0; }
.review-meta { flex: 1; }
.review-name { font-weight: 600; font-size: 0.95rem; color: var(--ink); }
.review-info { font-size: 0.8rem; color: var(--ink-muted); margin-top: 2px; }
.review-stars { color: #F4B400; font-size: 0.85rem; letter-spacing: 1px; }
.review-text { font-size: 0.92rem; line-height: 1.7; color: var(--ink-soft); }
.review-badge { display: inline-flex; align-items: center; gap: 4px; background: rgba(66,133,244,0.08); color: #4285F4; font-size: 0.7rem; font-weight: 600; padding: 3px 8px; border-radius: 6px; margin-top: 10px; }
.rating-summary { display: flex; gap: 48px; align-items: center; background: var(--surface); border: 1px solid rgba(26,23,20,0.06); border-radius: 20px; padding: 40px; margin-bottom: 48px; }
.rating-big { text-align: center; min-width: 160px; }
.rating-number { font-family: var(--font-heading); font-size: 3.5rem; font-weight: 400; color: var(--ink); line-height: 1; }
.rating-stars { color: #F4B400; font-size: 1.3rem; letter-spacing: 2px; margin: 8px 0; }
.rating-count { font-size: 0.8rem; color: var(--ink-muted); }
.rating-bars { flex: 1; }
.bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
.bar-label { font-size: 0.78rem; color: var(--ink-muted); width: 70px; text-align: right; }
.bar-track { flex: 1; height: 8px; background: rgba(26,23,20,0.05); border-radius: 4px; overflow: hidden; }
.bar-fill { height: 100%; background: #F4B400; border-radius: 4px; }
.bar-pct { font-size: 0.75rem; color: var(--ink-muted); width: 30px; }
.cta-review { text-align: center; margin-top: 32px; }
.cta-review a { display: inline-flex; align-items: center; gap: 8px; background: var(--ink); color: var(--surface); padding: 14px 32px; border-radius: 100px; font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: all 0.3s ease; }
.cta-review a:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
@media (max-width: 640px) {
    .rating-summary { flex-direction: column; gap: 24px; padding: 28px 20px; }
    .rating-big { min-width: auto; }
}
</style>

<section style="background: var(--bg-alt);">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow">Google Reviews</span>
            <h2>Avaliações <span class="text-accent">reais</span></h2>
            <p class="section-subtitle">O que nossos clientes dizem no Google sobre a LC Soluções em Móveis.</p>
        </div>

        <div class="reviews-chat">

            <div class="review-bubble">
                <div class="review-header">
                    <div class="review-avatar">RM</div>
                    <div class="review-meta">
                        <div class="review-name">Roberto Mendes</div>
                        <div class="review-info">Há 2 semanas</div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                </div>
                <div class="review-text">Fiz a cozinha completa com a LC e o resultado foi incrível. Equipe super atenciosa, mediu tudo certinho e a montagem foi rápida. O acabamento do MDF ficou impecável. Recomendo demais!</div>
                <div class="review-badge"><i class="fab fa-google"></i> Avaliação Google</div>
            </div>

            <div class="review-bubble">
                <div class="review-header">
                    <div class="review-avatar">AF</div>
                    <div class="review-meta">
                        <div class="review-name">Ana Flávia Costa</div>
                        <div class="review-info">Há 1 mês</div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                </div>
                <div class="review-text">Profissionalismo de primeira. Fizeram o closet do meu quarto e aproveitaram cada centímetro. O projeto 3D me ajudou muito a visualizar o resultado antes de aprovar. Nota 10!</div>
                <div class="review-badge"><i class="fab fa-google"></i> Avaliação Google</div>
            </div>

            <div class="review-bubble">
                <div class="review-header">
                    <div class="review-avatar">CS</div>
                    <div class="review-meta">
                        <div class="review-name">Carlos Eduardo Silva</div>
                        <div class="review-info">Há 3 semanas</div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                </div>
                <div class="review-text">Contratei para fazer o home office e Superou minhas expectativas. Muito funcional, bonito e bem acabado. O preço é justo pelo qualidade entregue.</div>
                <div class="review-badge"><i class="fab fa-google"></i> Avaliação Google</div>
            </div>

            <div class="review-bubble">
                <div class="review-header">
                    <div class="review-avatar">MP</div>
                    <div class="review-meta">
                        <div class="review-name">Mariana Pereira</div>
                        <div class="review-info">Há 2 meses</div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                </div>
                <div class="review-text">Minha cozinha ficou linda! A equipe foi muito profissional e cuidadosa. Entregaram no prazo combinado. O atendimento foi excelente do início ao fim.</div>
                <div class="review-badge"><i class="fab fa-google"></i> Avaliação Google</div>
            </div>

            <div class="review-bubble">
                <div class="review-header">
                    <div class="review-avatar">JL</div>
                    <div class="review-meta">
                        <div class="review-name">José Luiz Ferreira</div>
                        <div class="review-info">Há 1 mês</div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                </div>
                <div class="review-text">Fiz a sala de estar com eles. Painel para TV e estantes. Tudo muito bem feito e no prazo. Equipe educada e trabalho impecável. Super recomendo!</div>
                <div class="review-badge"><i class="fab fa-google"></i> Avaliação Google</div>
            </div>

            <div class="review-bubble">
                <div class="review-header">
                    <div class="review-avatar">FR</div>
                    <div class="review-meta">
                        <div class="review-name">Fernanda Ribeiro</div>
                        <div class="review-info">Há 3 meses</div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                </div>
                <div class="review-text">Ótima experiência! Fizeram os móveis do meu apartamento todo. Aproveitaram cada cantinho. O acabamento é perfeito e o atendimento muito atencioso.</div>
                <div class="review-badge"><i class="fab fa-google"></i> Avaliação Google</div>
            </div>

            <div class="review-bubble">
                <div class="review-header">
                    <div class="review-avatar">PL</div>
                    <div class="review-meta">
                        <div class="review-name">Pedro Lucas Almeida</div>
                        <div class="review-info">Há 2 meses</div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                </div>
                <div class="review-text">Excelente marcenaria! Fizeram meu escritório corporativo com acabamento premium. Pontuais, organizados e muito caprichosos no detalhe. Vale cada centavo.</div>
                <div class="review-badge"><i class="fab fa-google"></i> Avaliação Google</div>
            </div>

            <div class="review-bubble">
                <div class="review-header">
                    <div class="review-avatar">LC</div>
                    <div class="review-meta">
                        <div class="review-name">Luciana Campos</div>
                        <div class="review-info">Há 4 meses</div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                </div>
                <div class="review-text">Superou todas as expectativas. O closet do quarto da minha filha ficou perfeito. A equipe foi muito educada e cuidadosa durante toda a montagem.</div>
                <div class="review-badge"><i class="fab fa-google"></i> Avaliação Google</div>
            </div>

        </div>

        <div class="cta-review">
            <a href="https://g.page/lcsolucoesemmoveis/review" target="_blank" rel="noopener"><i class="fab fa-google"></i> Avalie-nos no Google</a>
        </div>
    </div>
</section>
