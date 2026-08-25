<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- AdSense Code -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5253082939210672" crossorigin="anonymous"></script>
    <meta name="google-adsense-account" content="ca-pub-5253082939210672">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cozinha planejada pequena: 10 ideias inteligentes para aproveitar cada espaço | LC Soluções em Móveis</title>
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
                radial-gradient(circle at 30% 20%, rgba(197, 162, 83, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 70% 80%, rgba(197, 162, 83, 0.06) 0%, transparent 50%);
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

        /* Tips Section */
        .tips-section {
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

        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }

        .tip-card {
            background: linear-gradient(145deg, 
                rgba(26, 26, 26, 0.9), 
                rgba(10, 10, 10, 0.9));
            border-radius: 20px;
            padding: 35px 30px;
            border: 1px solid rgba(197, 162, 83, 0.1);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .tip-card::before {
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

        .tip-card:hover {
            transform: translateY(-8px);
            border-color: rgba(197, 162, 83, 0.3);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(197, 162, 83, 0.2),
                0 0 25px rgba(197, 162, 83, 0.1);
        }

        .tip-card:hover::before {
            opacity: 1;
        }

        .tip-number {
            position: absolute;
            top: -15px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
            box-shadow: 0 5px 15px rgba(197, 162, 83, 0.3);
        }

        .tip-icon {
            width: 60px;
            height: 60px;
            background: rgba(197, 162, 83, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--gold);
            font-size: 1.5rem;
            transition: all 0.4s ease;
        }

        .tip-card:hover .tip-icon {
            background: rgba(197, 162, 83, 0.2);
            transform: scale(1.1);
            color: var(--gold-light);
        }

        .tip-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tip-description {
            color: var(--text-light);
            line-height: 1.7;
            font-size: 1rem;
        }

        .tip-benefits {
            list-style: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(197, 162, 83, 0.1);
        }

        .tip-benefits li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .tip-benefits li::before {
            content: '→';
            color: var(--gold);
            font-weight: bold;
            margin-right: 10px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* Categories Section */
        .categories-section {
            margin: 80px 0;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }

        .category-card {
            background: linear-gradient(145deg, 
                rgba(26, 26, 26, 0.7), 
                rgba(10, 10, 10, 0.7));
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(197, 162, 83, 0.1);
            text-align: center;
            transition: all 0.4s ease;
        }

        .category-card:hover {
            border-color: rgba(197, 162, 83, 0.3);
            transform: translateY(-5px);
        }

        .category-icon {
            font-size: 2.2rem;
            color: var(--gold);
            margin-bottom: 15px;
        }

        .category-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 10px;
        }

        .category-count {
            font-size: 0.9rem;
            color: var(--text-light);
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
            
            .tips-grid {
                grid-template-columns: 1fr;
            }
            
            .tip-card {
                padding: 25px 20px;
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
            
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
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
            
            .categories-grid {
                grid-template-columns: 1fr;
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
            <h1 class="article-title">Cozinha planejada pequena: 10 ideias inteligentes para aproveitar cada espaço</h1>
            <p class="article-subtitle">Descubra soluções criativas para transformar sua cozinha compacta em um ambiente funcional e sofisticado</p>
            <div class="article-meta">
                <span><i class="fas fa-calendar-alt"></i> 23/09/2025</span>
                <div class="meta-divider"></div>
                <span><i class="fas fa-tag"></i> Ideias</span>
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
                        Ter uma cozinha pequena não significa abrir mão de conforto ou praticidade. Com móveis planejados, é possível aproveitar cada centímetro de forma estratégica. Veja <span class="highlight">10 ideias inteligentes</span> para transformar sua cozinha em um espaço funcional, moderno e incrivelmente prático.
                    </p>
                </section>

                <!-- Categorias de Soluções -->
                <section class="categories-section">
                    <h2 class="section-title">Soluções por Categoria</h2>
                    <div class="categories-grid">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-boxes-stacked"></i>
                            </div>
                            <h3 class="category-title">Armazenamento</h3>
                            <div class="category-count">4 soluções</div>
                        </div>
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-paint-roller"></i>
                            </div>
                            <h3 class="category-title">Design Visual</h3>
                            <div class="category-count">3 soluções</div>
                        </div>
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h3 class="category-title">Iluminação</h3>
                            <div class="category-count">1 solução</div>
                        </div>
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-cube"></i>
                            </div>
                            <h3 class="category-title">Móveis Inteligentes</h3>
                            <div class="category-count">2 soluções</div>
                        </div>
                    </div>
                </section>

                <!-- Dicas Principais -->
                <section class="tips-section">
                    <h2 class="section-title">10 Ideias Inteligentes para Cozinhas Pequenas</h2>
                    
                    <div class="tips-grid">
                        <!-- Dica 1 -->
                        <div class="tip-card">
                            <div class="tip-number">1</div>
                            <div class="tip-icon">
                                <i class="fas fa-arrows-up-to-line"></i>
                            </div>
                            <h3 class="tip-title">Armários até o teto</h3>
                            <p class="tip-description">
                                Aumentam significativamente o espaço de armazenamento e aproveitam áreas que normalmente ficam ociosas, eliminando espaços perdidos.
                            </p>
                            <ul class="tip-benefits">
                                <li>Maximiza capacidade de armazenamento</li>
                                <li>Elimina poeira em áreas superiores</li>
                                <li>Proporciona visual limpo e integrado</li>
                            </ul>
                        </div>

                        <!-- Dica 2 -->
                        <div class="tip-card">
                            <div class="tip-number">2</div>
                            <div class="tip-icon">
                                <i class="fas fa-shelves"></i>
                            </div>
                            <h3 class="tip-title">Prateleiras abertas</h3>
                            <p class="tip-description">
                                Ideais para itens de uso frequente, deixam o ambiente mais leve, funcional e com acesso rápido ao que realmente importa.
                            </p>
                            <ul class="tip-benefits">
                                <li>Acesso rápido a utensílios diários</li>
                                <li>Sensação de amplitude visual</li>
                                <li>Oportunidade para decoração</li>
                            </ul>
                        </div>

                        <!-- Dica 3 -->
                        <div class="tip-card">
                            <div class="tip-number">3</div>
                            <div class="tip-icon">
                                <i class="fas fa-drawer"></i>
                            </div>
                            <h3 class="tip-title">Gavetas organizadoras</h3>
                            <p class="tip-description">
                                Permitem separar talheres, panelas e utensílios de forma prática e eficiente, otimizando cada centímetro disponível.
                            </p>
                            <ul class="tip-benefits">
                                <li>Organização por categorias</li>
                                <li>Facilidade de acesso e limpeza</li>
                                <li>Proteção para utensílios</li>
                            </ul>
                        </div>

                        <!-- Dica 4 -->
                        <div class="tip-card">
                            <div class="tip-number">4</div>
                            <div class="tip-icon">
                                <i class="fas fa-palette"></i>
                            </div>
                            <h3 class="tip-title">Cores claras e neutras</h3>
                            <p class="tip-description">
                                Ampliam visualmente o ambiente e trazem sensação de amplitude, criando atmosfera leve e arejada mesmo em espaços reduzidos.
                            </p>
                            <ul class="tip-benefits">
                                <li>Ampliação visual do espaço</li>
                                <li>Reflexão máxima de luz</li>
                                <li>Ambiente mais iluminado</li>
                            </ul>
                        </div>

                        <!-- Dica 5 -->
                        <div class="tip-card">
                            <div class="tip-number">5</div>
                            <div class="tip-icon">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <h3 class="tip-title">Portas de correr</h3>
                            <p class="tip-description">
                                Economizam espaço valioso em locais reduzidos e facilitam o acesso, eliminando o problema de portas que atrapalham a circulação.
                            </p>
                            <ul class="tip-benefits">
                                <li>Economia de espaço de circulação</li>
                                <li>Funcionamento suave e prático</li>
                                <li>Design moderno e compacto</li>
                            </ul>
                        </div>

                        <!-- Dica 6 -->
                        <div class="tip-card">
                            <div class="tip-number">6</div>
                            <div class="tip-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h3 class="tip-title">Iluminação embutida</h3>
                            <p class="tip-description">
                                Sob os armários, valoriza bancadas e facilita o preparo das refeições, criando ambientes funcionais e aconchegantes.
                            </p>
                            <ul class="tip-benefits">
                                <li>Iluminação direta na bancada</li>
                                <li>Economia de energia</li>
                                <li>Ambiente mais aconchegante</li>
                            </ul>
                        </div>

                        <!-- Dica 7 -->
                        <div class="tip-card">
                            <div class="tip-number">7</div>
                            <div class="tip-icon">
                                <i class="fas fa-table"></i>
                            </div>
                            <h3 class="tip-title">Móveis retráteis</h3>
                            <p class="tip-description">
                                Como mesas que podem ser guardadas quando não usadas, oferecem flexibilidade máxima em espaços limitados.
                            </p>
                            <ul class="tip-benefits">
                                <li>Máxima flexibilidade de uso</li>
                                <li>Otimização temporária do espaço</li>
                                <li>Solução multifuncional</li>
                            </ul>
                        </div>

                        <!-- Dica 8 -->
                        <div class="tip-card">
                            <div class="tip-number">8</div>
                            <div class="tip-icon">
                                <i class="fas fa-mirror"></i>
                            </div>
                            <h3 class="tip-title">Espelhos e vidro</h3>
                            <p class="tip-description">
                                Aplicados em portas ou detalhes para dar sensação de profundidade e amplitude, criando ilusão de espaço expandido.
                            </p>
                            <ul class="tip-benefits">
                                <li>Ampliação visual imediata</li>
                                <li>Reflexão de luz natural</li>
                                <li>Toque de sofisticação</li>
                            </ul>
                        </div>

                        <!-- Dica 9 -->
                        <div class="tip-card">
                            <div class="tip-number">9</div>
                            <div class="tip-icon">
                                <i class="fas fa-refrigerator"></i>
                            </div>
                            <h3 class="tip-title">Eletrodomésticos compactos</h3>
                            <p class="tip-description">
                                Fogões e geladeiras menores ajudam a otimizar o espaço sem comprometer a funcionalidade do ambiente.
                            </p>
                            <ul class="tip-benefits">
                                <li>Otimização do espaço físico</li>
                                <li>Manutenção da funcionalidade</li>
                                <li>Economia de energia</li>
                            </ul>
                        </div>

                        <!-- Dica 10 -->
                        <div class="tip-card">
                            <div class="tip-number">10</div>
                            <div class="tip-icon">
                                <i class="fas fa-border-all"></i>
                            </div>
                            <h3 class="tip-title">Bancadas multifuncionais</h3>
                            <p class="tip-description">
                                Que servem como apoio para cozinhar e também como mesa de refeições, maximizando a utilidade de cada superfície.
                            </p>
                            <ul class="tip-benefits">
                                <li>Dupla funcionalidade</li>
                                <li>Economia de móveis</li>
                                <li>Otimização do fluxo de trabalho</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Citação -->
                <section class="quote-section">
                    <blockquote class="blockquote">
                        "Pequenos espaços podem se tornar grandes aliados quando bem planejados. Cada centímetro conta, e com as soluções certas, sua cozinha compacta pode superar até mesmo ambientes maiores em funcionalidade e estilo."
                    </blockquote>
                </section>

                <!-- Conclusão -->
                <section class="introduction">
                    <h2 class="section-title">Transformação Garantida</h2>
                    <p class="intro-text">
                        Com soluções inteligentes e bem planejadas, até mesmo a cozinha mais compacta pode ser transformada em um ambiente funcional, sofisticado e incrivelmente prático. O segredo está no <span class="highlight">aproveitamento inteligente de cada detalhe</span> e na escolha de soluções que realmente funcionam para o seu dia a dia.
                    </p>
                    <p class="intro-text">
                        Conte com a expertise da <span class="highlight">LC Soluções em Móveis</span> para desenvolver um projeto sob medida que aproveite cada centímetro do seu espaço, criando uma cozinha que une praticidade, beleza e eficiência em perfeita harmonia.
                    </p>
                </section>

                <!-- CTA -->
                <section class="cta-section">
                    <h3 class="cta-title">Pronto para transformar sua cozinha?</h3>
                    <p class="cta-description">
                        Nossa equipe especializada está pronta para criar soluções personalizadas que aproveitem cada centímetro do seu espaço. Solicite um projeto exclusivo e descubra o potencial da sua cozinha.
                    </p>
                    <a href="/contato" class="cta-button">
                        Peça seu Projeto Exclusivo
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