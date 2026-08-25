<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- AdSense Code -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5253082939210672" crossorigin="anonymous"></script>
    <meta name="google-adsense-account" content="ca-pub-5253082939210672">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antes e depois: como os móveis planejados transformam um ambiente | LC Soluções em Móveis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Variáveis CSS */
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

        /* Reset e configurações básicas */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--darker);
            color: var(--text);
            line-height: 1.7;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, 
                rgba(10, 10, 10, 0.95) 0%, 
                rgba(0, 0, 0, 0.9) 100%);
            backdrop-filter: blur(10px);
            position: relative;
            padding: 100px 0 80px;
            border-bottom: 1px solid rgba(197, 162, 83, 0.1);
            overflow: hidden;
            text-align: center;
        }

        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 25% 25%, rgba(197, 162, 83, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(197, 162, 83, 0.06) 0%, transparent 50%);
            z-index: -1;
        }

        .article-title {
            font-size: 3.2rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .article-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 3px;
            background: linear-gradient(90deg, 
                transparent, 
                var(--gold), 
                transparent);
            border-radius: 2px;
        }

        .article-subtitle {
            font-size: 1.3rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }

        .article-meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .meta-divider {
            width: 4px;
            height: 4px;
            background: var(--gold);
            border-radius: 50%;
        }

        /* Main Content */
        .main-content {
            padding: 80px 0;
            position: relative;
        }

        .content-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Introduction */
        .introduction {
            text-align: center;
            margin-bottom: 80px;
            padding: 0 20px;
        }

        .intro-text {
            font-size: 1.3rem;
            color: var(--text);
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .highlight {
            color: var(--gold-light);
            font-weight: 600;
        }

        /* Comparison Section */
        .comparison-section {
            margin: 60px 0;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--white);
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--gold);
            border-radius: 2px;
        }

        .comparison-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .comparison-card {
            background: linear-gradient(145deg, 
                rgba(26, 26, 26, 0.9), 
                rgba(10, 10, 10, 0.9));
            border-radius: 20px;
            padding: 40px 30px;
            border: 1px solid rgba(197, 162, 83, 0.1);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .comparison-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(197, 162, 83, 0.05) 0%, 
                transparent 50%);
            opacity: 0;
            transition: opacity 0.6s ease;
        }

        .comparison-card:hover {
            transform: translateY(-10px);
            border-color: rgba(197, 162, 83, 0.3);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(197, 162, 83, 0.2),
                0 0 25px rgba(197, 162, 83, 0.1);
        }

        .comparison-card:hover::before {
            opacity: 1;
        }

        .comparison-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-before {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: var(--white);
        }

        .badge-after {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: var(--dark);
        }

        .comparison-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px auto 25px;
            font-size: 1.8rem;
            transition: all 0.4s ease;
        }

        .icon-before {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }

        .icon-after {
            background: rgba(197, 162, 83, 0.1);
            color: var(--gold);
        }

        .comparison-card:hover .comparison-icon {
            transform: scale(1.1);
        }

        .comparison-card:hover .icon-after {
            background: rgba(197, 162, 83, 0.2);
            color: var(--gold-light);
        }

        .comparison-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 20px;
            position: relative;
        }

        .comparison-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 2px;
            border-radius: 1px;
            transition: width 0.4s ease;
        }

        .title-before::after {
            background: #ff6b6b;
        }

        .title-after::after {
            background: var(--gold);
        }

        .comparison-card:hover .comparison-title::after {
            width: 60px;
        }

        .comparison-description {
            color: var(--text-light);
            line-height: 1.7;
            text-align: center;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .comparison-features {
            list-style: none;
            margin-top: 20px;
        }

        .comparison-features li {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            color: var(--text-light);
            font-size: 0.95rem;
            padding: 8px 0;
        }

        .features-before li::before {
            content: '✗';
            color: #ff6b6b;
            font-weight: bold;
            margin-right: 12px;
            font-size: 1.1rem;
            min-width: 20px;
        }

        .features-after li::before {
            content: '✓';
            color: var(--gold);
            font-weight: bold;
            margin-right: 12px;
            font-size: 1.1rem;
            min-width: 20px;
        }

        /* Examples Section */
        .examples-section {
            margin: 80px 0;
        }

        .examples-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }

        .example-card {
            background: linear-gradient(145deg, 
                rgba(26, 26, 26, 0.8), 
                rgba(10, 10, 10, 0.8));
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(197, 162, 83, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .example-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gold);
        }

        .example-card:hover {
            border-color: rgba(197, 162, 83, 0.3);
            transform: translateY(-5px);
        }

        .example-icon {
            font-size: 2.5rem;
            color: var(--gold);
            margin-bottom: 20px;
        }

        .example-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 15px;
        }

        .example-description {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Quote Section */
        .quote-section {
            margin: 80px 0;
            position: relative;
        }

        .blockquote {
            position: relative;
            padding: 50px 50px 50px 100px;
            background: linear-gradient(145deg, 
                rgba(26, 26, 26, 0.8), 
                rgba(10, 10, 10, 0.8));
            border-radius: 20px;
            border: 1px solid rgba(197, 162, 83, 0.2);
            font-style: italic;
            color: var(--text-light);
            font-size: 1.3rem;
            line-height: 1.6;
            text-align: center;
        }

        .blockquote::before {
            content: '"';
            position: absolute;
            left: 40px;
            top: 30px;
            font-size: 5rem;
            color: var(--gold);
            opacity: 0.5;
            font-family: serif;
            line-height: 1;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, 
                rgba(197, 162, 83, 0.1) 0%, 
                rgba(180, 140, 69, 0.08) 50%, 
                rgba(197, 162, 83, 0.1) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(197, 162, 83, 0.2);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            margin: 80px 0 40px;
            position: relative;
            overflow: hidden;
        }

        .cta-title {
            font-size: 2.2rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 20px;
        }

        .cta-description {
            color: var(--text-light);
            font-size: 1.1rem;
            margin-bottom: 35px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .cta-button {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: var(--dark);
            padding: 18px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            box-shadow: 
                0 8px 25px rgba(197, 162, 83, 0.3),
                0 0 0 1px rgba(197, 162, 83, 0.2);
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.3), 
                transparent);
            transition: left 0.8s ease;
        }

        .cta-button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 
                0 15px 35px rgba(197, 162, 83, 0.4),
                0 0 0 1px rgba(197, 162, 83, 0.3),
                0 0 25px rgba(197, 162, 83, 0.2);
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .cta-button i {
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .cta-button:hover i {
            transform: translateX(5px);
        }

        /* Footer */
        footer {
            background: var(--dark);
            padding: 40px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: var(--text-light);
        }

        /* Efeitos de Partículas Douradas */
        .gold-particle {
            position: absolute;
            background: var(--gold);
            border-radius: 50%;
            opacity: 0;
            animation: floatParticle 8s infinite linear;
            z-index: -1;
        }

        .particle-1 {
            width: 6px;
            height: 6px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .particle-2 {
            width: 4px;
            height: 4px;
            top: 60%;
            left: 85%;
            animation-delay: 1s;
        }

        .particle-3 {
            width: 8px;
            height: 8px;
            top: 80%;
            left: 15%;
            animation-delay: 2s;
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.7;
            }
            90% {
                opacity: 0.3;
            }
            100% {
                transform: translateY(-100px) translateX(50px) rotate(180deg);
                opacity: 0;
            }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .article-title {
                font-size: 2.5rem;
            }
            
            .article-subtitle {
                font-size: 1.1rem;
            }
            
            .comparison-grid {
                grid-template-columns: 1fr;
            }
            
            .comparison-card {
                padding: 30px 20px;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .blockquote {
                padding: 40px 30px 40px 70px;
                font-size: 1.1rem;
            }
            
            .blockquote::before {
                left: 25px;
                top: 25px;
                font-size: 4rem;
            }
            
            .cta-section {
                padding: 50px 25px;
            }
            
            .cta-title {
                font-size: 1.8rem;
            }
            
            .examples-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .article-title {
                font-size: 2rem;
            }
            
            .container {
                padding: 0 15px;
            }
            
            .main-content {
                padding: 60px 0;
            }
            
            .cta-button {
                padding: 16px 35px;
                font-size: 1rem;
            }
            
            .article-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .meta-divider {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Partículas Douradas -->
    <div class="gold-particle particle-1"></div>
    <div class="gold-particle particle-2"></div>
    <div class="gold-particle particle-3"></div>

    <!-- Header -->
    <header>
        <div class="container">
            <h1 class="article-title">Antes e depois: como os móveis planejados transformam um ambiente</h1>
            <p class="article-subtitle">Descubra o poder da transformação visual e funcional através de projetos sob medida</p>
            <div class="article-meta">
                <span><i class="fas fa-calendar-alt"></i> 23/09/2025</span>
                <div class="meta-divider"></div>
                <span><i class="fas fa-tag"></i> Transformação</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="content-wrapper">
                <!-- Introdução -->
                <section class="introduction">
                    <p class="intro-text">
                        Os móveis planejados têm o poder de mudar completamente a percepção de um ambiente. Mais do que estética, eles trazem <span class="highlight">funcionalidade, organização e conforto</span>, transformando espaços comuns em áreas modernas e acolhedoras. Veja como o contraste entre "antes e depois" revela o impacto transformador dessa escolha inteligente.
                    </p>
                </section>

                <!-- Comparação Antes/Depois -->
                <section class="comparison-section">
                    <h2 class="section-title">A Transformação em Detalhes</h2>
                    
                    <div class="comparison-grid">
                        <!-- Antes -->
                        <div class="comparison-card">
                            <div class="comparison-badge badge-before">Antes</div>
                            <div class="comparison-icon icon-before">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <h3 class="comparison-title title-before">Ambientes sem Planejamento</h3>
                            <p class="comparison-description">
                                Antes do projeto, os ambientes costumam apresentar problemas funcionais e estéticos que comprometem o conforto e a praticidade.
                            </p>
                            <ul class="comparison-features features-before">
                                <li>Falta de espaço para armazenamento adequado</li>
                                <li>Circulação comprometida por móveis inadequados</li>
                                <li>Visual poluído e desorganizado</li>
                                <li>Ausência de harmonia entre os elementos</li>
                                <li>Espaços mal aproveitados e subutilizados</li>
                            </ul>
                        </div>

                        <!-- Depois -->
                        <div class="comparison-card">
                            <div class="comparison-badge badge-after">Depois</div>
                            <div class="comparison-icon icon-after">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 class="comparison-title title-after">Transformação com Planejados</h3>
                            <p class="comparison-description">
                                Com o projeto planejado, os ambientes ganham inteligência espacial, sofisticação e funcionalidade integrada.
                            </p>
                            <ul class="comparison-features features-after">
                                <li>Aproveitamento inteligente de cada centímetro</li>
                                <li>Integração perfeita entre móveis e decoração</li>
                                <li>Modernidade e sofisticação em cada detalhe</li>
                                <li>Funcionalidade aliada à beleza estética</li>
                                <li>Organização que facilita o dia a dia</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Exemplos de Transformação -->
                <section class="examples-section">
                    <h2 class="section-title">Exemplos de Transformação Impressionante</h2>
                    
                    <div class="examples-grid">
                        <div class="example-card">
                            <div class="example-icon">
                                <i class="fas fa-kitchen-set"></i>
                            </div>
                            <h3 class="example-title">Cozinhas Pequenas</h3>
                            <p class="example-description">
                                Que ganham mais armários sem perder espaço de circulação, com soluções inteligentes de armazenamento e bancadas otimizadas.
                            </p>
                        </div>

                        <div class="example-card">
                            <div class="example-icon">
                                <i class="fas fa-tv"></i>
                            </div>
                            <h3 class="example-title">Salas de TV</h3>
                            <p class="example-description">
                                Que se transformam com painéis sob medida, iluminação indireta e organização discreta de equipamentos eletrônicos.
                            </p>
                        </div>

                        <div class="example-card">
                            <div class="example-icon">
                                <i class="fas fa-bed"></i>
                            </div>
                            <h3 class="example-title">Quartos</h3>
                            <p class="example-description">
                                Que se tornam mais aconchegantes e organizados com armários embutidos, cabeceiras personalizadas e closet integrado.
                            </p>
                        </div>

                        <div class="example-card">
                            <div class="example-icon">
                                <i class="fas fa-laptop"></i>
                            </div>
                            <h3 class="example-title">Home Office</h3>
                            <p class="example-description">
                                Que ganham produtividade com mesas sob medida, prateleiras organizadas e ambiente ergonômico personalizado.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Citação -->
                <section class="quote-section">
                    <blockquote class="blockquote">
                        "Os móveis planejados não apenas decoram, eles transformam completamente a experiência de viver em um ambiente. Cada projeto sob medida é uma oportunidade de criar espaços que realmente funcionam para as pessoas que os habitam."
                    </blockquote>
                </section>

                <!-- Conclusão -->
                <section class="introduction">
                    <h2 class="section-title">O Resultado da Transformação</h2>
                    <p class="intro-text">
                        O contraste entre o antes e depois demonstra de forma clara e impactante como os móveis planejados podem revolucionar completamente a sua casa. Mais do que uma simples questão estética, eles representam um investimento em <span class="highlight">qualidade de vida, funcionalidade e valorização do imóvel</span>.
                    </p>
                    <p class="intro-text">
                        Cada projeto realizado pela <span class="highlight">LC Soluções em Móveis</span> é uma oportunidade de transformar espaços comuns em ambientes extraordinários, onde a beleza encontra a praticidade e o design se une à inteligência espacial.
                    </p>
                </section>

                <!-- CTA -->
                <section class="cta-section">
                    <h3 class="cta-title">Pronto para transformar seus ambientes?</h3>
                    <p class="cta-description">
                        Descubra como podemos criar o contraste perfeito entre o "antes" e "depois" na sua casa. Solicite um projeto personalizado e veja sua transformação começar.
                    </p>
                    <a href="/contato" class="cta-button">
                        Transforme seu Ambiente Agora
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; <span id="year"></span> LC Soluções em Móveis · Todos os direitos reservados</p>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>