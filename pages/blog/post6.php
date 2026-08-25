<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- AdSense Code -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5253082939210672" crossorigin="anonymous"></script>
    <meta name="google-adsense-account" content="ca-pub-5253082939210672">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção de móveis planejados: dicas para aumentar a durabilidade | LC Soluções em Móveis</title>
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
                radial-gradient(circle at 25% 30%, rgba(197, 162, 83, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 75% 70%, rgba(197, 162, 83, 0.06) 0%, transparent 50%);
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

        /* Maintenance Tips Section */
        .maintenance-section {
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
            margin-bottom: 15px;
        }

        .tip-details {
            background: rgba(197, 162, 83, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            border-left: 3px solid var(--gold);
        }

        .do-list, .dont-list {
            list-style: none;
            margin-top: 10px;
        }

        .do-list li, .dont-list li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .do-list li::before {
            content: '✓';
            color: var(--gold);
            font-weight: bold;
            margin-right: 10px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .dont-list li::before {
            content: '✗';
            color: #ff6b6b;
            font-weight: bold;
            margin-right: 10px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .detail-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--gold-light);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Frequency Section */
        .frequency-section {
            margin: 80px 0;
        }

        .frequency-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }

        .frequency-card {
            background: linear-gradient(145deg, 
                rgba(26, 26, 26, 0.8), 
                rgba(10, 10, 10, 0.8));
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(197, 162, 83, 0.1);
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
        }

        .frequency-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            border-radius: 15px 15px 0 0;
        }

        .frequency-daily::before {
            background: linear-gradient(90deg, #4CAF50, #45a049);
        }

        .frequency-weekly::before {
            background: linear-gradient(90deg, #2196F3, #1976D2);
        }

        .frequency-monthly::before {
            background: linear-gradient(90deg, #FF9800, #F57C00);
        }

        .frequency-occasional::before {
            background: linear-gradient(90deg, #9C27B0, #7B1FA2);
        }

        .frequency-card:hover {
            border-color: rgba(197, 162, 83, 0.3);
            transform: translateY(-5px);
        }

        .frequency-icon {
            font-size: 2.2rem;
            margin-bottom: 15px;
        }

        .frequency-daily .frequency-icon { color: #4CAF50; }
        .frequency-weekly .frequency-icon { color: #2196F3; }
        .frequency-monthly .frequency-icon { color: #FF9800; }
        .frequency-occasional .frequency-icon { color: #9C27B0; }

        .frequency-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 10px;
        }

        .frequency-description {
            color: var(--text-light);
            font-size: 0.9rem;
            line-height: 1.5;
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
            
            .frequency-grid {
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
            
            .frequency-grid {
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
            <h1 class="article-title">Manutenção de móveis planejados: dicas para aumentar a durabilidade</h1>
            <p class="article-subtitle">Guia completo com práticas essenciais para conservar a beleza e funcionalidade dos seus móveis por muitos anos</p>
            <div class="article-meta">
                <span><i class="fas fa-calendar-alt"></i> 23/09/2025</span>
                <div class="meta-divider"></div>
                <span><i class="fas fa-tag"></i> Dicas</span>
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
                        Os móveis planejados são feitos para durar, mas para que mantenham sua <span class="highlight">beleza e funcionalidade</span> ao longo dos anos, é fundamental adotar alguns cuidados simples de manutenção. Confira as melhores práticas para conservar seu investimento sempre valorizado.
                    </p>
                </section>

                <!-- Dicas de Manutenção -->
                <section class="maintenance-section">
                    <h2 class="section-title">5 Práticas Essenciais de Manutenção</h2>
                    
                    <div class="tips-grid">
                        <!-- Dica 1 -->
                        <div class="tip-card">
                            <div class="tip-number">1</div>
                            <div class="tip-icon">
                                <i class="fas fa-broom"></i>
                            </div>
                            <h3 class="tip-title">Limpeza Correta e Regular</h3>
                            <p class="tip-description">
                                A limpeza adequada é fundamental para preservar o acabamento e evitar danos prematuros aos seus móveis.
                            </p>
                            <div class="tip-details">
                                <div class="detail-title">Recomendado:</div>
                                <ul class="do-list">
                                    <li>Pano macio e levemente umedecido</li>
                                    <li>Sabão neutro diluído em água</li>
                                    <li>Produtos específicos para MDF</li>
                                    <li>Secagem imediata com pano seco</li>
                                </ul>
                                <div class="detail-title">Evitar:</div>
                                <ul class="dont-list">
                                    <li>Produtos abrasivos e álcool em excesso</li>
                                    <li>Esponjas de aço ou materiais ásperos</li>
                                    <li>Água em excesso que penetre nas junções</li>
                                    <li>Limpeza a seco que arranha o acabamento</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Dica 2 -->
                        <div class="tip-card">
                            <div class="tip-number">2</div>
                            <div class="tip-icon">
                                <i class="fas fa-tint-slash"></i>
                            </div>
                            <h3 class="tip-title">Proteção Contra Umidade</h3>
                            <p class="tip-description">
                                A água é um dos maiores inimigos da madeira e do MDF. Proteção adequada previne danos irreversíveis.
                            </p>
                            <div class="tip-details">
                                <div class="detail-title">Recomendado:</div>
                                <ul class="do-list">
                                    <li>Acabamentos resistentes à umidade</li>
                                    <li>Ventilação adequada do ambiente</li>
                                    <li>Limpeza imediata de respingos</li>
                                    <li>Uso de desumidificadores se necessário</li>
                                </ul>
                                <div class="detail-title">Evitar:</div>
                                <ul class="dont-list">
                                    <li>Contato direto com água</li>
                                    <li>Ambientes com umidade excessiva</li>
                                    <li>Vazamentos não resolvidos</li>
                                    <li>Plantas com rega excessiva próximas</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Dica 3 -->
                        <div class="tip-card">
                            <div class="tip-number">3</div>
                            <div class="tip-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h3 class="tip-title">Cuidados com Ferragens</h3>
                            <p class="tip-description">
                                Dobradiças, corrediças e trilhos precisam de manutenção periódica para garantir funcionamento perfeito.
                            </p>
                            <div class="tip-details">
                                <div class="detail-title">Recomendado:</div>
                                <ul class="do-list">
                                    <li>Lubrificação periódica das partes móveis</li>
                                    <li>Verificação do aperto dos parafusos</li>
                                    <li>Limpeza de trilhos e corrediças</li>
                                    <li>Substituição preventiva quando necessário</li>
                                </ul>
                                <div class="detail-title">Evitar:</div>
                                <ul class="dont-list">
                                    <li>Forçar portas ou gavetas emperradas</li>
                                    <li>Uso de produtos corrosivos nas ferragens</li>
                                    <li>Ignorar ruídos ou dificuldades de abertura</li>
                                    <li>Sobrecarregar mecanismos</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Dica 4 -->
                        <div class="tip-card">
                            <div class="tip-number">4</div>
                            <div class="tip-icon">
                                <i class="fas fa-weight-hanging"></i>
                            </div>
                            <h3 class="tip-title">Evitar Sobrecarga</h3>
                            <p class="tip-description">
                                Respeitar os limites de peso é essencial para preservar a estrutura e evitar deformações.
                            </p>
                            <div class="tip-details">
                                <div class="detail-title">Recomendado:</div>
                                <ul class="do-list">
                                    <li>Distribuir peso uniformemente</li>
                                    <li>Respeitar limites indicados pelo fabricante</li>
                                    <li>Usar prateleiras reforçadas se necessário</li>
                                    <li>Organizar itens pesados na base</li>
                                </ul>
                                <div class="detail-title">Evitar:</div>
                                <ul class="dont-list">
                                    <li>Exceder capacidade das prateleiras</li>
                                    <li>Concentrar peso em um só ponto</li>
                                    <li>Armazenar líquidos em grandes quantidades</li>
                                    <li>Ignorar sinais de deformação</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Dica 5 -->
                        <div class="tip-card">
                            <div class="tip-number">5</div>
                            <div class="tip-icon">
                                <i class="fas fa-sun"></i>
                            </div>
                            <h3 class="tip-title">Proteção Contra Calor e Sol</h3>
                            <p class="tip-description">
                                Exposição excessiva ao calor e luz solar pode comprometer seriamente a durabilidade dos móveis.
                            </p>
                            <div class="tip-details">
                                <div class="detail-title">Recomendado:</div>
                                <ul class="do-list">
                                    <li>Proteção com cortinas ou persianas</li>
                                    <li>Distanciamento de fontes de calor</li>
                                    <li>Uso de protetores térmicos</li>
                                    <li>Acabamentos com proteção UV</li>
                                </ul>
                                <div class="detail-title">Evitar:</div>
                                <ul class="dont-list">
                                    <li>Exposição direta ao sol por longos períodos</li>
                                    <li>Proximidade com fogões e fornos</li>
                                    <li>Colocar objetos quentes diretamente</li>
                                    <li>Ignorar desbotamento progressivo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Frequência de Manutenção -->
                <section class="frequency-section">
                    <h2 class="section-title">Frequência Recomendada</h2>
                    
                    <div class="frequency-grid">
                        <div class="frequency-card frequency-daily">
                            <div class="frequency-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <h3 class="frequency-title">Diária</h3>
                            <p class="frequency-description">
                                Limpeza superficial com pano seco e remoção de resíduos imediatamente após o uso.
                            </p>
                        </div>

                        <div class="frequency-card frequency-weekly">
                            <div class="frequency-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <h3 class="frequency-title">Semanal</h3>
                            <p class="frequency-description">
                                Limpeza completa com produtos adequados e verificação de ferragens soltas.
                            </p>
                        </div>

                        <div class="frequency-card frequency-monthly">
                            <div class="frequency-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h3 class="frequency-title">Mensal</h3>
                            <p class="frequency-description">
                                Lubrificação de ferragens, verificação de estrutura e limpeza profunda.
                            </p>
                        </div>

                        <div class="frequency-card frequency-occasional">
                            <div class="frequency-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h3 class="frequency-title">Ocasional</h3>
                            <p class="frequency-description">
                                Ajustes profissionais, reparos e verificações técnicas especializadas.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Citação -->
                <section class="quote-section">
                    <blockquote class="blockquote">
                        "Com cuidados simples e regulares, seus móveis planejados permanecerão como novos por muitos anos. A manutenção preventiva é o melhor investimento para preservar seu patrimônio e garantir que cada peça continue cumprindo sua função com excelência."
                    </blockquote>
                </section>

                <!-- Conclusão -->
                <section class="introduction">
                    <h2 class="section-title">Investimento em Longevidade</h2>
                    <p class="intro-text">
                        Adotar boas práticas de manutenção não apenas garante maior durabilidade e beleza aos seus móveis planejados, mas também <span class="highlight">protege seu investimento</span> a longo prazo. Cada cuidado aplicado hoje se transforma em anos adicionais de uso e satisfação.
                    </p>
                    <p class="intro-text">
                        Quando precisar de reparos especializados, ajustes técnicos ou simplesmente orientação profissional, conte com o suporte especializado da <span class="highlight">LC Soluções em Móveis</span>. Nossa equipe está sempre disponível para garantir que seus móveis continuem perfeitos por muitos anos.
                    </p>
                </section>

                <!-- CTA -->
                <section class="cta-section">
                    <h3 class="cta-title">Precisa de ajuda com a manutenção?</h3>
                    <p class="cta-description">
                        Nossa equipe de especialistas está pronta para oferecer orientação personalizada, realizar reparos especializados e garantir que seus móveis planejados mantenham sempre o melhor desempenho.
                    </p>
                    <a href="/contato" class="cta-button">
                        Fale com Nossos Especialistas
                        <i class="fas fa-tools"></i>
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