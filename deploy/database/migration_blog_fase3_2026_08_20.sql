-- =============================================
-- MIGRAÇÃO: Blog Fases 3-4 - Autoridade + Cauda Longa
-- Data: 2026-08-20
-- Execute APÓS as migrações anteriores
-- =============================================

-- POST 18: O Que São Móveis Planejados?
SET @cat = (SELECT `id` FROM `categorias` WHERE `slug` = 'moveis-planejados' LIMIT 1);
INSERT INTO `posts` (`titulo`,`slug`,`conteudo`,`resumo`,`imagem`,`categoria`,`categoria_id`,`meta_title`,`meta_description`,`status`,`created_at`) VALUES (
'O Que São Móveis Planejados? Tudo o Que Você Precisa Saber',
'o-que-sao-moveis-planejados',
'<h2>Móveis planejados são projetados sob medida para um ambiente específico. Cada centímetro é pensado para aproveitar ao máximo o espaço disponível.</h2><p>Se você já pesquisou sobre reforma ou decoração, provavelmente se deparou com o termo móveis planejados. Mas o que isso significa exatamente?</p><p>Móveis planejados são projetados e fabricados especificamente para um ambiente. Diferente dos móveis prontos (medidas padrão), os planejados são feitos considerando as dimensões exatas do espaço e as necessidades do usuário.</p><p>O processo envolve: medição técnica, projeto 3D, aprovação do cliente, fabricação e instalação.</p><h2>Quais ambientes podem ter móveis planejados?</h2><p>Praticamente todos: cozinhas, quartos, salas, banheiros, home offices, lavanderias, áreas gourmet, escritórios e lojas.</p><h2>Qual a durabilidade?</h2><p>Móveis planejados em MDF de qualidade duram de 15 a 20 anos ou mais, dependendo do material, acabamento e manutenção.</p><h2>Vale a pena?</h2><p>Sim — especialmente para ambientes com dimensões específicas. Em Campo Grande, onde apartamentos compactos são comuns, os móveis planejados são quase sempre a melhor opção.</p><div class="blog-cta-box"><h3>Quer entender mais?</h3><p>A LC Soluções em Móveis pode explicar todo o processo. Fale conosco.</p><p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20saber%20mais%20sobre%20móveis%20planejados">Fale Conosco</a></p></div>',
'O que são móveis planejados? Entenda o processo, materiais, durabilidade e quando vale a pena investir.',
'/assets/img/blog/1778770228_IMG-20250512-WA0043.jpg',
'Móveis Planejados',@cat,
'O Que São Móveis Planejados? | Blog LC Soluções',
'Entenda o que são móveis planejados, como funcionam, quais materiais são usados e quando vale a pena.',
'publicado',NOW()
);

-- POST 19: Como Funciona a Fabricação
SET @cat = (SELECT `id` FROM `categorias` WHERE `slug` = 'guia-do-cliente' LIMIT 1);
INSERT INTO `posts` (`titulo`,`slug`,`conteudo`,`resumo`,`imagem`,`categoria`,`categoria_id`,`meta_title`,`meta_description`,`status`,`created_at`) VALUES (
'Como Funciona o Processo de Fabricação de um Móvel Planejado?',
'como-funciona-fabricacao-movel-planejado',
'<h2>Do primeiro contato à instalação, o processo de criação de um móvel planejado tem etapas bem definidas.</h2><p>Veja como funciona, passo a passo:</p><h2>1. Primeiro contato e levantamento de necessidades</h2><p>Você explica o que precisa, mostra o ambiente e a marcenaria entende suas necessidades.</p><h2>2. Medição técnica</h2><p>Um profissional vai até o local e realiza medições precisas de paredes, piso, teto, janelas e tomadas.</p><h2>3. Projeto 3D</h2><p>O designer cria o projeto em software 3D, mostrando como o móvel ficará no ambiente.</p><h2>4. Aprovação do cliente</h2><p>O projeto é apresentado ao cliente, que pode solicitar alterações antes da fabricação.</p><h2>5. Fabricação</h2><p>As peças passam por corte, furação, cola de borda, acabamento e montagem.</p><h2>6. Controle de qualidade</h2><p>Cada peça é verificada antes de sair da marcenaria.</p><h2>7. Transporte e instalação</h2><p>As peças são transportadas e instaladas no local pela equipe especializada.</p><h2>8. Inspeção e entrega</h2><p>Tudo é verificado e o cliente aprova antes de assinar o Termo de Entrega.</p><p>Em média, o processo leva de 30 a 45 dias úteis.</p><div class="blog-cta-box"><h3>Quer saber como funciona na prática?</h3><p>A LC Soluções em Móveis segue todas essas etapas com rigor técnico.</p><p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20saber%20como%20funciona%20o%20processo">Conhecer Nosso Processo</a></p></div>',
'Como funciona a fabricação de um móvel planejado? Da medição à instalação, entenda cada etapa.',
'/assets/img/blog/1778677051_IMG-20250507-WA0007.jpg',
'Guia do Cliente',@cat,
'Como Funciona a Fabricação de um Móvel Planejado? | Blog LC',
'Do primeiro contato à instalação, entenda cada etapa da fabricação de móveis planejados.',
'publicado',NOW()
);

-- POST 20: Quanto Tempo Demora?
SET @cat = (SELECT `id` FROM `categorias` WHERE `slug` = 'guia-do-cliente' LIMIT 1);
INSERT INTO `posts` (`titulo`,`slug`,`conteudo`,`resumo`,`imagem`,`categoria`,`categoria_id`,`meta_title`,`meta_description`,`status`,`created_at`) VALUES (
'Quanto Tempo Demora para Fazer Móveis Planejados?',
'quanto-tempo-demora-moveis-planejados',
'<h2>Prazo é uma das maiores dúvidas de quem vai fazer móveis planejados.</h2><h2>Fases do prazo</h2><h3>1. Medição + projeto (3 a 7 dias úteis)</h3><p>Após o primeiro contato, a marcenaria agenda a medição e cria o projeto 3D.</p><h3>2. Aprovação do cliente (1 a 5 dias)</h3><p>O tempo depende do cliente. Quanto mais rápido as aprovações, mais rápido a fabricação começa.</p><h3>3. Fabricação (15 a 30 dias úteis)</h3><p>Projeto pequeno: 10-15 dias. Médio: 15-25 dias. Grande: 20-30 dias.</p><h3>4. Transporte + instalação (1 a 3 dias)</h3><h2>Prazo total estimado</h2><p>Projeto simples: 20-25 dias úteis. Médio: 25-35 dias. Complexo: 35-45 dias.</p><h2>Como evitar atrasos?</h2><ul><li>Aprove as aprovações rapidamente</li><li>Não altere o projeto após a aprovação</li><li>Defina todos os materiais antes de iniciar</li></ul><div class="blog-cta-box"><h3>Precisa de um prazo definido?</h3><p>A LC Soluções em Móveis trabalha com prazos claros e realistas.</p><p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20saber%20o%20prazo%20para%20meu%20projeto">Solicitar Orçamento com Prazo</a></p></div>',
'Quanto tempo leva para fazer móveis planejados? Veja as etapas e prazos estimados por tipo de projeto.',
'/assets/img/blog/1778676740_IMG-20250507-WA0007.jpg',
'Guia do Cliente',@cat,
'Quanto Tempo Demora para Fazer Móveis Planejados? | Blog LC',
'Entenda quanto tempo leva cada etapa: medição, projeto, fabricação e instalação.',
'publicado',NOW()
);

-- POST 21: Quarto Planejado
SET @cat = (SELECT `id` FROM `categorias` WHERE `slug` = 'ambientes' LIMIT 1);
INSERT INTO `posts` (`titulo`,`slug`,`conteudo`,`resumo`,`imagem`,`categoria`,`categoria_id`,`meta_title`,`meta_description`,`status`,`created_at`) VALUES (
'Quarto Planejado: Como Criar um Ambiente Bonito e Funcional',
'quarto-planejado-ambiente-bonito-funcional',
'<h2>O quarto é o ambiente de descanso. Com móveis planejados, ele pode ter tudo o que você precisa sem parecer apertado.</h2><h2>Armário embutido</h2><p>Um armário que vai do chão ao teto aproveita toda a altura da parede. Divisórias internas para camisas, calças, vestidos e acessórios.</p><h2>Closet integrado</h2><p>Se o quarto tem espaço, um closet embutido ocupa uma parede inteira e oferece organização completa.</p><h2>Cama com storage</h2><p>Camas com gavetas debaixo do colchão são ideais para quartos pequenos. Guardam roupas de cama e mantas.</p><h2>Criados-mudo sob medida</h2><p>Gavetas extras, nichos para livros e espaço para carregadores. Tudo dimensionado para o espaço disponível.</p><h2>Quanto custa?</h2><p>Em Campo Grande, um quarto completo varia de R$ 8.000 a R$ 20.000.</p><div class="blog-cta-box"><h3>Quer planejar seu quarto?</h3><p>A LC Soluções em Móveis pode projetar seu quarto com móveis sob medida.</p><p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20projeto%20de%20quarto%20planejado">Solicitar Projeto de Quarto</a></p></div>',
'Quarto planejado: armário embutido, closet, cama com storage e dicas para cada tamanho de espaço.',
'/assets/img/quarto/quarto1.jpeg',
'Ambientes',@cat,
'Quarto Planejado: Como Criar um Ambiente Bonito e Funcional | Blog LC',
'Guia completo para planejar um quarto com móveis sob medida.',
'publicado',NOW()
);

-- POST 22: Sala e Painel de TV
SET @cat = (SELECT `id` FROM `categorias` WHERE `slug` = 'ambientes' LIMIT 1);
INSERT INTO `posts` (`titulo`,`slug`,`conteudo`,`resumo`,`imagem`,`categoria`,`categoria_id`,`meta_title`,`meta_description`,`status`,`created_at`) VALUES (
'Sala e Painel de TV Planejados: Guia Completo',
'sala-e-painel-tv-planejados-guia',
'<h2>A sala é o ambiente de convivência. Um painel de TV planejado transforma o visual e resolve a organização de eletrônicos.</h2><h2>O que um painel planejado pode ter?</h2><ul><li>Espaço para TV com dimensões exatas</li><li>Nichos para decoração</li><li>Armários para esconder equipamentos</li><li>Estantes para home theater</li><li>Gavetas para controles e acessórios</li><li>Canaleta para fios</li><li>Iluminação LED</li></ul><h2>Estilos de painel</h2><h3>Wall-to-wall</h3><p>Ocupa a parede inteira. Maximiza armazenamento.</p><h3>Compacto</h3><p>Apenas o espaço da TV e nichos ao redor. Para salas pequenas.</p><h3>Prateleiras flutuantes</h3><p>Visual minimalista. Para quem prefere mostrar objetos decorativos.</p><h2>Quanto custa?</h2><p>Em Campo Grande, um painel de TV planejado varia de R$ 3.000 a R$ 12.000.</p><div class="blog-cta-box"><h3>Quer um painel de TV planejado?</h3><p>A LC Soluções em Móveis pode projetar e instalar seu painel.</p><p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20painel%20de%20TV%20planejado">Solicitar Projeto de Painel</a></p></div>',
'Sala e painel de TV planejados: estilos, nichos, iluminação, integração e preços.',
'/assets/img/sala/painel1.jpeg',
'Ambientes',@cat,
'Sala e Painel de TV Planejados: Guia Completo | Blog LC',
'Guia completo para planejar sua sala com painel de TV sob medida.',
'publicado',NOW()
);
