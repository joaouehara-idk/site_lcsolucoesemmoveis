<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- AdSense Code -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5253082939210672" crossorigin="anonymous"></script>
    <meta name="google-adsense-account" content="ca-pub-5253082939210672">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antes e depois: transformação incrível de ambientes | LC Soluções</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #1A1714;
            --gold-light: #1A1714;
            --gold-dark: #1A1714;
            --dark: #0a0a0a;
            --darker: #000000;
            --dark-light: #1a1a1a;
            --text: #e2e2e2;
            --text-light: #a0a0a0;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--darker);
            color: var(--text);
            line-height: 1.7;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Hero */
        .article-hero {
            background: linear-gradient(145deg, 
                rgba(10,10,10,0.95) 0%, 
                rgba(26,26,26,0.85) 50%,
                rgba(197,162,83,0.1) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100"><polygon points="1000,100 1000,0 0,100" fill="%23C5A253" opacity="0.03"/></svg>');
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(197, 162, 83, 0.2);
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .article-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700;
            text-align: center;
            color: var(--white);
            margin-bottom: 20px;
            position: relative;
        }

        .article-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            border-radius: 10px;
        }

        .article-meta {
            text-align: center;
            color: var(--text-light);
            font-weight: 300;
            font-size: 1.1rem;
            margin-top: 40px;
        }

        .meta-divider {
            color: var(--gold);
            margin: 0 15px;
        }

        /* Conteúdo do Artigo */
        .article-content {
            background: linear-gradient(145deg, rgba(26,26,26,0.6), rgba(10,10,10,0.8));
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 60px 50px;
            margin: -40px auto 60px;
            position: relative;
            border: 1px solid rgba(197, 162, 83, 0.1);
            box-shadow: 
                0 10px 30px rgba(0,0,0,0.3),
                0 0 0 1px rgba(197, 162, 83, 0.05),
                inset 0 1px 0 rgba(255,255,255,0.1);
        }

        .article-intro {
            font-size: 1.3rem;
            line-height: 1.8;
            color: var(--text);
            margin-bottom: 50px;
            padding: 30px;
            background: rgba(197, 162, 83, 0.05);
            border-left: 4px solid var(--gold);
            border-radius: 0 15px 15px 0;
        }

        .content-section {
            margin-bottom: 50px;
            padding: 30px;
            background: linear-gradient(145deg, rgba(26,26,26,0.4), rgba(10,10,10,0.6));
            border-radius: 20px;
            border: 1px solid rgba(197, 162, 83, 0.08);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .content-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(197,162,83,0.02), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .content-section:hover {
            transform: translateY(-5px);
            border-color: rgba(197, 162, 83, 0.2);
            box-shadow: 
                0 15px 40px rgba(197, 162, 83, 0.1),
                0 0 0 1px rgba(197, 162, 83, 0.1);
        }

        .content-section:hover::before {
            opacity: 1;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--gold-light);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .section-title::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 30px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border-radius: 4px;
            margin-right: 15px;
        }

        .section-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text);
        }

        /* Listas Estilizadas */
        .styled-list {
            list-style: none;
            margin: 20px 0;
        }

        .styled-list li {
            position: relative;
            padding: 12px 0 12px 40px;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(197, 162, 83, 0.1);
        }

        .styled-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 25px;
            height: 25px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: var(--dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .styled-list strong {
            color: var(--gold-light);
            font-weight: 600;
        }

        /* Blockquote Estilizado */
        .blockquote {
            background: linear-gradient(145deg, 
                rgba(197, 162, 83, 0.1), 
                rgba(197, 162, 83, 0.05));
            border-left: 4px solid var(--gold);
            padding: 30px;
            margin: 40px 0;
            border-radius: 0 15px 15px 0;
            position: relative;
            font-style: italic;
        }

        .blockquote::before {
            content: '"';
            position: absolute;
            top: -10px;
            left: 20px;
            font-size: 4rem;
            color: var(--gold);
            opacity: 0.3;
            font-family: serif;
            line-height: 1;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(145deg, 
                rgba(197, 162, 83, 0.15), 
                rgba(197, 162, 83, 0.08));
            border: 1px solid rgba(197, 162, 83, 0.2);
            padding: 50px;
            border-radius: 20px;
            margin-top: 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.05"><polygon points="50,0 100,50 50,100 0,50" fill="%23C5A253"/></svg>');
            background-size: 50px 50px;
            animation: float 20s linear infinite;
        }

        .cta-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--gold);
            margin-bottom: 20px;
            position: relative;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: var(--dark);
            font-weight: 600;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 5px 15px rgba(197, 162, 83, 0.3),
                0 0 0 1px rgba(197, 162, 83, 0.1);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 8px 25px rgba(197, 162, 83, 0.4),
                0 0 0 1px rgba(197, 162, 83, 0.2);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        /* Footer */
        .article-footer {
            background: linear-gradient(145deg, var(--dark), var(--darker));
            border-top: 1px solid rgba(197, 162, 83, 0.1);
            padding: 40px 0;
            text-align: center;
            color: var(--text-light);
            font-weight: 300;
        }

        /* Partículas Douradas */
        .gold-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--gold);
            border-radius: 50%;
            opacity: 0.3;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .article-content {
                padding: 40px 25px;
                margin: -20px auto 40px;
                border-radius: 20px;
            }

            .content-section {
                padding: 25px 20px;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .article-intro {
                font-size: 1.1rem;
                padding: 20px;
            }

            .cta-section {
                padding: 30px 20px;
            }
        }

        @media (max-width: 480px) {
            .article-hero {
                padding: 80px 0 60px;
            }

            .article-content {
                padding: 30px 20px;
                border-radius: 15px;
            }

            .content-section {
                padding: 20px 15px;
            }

            .styled-list li {
                padding-left: 35px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Hero -->
    <header class="article-hero">
        <div class="container">
            <h1 class="article-title">
                Antes e depois: transformação incrível de ambientes
            </h1>
            <div class="article-meta">
                <span>24 de Setembro, 2025</span>
                <span class="meta-divider">•</span>
                <span>Categoria: Inspiração</span>
                <span class="meta-divider">•</span>
                <span>LC Soluções em Móveis</span>
            </div>
        </div>
        
        <!-- Partículas decorativas -->
        <div class="gold-particle" style="top: 20%; left: 10%; animation-delay: 0s;"></div>
        <div class="gold-particle" style="top: 60%; left: 85%; animation-delay: 2s;"></div>
        <div class="gold-particle" style="top: 40%; left: 70%; animation-delay: 4s;"></div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="container">
        <article class="article-content">
            <p class="article-intro">
                Quem nunca se surpreendeu com um <strong>antes e depois</strong> bem feito? Com móveis planejados, é possível transformar ambientes simples em espaços modernos, funcionais e cheios de estilo. Essa transformação vai além da estética: é uma mudança na experiência de viver o ambiente.
            </p>

            <section class="content-section">
                <h2 class="section-title">Antes: limitações e desafios</h2>
                <p class="section-content">
                    Ambientes sem planejamento apresentam problemas comuns como:
                </p>
                <ul class="styled-list">
                    <li>Falta de espaço para armazenamento;</li>
                    <li>Excesso de móveis soltos que atrapalham a circulação;</li>
                    <li>Decoração despadronizada e pouco funcional;</li>
                    <li>Sensação de desorganização e desconforto.</li>
                </ul>
            </section>

            <section class="content-section">
                <h2 class="section-title">Depois: ambientes renovados</h2>
                <p class="section-content">
                    Com móveis planejados, o cenário muda completamente. O ambiente passa a ter:
                </p>
                <ul class="styled-list">
                    <li><strong>Layout otimizado</strong>, aproveitando cada centímetro;</li>
                    <li><strong>Integração estética</strong> entre móveis e decoração;</li>
                    <li><strong>Funcionalidade</strong> sem abrir mão da beleza;</li>
                    <li><strong>Valorização do imóvel</strong> e maior conforto no dia a dia.</li>
                </ul>
            </section>

            <section class="content-section">
                <h2 class="section-title">Exemplos de transformação incrível</h2>
                <p class="section-content">
                    Algumas mudanças impressionam pelo contraste entre o antes e o depois:
                </p>
                <ul class="styled-list">
                    <li><strong>Cozinhas pequenas</strong> que se tornam práticas e sofisticadas;</li>
                    <li><strong>Salas de estar</strong> que ganham amplitude com painéis e iluminação embutida;</li>
                    <li><strong>Quartos</strong> que passam de desorganizados para aconchegantes com armários sob medida.</li>
                </ul>
            </section>

            <blockquote class="blockquote">
                "O antes e depois de um projeto planejado mostra que, com criatividade e técnica, qualquer espaço pode ser transformado."
            </blockquote>

            <section class="content-section">
                <h2 class="section-title">Conclusão</h2>
                <p class="section-content">
                    Os móveis planejados oferecem muito mais do que beleza: eles trazem <strong>funcionalidade, modernidade e conforto</strong>. Se você quer viver essa transformação em sua casa, conte com a <strong>LC Soluções em Móveis</strong> para criar projetos exclusivos que revelam todo o potencial do seu ambiente.
                </p>
            </section>

            <div class="cta-section">
                <h3 class="cta-title">Pronto para transformar seu ambiente?</h3>
                <p class="section-content" style="margin-bottom: 25px; position: relative;">
                    Entre em contato e descubra como podemos criar um projeto exclusivo para seu espaço
                </p>
                <a href="/contato" class="btn-primary">Transforme seu ambiente agora</a>
            </div>
        </article>
    </main>

    <!-- Footer -->
    <footer class="article-footer">
        <div class="container">
            <p>© <span id="year"></span> LC Soluções em Móveis · Todos os direitos reservados</p>
            <p style="margin-top: 10px; font-size: 0.9rem; opacity: 0.7;">
                Design sofisticado para ambientes que inspiram
            </p>
        </div>
    </footer>

    <script>
        // Ano atual no footer
        document.getElementById('year').textContent = new Date().getFullYear();

        // Efeito de entrada suave para as seções
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.content-section');
            
            sections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    section.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, 200 * index);
            });
        });
    </script>
</body>
</html>